<?php

namespace App\Services;

use App\Models\Document;
use App\Models\EmailLog;
use App\Models\EmailLogAttachment;
use App\Models\Admin;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * Stores CRM-sent emails to AWS S3 (full HTML snapshot + attachments).
 * When S3 is not configured, stores attachments on the local disk and still
 * creates email_log_attachments so the Emails tab can list and download them.
 */
class CrmSentEmailS3Service
{
    public const LOCAL_FILE_PATH_PREFIX = 'local:';

    /**
     * Store a CRM-sent email to S3 and create Document + EmailLogAttachment records.
     *
     * @param EmailLog $emailLog
     * @param string $subject
     * @param string $messageHtml
     * @param array $attachmentPaths Array of local file paths (or [path =>, name =>] tuples)
     * @return bool Success (true if S3 full archive succeeded, or if local-only archive succeeded; false on total failure)
     */
    public function storeToS3(EmailLog $emailLog, string $subject, string $messageHtml, array $attachmentPaths = []): bool
    {
        if (!$emailLog->client_id) {
            Log::warning('CrmSentEmailS3Service: No client_id, skipping storage');
            return false;
        }

        if ($this->isS3Configured()) {
            try {
                if ($this->storeFullEmailToS3($emailLog, $subject, $messageHtml, $attachmentPaths)) {
                    return true;
                }
            } catch (\Exception $e) {
                Log::error('CrmSentEmailS3Service: S3 full store failed', [
                    'email_log_id' => $emailLog->id,
                    'error' => $e->getMessage(),
                ]);
            }
            // S3 is expected in production — do not mirror the same files locally (avoids duplicate rows)
            return false;
        }

        // No S3: still persist copies so the CRM email list and downloads work
        return $this->storeArchiveLocally($emailLog, $attachmentPaths);
    }

    protected function isS3Configured(): bool
    {
        return (bool) (config('filesystems.disks.s3.key') && config('filesystems.disks.s3.bucket'));
    }

    /**
     * Public URL for a path on the S3 disk. Intelephense types Storage::disk() as the
     * generic Filesystem contract, which has no url(); FilesystemAdapter does.
     */
    protected function s3PublicUrl(string $path): string
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        return $disk->url($path);
    }

    /**
     * Original behaviour: S3 HTML snapshot + S3 attachment copies.
     */
    protected function storeFullEmailToS3(EmailLog $emailLog, string $subject, string $messageHtml, array $attachmentPaths = []): bool
    {
        $clientId = $emailLog->client_id;
        $admin = Admin::find($clientId);
        $clientUniqueId = ($admin && !empty($admin->client_id)) ? $admin->client_id : 'client_' . $clientId;
        $sanitizedClientId = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $clientUniqueId);

        $docType = 'crm_sent';
        $uniqueFileName = time() . '-' . substr(uniqid(), -6) . '-email.html';
        $s3Path = $sanitizedClientId . '/' . $docType . '/sent/' . $uniqueFileName;

        $htmlContent = $this->buildEmailHtml($emailLog, $subject, $messageHtml);

        $uploaded = Storage::disk('s3')->put($s3Path, $htmlContent);
        if (!$uploaded) {
            throw new \Exception('S3 put returned false for HTML');
        }
        $fileUrl = $this->s3PublicUrl($s3Path);

        $document = new Document();
        $document->file_name = 'email-' . $emailLog->id;
        $document->filetype = 'html';
        $document->user_id = $emailLog->user_id;
        $document->myfile = $fileUrl;
        $document->myfile_key = $uniqueFileName;
        $document->client_id = $clientId;
        $document->type = $emailLog->type ?? 'client';
        $document->mail_type = 'sent';
        $document->file_size = strlen($htmlContent);
        $document->doc_type = $docType;
        $document->client_matter_id = $emailLog->client_matter_id;
        $document->save();

        $emailLog->uploaded_doc_id = $document->id;
        $emailLog->save();

        foreach ($attachmentPaths as $pathOrTuple) {
            $filePath = is_array($pathOrTuple) ? ($pathOrTuple['path'] ?? null) : $pathOrTuple;
            $displayName = is_array($pathOrTuple) ? ($pathOrTuple['name'] ?? basename((string) $filePath)) : basename((string) $filePath);
            if ($filePath && file_exists($filePath)) {
                $this->uploadSingleAttachmentToS3($emailLog->id, $filePath, $displayName, $clientUniqueId);
            }
        }

        return true;
    }

    /**
     * When S3 is unavailable: copy attachments into storage/app and create email_log_attachments rows.
     * (Skipping a local "HTML email snapshot" document avoids non-public URLs; S3 path still provides that when configured.)
     */
    protected function storeArchiveLocally(EmailLog $emailLog, array $attachmentPaths = []): bool
    {
        try {
            $any = false;
            foreach ($attachmentPaths as $pathOrTuple) {
                $filePath = is_array($pathOrTuple) ? ($pathOrTuple['path'] ?? null) : $pathOrTuple;
                $displayName = is_array($pathOrTuple) ? ($pathOrTuple['name'] ?? basename((string) $filePath)) : basename((string) $filePath);
                if ($filePath && is_readable($filePath)) {
                    if ($this->createLocalEmailLogAttachment($emailLog->id, (string) $filePath, (string) $displayName)) {
                        $any = true;
                    }
                }
            }

            if (!$any && (empty($attachmentPaths))) {
                return true; // no files to store
            }

            return $any;
        } catch (\Exception $e) {
            Log::error('CrmSentEmailS3Service: local archive failed', [
                'email_log_id' => $emailLog->id ?? null,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Copy a file into storage/app and create an email_log_attachments row (no s3_key).
     */
    protected function createLocalEmailLogAttachment(int $emailLogId, string $sourcePath, string $displayName): bool
    {
        try {
            $content = @file_get_contents($sourcePath);
            if ($content === false) {
                Log::warning('CrmSentEmailS3Service: could not read source for local copy', ['path' => $sourcePath]);
                return false;
            }
            $sanitized = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $displayName);
            $sanitized = preg_replace('/_+/', '_', trim($sanitized, '_')) ?: 'attachment';
            $unique = time() . '_' . substr(uniqid(), -6) . '_';
            $rel = 'crm_composed_email_attachments/' . $emailLogId . '/' . $unique . $sanitized;

            if (!Storage::disk('local')->put($rel, $content)) {
                Log::warning('CrmSentEmailS3Service: local put failed', ['rel' => $rel]);
                return false;
            }

            $contentType = $this->guessContentType($displayName);
            EmailLogAttachment::create([
                'email_log_id' => $emailLogId,
                'filename' => $displayName,
                'display_name' => $displayName,
                'content_type' => $contentType,
                'file_path' => self::LOCAL_FILE_PATH_PREFIX . $rel,
                's3_key' => null,
                'file_size' => strlen($content),
                'is_inline' => false,
                'extension' => pathinfo($displayName, PATHINFO_EXTENSION),
            ]);
            return true;
        } catch (\Exception $e) {
            Log::warning('CrmSentEmailS3Service: createLocalEmailLogAttachment failed', [
                'path' => $sourcePath,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Build full HTML document for the email.
     */
    protected function buildEmailHtml(EmailLog $emailLog, string $subject, string $messageHtml): string
    {
        $from = htmlspecialchars($emailLog->from_mail ?? '');
        $to = htmlspecialchars($emailLog->to_mail ?? '');
        $date = $emailLog->created_at ? $emailLog->created_at->format('d/m/Y h:i a') : date('d/m/Y h:i a');
        $subjectEscaped = htmlspecialchars($subject);
        $body = $messageHtml;

        return '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' . $subjectEscaped . '</title></head><body>' .
            '<div style="font-family:Arial,sans-serif;max-width:800px;">' .
            '<p><strong>From:</strong> ' . $from . '</p>' .
            '<p><strong>To:</strong> ' . $to . '</p>' .
            '<p><strong>Date:</strong> ' . $date . '</p>' .
            '<p><strong>Subject:</strong> ' . $subjectEscaped . '</p>' .
            '<hr>' .
            '<div>' . $body . '</div>' .
            '</div></body></html>';
    }

    /**
     * Upload a single attachment to S3 and create EmailLogAttachment record.
     */
    protected function uploadSingleAttachmentToS3(int $emailLogId, string $localPath, string $displayName, string $clientUniqueId): void
    {
        try {
            $content = file_get_contents($localPath);
            if ($content === false) {
                Log::warning('CrmSentEmailS3Service: Could not read attachment', ['path' => $localPath]);
                return;
            }

            $sanitized = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $displayName);
            $sanitized = preg_replace('/_+/', '_', trim($sanitized, '_')) ?: 'attachment';
            $ext = pathinfo($displayName, PATHINFO_EXTENSION);
            $s3Key = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $clientUniqueId) . '/attachments/' . time() . '_' . substr(uniqid(), -6) . '_' . $sanitized . ($ext ? '.' . $ext : '');

            $uploaded = Storage::disk('s3')->put($s3Key, $content);
            $s3Path = $uploaded ? $this->s3PublicUrl($s3Key) : null;

            $contentType = $this->guessContentType($displayName);

            EmailLogAttachment::create([
                'email_log_id' => $emailLogId,
                'filename' => $displayName,
                'display_name' => $displayName,
                'content_type' => $contentType,
                'file_path' => $s3Path,
                's3_key' => $s3Key,
                'file_size' => strlen($content),
                'is_inline' => false,
                'extension' => pathinfo($displayName, PATHINFO_EXTENSION),
            ]);
        } catch (\Exception $e) {
            Log::warning('CrmSentEmailS3Service: Attachment upload failed', [
                'path' => $localPath,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function guessContentType(string $filename): string
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $map = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        return $map[$ext] ?? 'application/octet-stream';
    }

    /**
     * Safe absolute path for a locally stored sent-email attachment, or null.
     */
    public static function resolveLocalDiskAbsolutePath(?string $filePath): ?string
    {
        if ($filePath === null || $filePath === '') {
            return null;
        }
        if (strpos($filePath, self::LOCAL_FILE_PATH_PREFIX) !== 0) {
            return null;
        }
        $rel = substr($filePath, strlen(self::LOCAL_FILE_PATH_PREFIX));
        if ($rel === '' || strpos($rel, '..') !== false) {
            return null;
        }
        $abs = Storage::disk('local')->path($rel);
        $root = rtrim(Storage::disk('local')->path(''), DIRECTORY_SEPARATOR);
        $absReal = realpath($abs);
        $rootReal = realpath($root);
        if ($absReal === false || $rootReal === false) {
            if (is_readable($abs) && strncmp($abs, $root, strlen($root)) === 0) {
                return $abs;
            }
            return null;
        }
        if (strncmp($absReal, $rootReal, strlen($rootReal)) !== 0) {
            return null;
        }
        return is_readable($absReal) ? $absReal : null;
    }
}

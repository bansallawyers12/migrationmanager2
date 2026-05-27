<?php

namespace App\Console\Commands;

use App\Models\Document;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Retry uploading locally stored signed PDFs (storage/app/public/signed) to S3.
 * On success: update document links and remove the local file.
 * On failure: leave the file in place for a future run.
 */
class RetryLocalSignedDocumentsToS3 extends Command
{
    protected $signature = 'documents:retry-local-signed-to-s3
                            {--dry-run : Report actions without uploading or deleting}
                            {--id=* : Process only specific document id(s) from filenames}';

    protected $description = 'Upload existing local signed PDFs from storage/app/public/signed to S3 and remove them on success';

    private const SIGNED_DIR = 'signed';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $filterIds = $this->normalizeIds((array) $this->option('id'));

        if ($dryRun) {
            $this->warn('Dry run: no uploads, DB updates, or deletions will be performed.');
        }

        $files = $this->collectLocalSignedFiles($filterIds);

        if ($files === []) {
            $this->info('No local signed PDFs found in storage/app/public/signed.');
            return self::SUCCESS;
        }

        $this->info('Found ' . count($files) . ' local signed PDF(s) to process.');

        $stats = [
            'uploaded' => 0,
            'already_on_s3' => 0,
            'skipped' => 0,
            'failed' => 0,
            'deleted' => 0,
            'db_updated' => 0,
        ];

        foreach ($files as $relativePath) {
            $basename = basename($relativePath);
            $documentId = $this->parseDocumentIdFromFilename($basename);

            if ($documentId === null) {
                $this->warn("Skipping {$basename}: filename does not match {id}_signed.pdf.");
                $stats['skipped']++;
                continue;
            }

            $document = Document::find($documentId);
            if (!$document) {
                $this->warn("Skipping {$basename}: no document record for id {$documentId}.");
                $stats['skipped']++;
                continue;
            }

            $clientId = $this->resolveClientId($document);
            $docType = $this->resolveDocType($document);

            if ($clientId === null) {
                $this->warn("Skipping {$basename}: document {$documentId} has no resolvable client id.");
                $stats['skipped']++;
                continue;
            }

            $s3Key = $clientId . '/' . $docType . '/signed/' . $documentId . '_signed.pdf';
            $localFullPath = storage_path('app/public/' . $relativePath);

            if (!is_file($localFullPath) || !is_readable($localFullPath)) {
                $this->warn("Skipping {$basename}: local file missing or not readable.");
                $stats['skipped']++;
                continue;
            }

            try {
                /** @var \Illuminate\Filesystem\FilesystemAdapter $s3Disk */
                $s3Disk = Storage::disk('s3');
                $onS3 = $s3Disk->exists($s3Key);

                if ($onS3) {
                    $this->line("{$basename}: already on S3 ({$s3Key}).");
                    $stats['already_on_s3']++;
                } else {
                    if ($dryRun) {
                        $this->line("[dry-run] Would upload {$basename} to s3://{$s3Key}");
                    } else {
                        $stream = fopen($localFullPath, 'r');
                        if ($stream === false) {
                            throw new \RuntimeException('Unable to open local file for reading.');
                        }

                        try {
                            $uploaded = $s3Disk->put($s3Key, $stream);
                        } finally {
                            fclose($stream);
                        }

                        if (!$uploaded || !$s3Disk->exists($s3Key)) {
                            throw new \RuntimeException('S3 put completed but object was not found.');
                        }

                        $this->info("Uploaded {$basename} to s3://{$s3Key}");
                        $stats['uploaded']++;
                    }
                }

                if ($dryRun) {
                    if ($this->countDocumentsNeedingLinkUpdate($documentId, $relativePath) > 0) {
                        $this->line("[dry-run] Would update document link(s) for id {$documentId}.");
                    }
                    $this->line("[dry-run] Would delete local file {$relativePath}");
                    continue;
                }

                $s3Url = $s3Disk->url($s3Key);
                $updated = $this->updateDocumentLinks($documentId, $s3Url, $relativePath);
                $stats['db_updated'] += $updated;

                if (Storage::disk('public')->delete($relativePath)) {
                    $this->info("Removed local file {$relativePath}");
                    $stats['deleted']++;
                } else {
                    $this->warn("S3 OK but failed to delete local file {$relativePath}");
                }

                Log::info('Retry local signed PDF to S3 succeeded', [
                    'document_id' => $documentId,
                    's3_key' => $s3Key,
                    's3_url' => $s3Url,
                    'db_rows_updated' => $updated,
                ]);
            } catch (\Throwable $e) {
                $stats['failed']++;
                $this->error("Failed {$basename}: {$e->getMessage()}");
                Log::warning('Retry local signed PDF to S3 failed', [
                    'document_id' => $documentId,
                    's3_key' => $s3Key ?? null,
                    'local_path' => $relativePath,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info(sprintf(
            'Done. uploaded=%d, already_on_s3=%d, deleted=%d, db_updated=%d, skipped=%d, failed=%d',
            $stats['uploaded'],
            $stats['already_on_s3'],
            $stats['deleted'],
            $stats['db_updated'],
            $stats['skipped'],
            $stats['failed']
        ));

        return $stats['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @param  array<int|string>  $filterIds
     * @return list<string> Relative paths under the public disk, e.g. signed/123_signed.pdf
     */
    private function collectLocalSignedFiles(array $filterIds): array
    {
        if (!Storage::disk('public')->exists(self::SIGNED_DIR)) {
            return [];
        }

        $files = Storage::disk('public')->files(self::SIGNED_DIR);
        $pdfs = [];

        foreach ($files as $path) {
            if (!preg_match('/\.pdf$/i', $path)) {
                continue;
            }

            $documentId = $this->parseDocumentIdFromFilename(basename($path));
            if ($documentId === null) {
                $pdfs[] = $path;
                continue;
            }

            if ($filterIds !== [] && !in_array($documentId, $filterIds, true)) {
                continue;
            }

            $pdfs[] = $path;
        }

        sort($pdfs);

        return $pdfs;
    }

    /**
     * @param  array<int|string>  $raw
     * @return list<int>
     */
    private function normalizeIds(array $raw): array
    {
        $ids = [];
        foreach ($raw as $value) {
            foreach (explode(',', (string) $value) as $part) {
                $part = trim($part);
                if ($part !== '' && ctype_digit($part)) {
                    $ids[] = (int) $part;
                }
            }
        }

        return array_values(array_unique($ids));
    }

    private function parseDocumentIdFromFilename(string $filename): ?int
    {
        if (!preg_match('/^(\d+)_signed\.pdf$/i', $filename, $matches)) {
            return null;
        }

        $id = (int) $matches[1];

        return $id > 0 ? $id : null;
    }

    private function resolveClientId(Document $document): int|string|null
    {
        if (!$document->client_id) {
            return null;
        }

        $admin = DB::table('admins')
            ->select('client_id')
            ->where('id', '=', $document->client_id)
            ->first();

        if ($admin && $admin->client_id) {
            return $admin->client_id;
        }

        return $document->client_id;
    }

    private function resolveDocType(Document $document): string
    {
        $docType = trim((string) ($document->doc_type ?? ''));

        return $docType !== '' ? $docType : 'ad_hoc_documents';
    }

    private function linkPointsToLocalSignedFile(?string $link, int $documentId): bool
    {
        if ($link === null || $link === '') {
            return false;
        }

        return str_contains($link, self::SIGNED_DIR . '/' . $documentId . '_signed.pdf');
    }

    private function shouldUpdateSignedDocLink(?string $link, int $documentId): bool
    {
        if ($link === null || trim($link) === '') {
            return true;
        }

        return $this->linkPointsToLocalSignedFile($link, $documentId);
    }

    private function countDocumentsNeedingLinkUpdate(int $documentId, string $localRelativePath): int
    {
        $count = 0;
        $primary = Document::find($documentId);

        if ($primary && $this->shouldUpdateSignedDocLink($primary->signed_doc_link, $documentId)) {
            $count++;
        }

        $others = Document::query()
            ->where('id', '!=', $documentId)
            ->where(function ($query) use ($localRelativePath) {
                $query->where('signed_doc_link', 'like', '%' . $localRelativePath)
                    ->orWhere('myfile', 'like', '%' . $localRelativePath);
            })
            ->get();

        foreach ($others as $other) {
            if ($this->shouldUpdateSignedDocLink($other->signed_doc_link, $documentId)
                || $this->linkPointsToLocalSignedFile($other->myfile, $documentId)) {
                $count++;
            }
        }

        return $count;
    }

    private function updateDocumentLinks(int $documentId, string $s3Url, string $localRelativePath): int
    {
        $updated = 0;

        $primary = Document::find($documentId);
        if ($primary) {
            $changed = false;

            if ($this->shouldUpdateSignedDocLink($primary->signed_doc_link, $documentId)) {
                $primary->signed_doc_link = $s3Url;
                $changed = true;
            }

            if ($this->linkPointsToLocalSignedFile($primary->myfile, $documentId)) {
                $primary->myfile = $s3Url;
                $changed = true;
            }

            if ($changed) {
                $primary->save();
                $updated++;
            }
        }

        $others = Document::query()
            ->where('id', '!=', $documentId)
            ->where(function ($query) use ($localRelativePath) {
                $query->where('signed_doc_link', 'like', '%' . $localRelativePath)
                    ->orWhere('myfile', 'like', '%' . $localRelativePath);
            })
            ->get();

        foreach ($others as $other) {
            $changed = false;

            if ($this->shouldUpdateSignedDocLink($other->signed_doc_link, $documentId)) {
                $other->signed_doc_link = $s3Url;
                $changed = true;
            }

            if ($this->linkPointsToLocalSignedFile($other->myfile, $documentId)) {
                $other->myfile = $s3Url;
                $changed = true;
            }

            if ($changed) {
                $other->save();
                $updated++;
            }
        }

        return $updated;
    }
}

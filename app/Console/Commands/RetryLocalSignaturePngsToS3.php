<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\Signer;
use App\Services\SignedDocumentS3PathResolver;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Retry uploading locally stored signature PNGs (storage/app/public/signatures) to S3.
 * On success: update documents.signature_doc_link and remove the local file.
 * On failure: leave the file in place for a future run.
 */
class RetryLocalSignaturePngsToS3 extends Command
{
    protected $signature = 'signatures:retry-local-png-to-s3
                            {--dry-run : Report actions without uploading, updating DB, or deleting}
                            {--signer-id=* : Process only PNGs for specific signer id(s)}';

    protected $description = 'Upload existing local signature PNGs from storage/app/public/signatures to S3 and remove them on success';

    private const SIGNATURES_DIR = 'signatures';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $filterSignerIds = $this->normalizeIds((array) $this->option('signer-id'));

        if ($dryRun) {
            $this->warn('Dry run: no uploads, DB updates, or deletions will be performed.');
        }

        $files = $this->collectLocalSignaturePngs($filterSignerIds);

        if ($files === []) {
            $this->info('No local signature PNGs found in storage/app/public/signatures.');
            return self::SUCCESS;
        }

        $this->info('Found ' . count($files) . ' local signature PNG(s) to process.');

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
            $parsed = $this->parseFilename($basename);

            if ($parsed === null) {
                $this->warn("Skipping {$basename}: filename does not match {signer_id}_field_{field_id}_{hash}.png.");
                $stats['skipped']++;
                continue;
            }

            ['signer_id' => $signerId, 'field_id' => $fieldId] = $parsed;

            $signer = Signer::find($signerId);
            if (!$signer) {
                $this->warn("Skipping {$basename}: no signer record for id {$signerId}.");
                $stats['skipped']++;
                continue;
            }

            $document = $signer->document;
            if (!$document) {
                $this->warn("Skipping {$basename}: signer {$signerId} has no document.");
                $stats['skipped']++;
                continue;
            }

            $s3Key = SignedDocumentS3PathResolver::resolveSignaturePngS3Key($document, $basename);
            if ($s3Key === null) {
                $this->warn("Skipping {$basename}: document {$document->id} has no resolvable S3 path.");
                $stats['skipped']++;
                continue;
            }

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
                    $wouldUpdate = $this->countDocumentsNeedingLinkUpdate($document->id, $fieldId, $basename);
                    if ($wouldUpdate > 0) {
                        $this->line("[dry-run] Would update signature_doc_link on {$wouldUpdate} document(s) for field {$fieldId}.");
                    }
                    $this->line("[dry-run] Would delete local file {$relativePath}");
                    continue;
                }

                $s3Url = $s3Disk->url($s3Key);
                $updated = $this->updateSignatureDocLinks($document->id, $fieldId, $basename, $s3Url);
                $stats['db_updated'] += $updated;

                if (Storage::disk('public')->delete($relativePath)) {
                    $this->info("Removed local file {$relativePath}");
                    $stats['deleted']++;
                } else {
                    $this->warn("S3 OK but failed to delete local file {$relativePath}");
                }

                Log::info('Retry local signature PNG to S3 succeeded', [
                    'document_id' => $document->id,
                    'signer_id' => $signerId,
                    'field_id' => $fieldId,
                    's3_key' => $s3Key,
                    's3_url' => $s3Url,
                    'db_rows_updated' => $updated,
                ]);
            } catch (\Throwable $e) {
                $stats['failed']++;
                $this->error("Failed {$basename}: {$e->getMessage()}");
                Log::warning('Retry local signature PNG to S3 failed', [
                    'document_id' => $document->id,
                    'signer_id' => $signerId,
                    'field_id' => $fieldId,
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
     * @param  array<int|string>  $filterSignerIds
     * @return list<string> Relative paths under the public disk, e.g. signatures/123_field_456_abc.png
     */
    private function collectLocalSignaturePngs(array $filterSignerIds): array
    {
        if (!Storage::disk('public')->exists(self::SIGNATURES_DIR)) {
            return [];
        }

        $files = Storage::disk('public')->files(self::SIGNATURES_DIR);
        $pngs = [];

        foreach ($files as $path) {
            if (!preg_match('/\.png$/i', $path)) {
                continue;
            }

            $parsed = $this->parseFilename(basename($path));
            if ($parsed === null) {
                $pngs[] = $path;
                continue;
            }

            if ($filterSignerIds !== [] && !in_array($parsed['signer_id'], $filterSignerIds, true)) {
                continue;
            }

            $pngs[] = $path;
        }

        sort($pngs);

        return $pngs;
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

    /**
     * @return array{signer_id: int, field_id: int}|null
     */
    private function parseFilename(string $filename): ?array
    {
        if (!preg_match('/^(\d+)_field_(\d+)_[a-f0-9]+\.png$/i', $filename, $matches)) {
            return null;
        }

        $signerId = (int) $matches[1];
        $fieldId = (int) $matches[2];

        if ($signerId <= 0 || $fieldId <= 0) {
            return null;
        }

        return [
            'signer_id' => $signerId,
            'field_id' => $fieldId,
        ];
    }

    private function isLocalPublicSignatureUrl(string $url, string $basename): bool
    {
        if (str_contains($url, self::SIGNATURES_DIR . '/' . $basename)) {
            return true;
        }

        return str_contains($url, '/storage/' . self::SIGNATURES_DIR . '/' . $basename);
    }

    /**
     * Post-signing format: {"7415":"https://.../storage/signatures/6027_field_7415_....png"}
     * Pre-signing format: [{"email":"...","name":"...","url":"..."}] — never modified.
     */
    private function signatureDocLinkNeedsUpdate(?string $signatureDocLink, int $fieldId, string $basename): bool
    {
        if ($signatureDocLink === null || trim($signatureDocLink) === '') {
            return false;
        }

        $decoded = json_decode($signatureDocLink, true);
        if (!is_array($decoded) || array_is_list($decoded)) {
            return false;
        }

        foreach ([(string) $fieldId, $fieldId] as $key) {
            if (!array_key_exists($key, $decoded)) {
                continue;
            }

            $value = $decoded[$key];
            if (is_string($value) && $this->isLocalPublicSignatureUrl($value, $basename)) {
                return true;
            }
        }

        foreach ($decoded as $value) {
            if (is_string($value) && $this->isLocalPublicSignatureUrl($value, $basename)) {
                return true;
            }
        }

        return false;
    }

    private function applySignatureDocLinkUpdate(string $signatureDocLink, string $basename, string $s3Url): ?string
    {
        $decoded = json_decode($signatureDocLink, true);
        if (!is_array($decoded) || array_is_list($decoded)) {
            return null;
        }

        $changed = false;

        foreach ($decoded as $key => $value) {
            if (!is_string($value) || !$this->isLocalPublicSignatureUrl($value, $basename)) {
                continue;
            }

            $decoded[$key] = $s3Url;
            $changed = true;
        }

        if (!$changed) {
            return null;
        }

        return json_encode($decoded);
    }

    private function countDocumentsNeedingLinkUpdate(int $documentId, int $fieldId, string $basename): int
    {
        $count = 0;

        $primary = Document::find($documentId);
        if ($primary && $this->signatureDocLinkNeedsUpdate($primary->signature_doc_link, $fieldId, $basename)) {
            $count++;
        }

        $others = Document::query()
            ->where('id', '!=', $documentId)
            ->where('signature_doc_link', 'like', '%' . self::SIGNATURES_DIR . '/' . $basename . '%')
            ->get();

        foreach ($others as $other) {
            if ($this->signatureDocLinkNeedsUpdate($other->signature_doc_link, $fieldId, $basename)) {
                $count++;
            }
        }

        return $count;
    }

    private function updateSignatureDocLinks(int $documentId, int $fieldId, string $basename, string $s3Url): int
    {
        $updated = 0;

        $primary = Document::find($documentId);
        if ($primary) {
            $newLink = $this->applySignatureDocLinkUpdate(
                (string) $primary->signature_doc_link,
                $basename,
                $s3Url
            );

            if ($newLink !== null) {
                $primary->signature_doc_link = $newLink;
                $primary->save();
                $updated++;
            }
        }

        $others = Document::query()
            ->where('id', '!=', $documentId)
            ->where('signature_doc_link', 'like', '%' . self::SIGNATURES_DIR . '/' . $basename . '%')
            ->get();

        foreach ($others as $other) {
            $newLink = $this->applySignatureDocLinkUpdate(
                (string) $other->signature_doc_link,
                $basename,
                $s3Url
            );

            if ($newLink !== null) {
                $other->signature_doc_link = $newLink;
                $other->save();
                $updated++;
            }
        }

        return $updated;
    }
}

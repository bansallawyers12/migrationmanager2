<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Retry uploading locally stored visa agreement DOCX files (storage/app/public/agreements) to S3.
 * Matches ClientsController::generateagreement S3 layout: {client_id}/cost_assignment_form/agreement_{client_id}_{time}.docx
 * On success: remove the local file. On failure: leave for the next scheduled run.
 */
class RetryLocalAgreementsToS3 extends Command
{
    protected $signature = 'agreements:retry-local-to-s3
                            {--dry-run : Report actions without uploading or deleting}';

    protected $description = 'Upload local visa agreement DOCX files from storage/app/public/agreements to S3 and remove them on success';

    private const AGREEMENTS_DIR = 'agreements';

    private const FILENAME_PATTERN = '/^agreement_(.+)\.docx$/i';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Dry run: no uploads or deletions will be performed.');
        }

        $public = Storage::disk('public');

        if (!$public->exists(self::AGREEMENTS_DIR)) {
            $this->info('No local agreements directory found.');

            return self::SUCCESS;
        }

        $files = $public->files(self::AGREEMENTS_DIR);
        $docxFiles = array_values(array_filter($files, fn ($path) => str_ends_with(strtolower($path), '.docx')));

        if ($docxFiles === []) {
            $this->info('No local agreement DOCX files found.');

            return self::SUCCESS;
        }

        $this->info('Found ' . count($docxFiles) . ' local agreement DOCX file(s) to process.');

        $stats = [
            'uploaded' => 0,
            'already_on_s3' => 0,
            'skipped' => 0,
            'failed' => 0,
            'deleted' => 0,
        ];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $s3Disk */
        $s3Disk = Storage::disk('s3');

        foreach ($docxFiles as $relativePath) {
            $basename = basename($relativePath);
            $clientId = $this->parseClientIdFromFilename($basename);

            if ($clientId === null) {
                $this->warn("Skipping {$basename}: filename does not match agreement_{client_id}.docx.");
                $stats['skipped']++;
                continue;
            }

            $localFullPath = storage_path('app/public/' . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $relativePath));

            if (!is_file($localFullPath) || !is_readable($localFullPath)) {
                $this->warn("Skipping {$basename}: local file missing or not readable.");
                $stats['skipped']++;
                continue;
            }

            try {
                $prefix = $clientId . '/cost_assignment_form/';
                $existingOnS3 = $this->findExistingAgreementOnS3($s3Disk, $prefix, $clientId);

                if ($existingOnS3 !== null) {
                    $this->line("{$basename}: already on S3 ({$existingOnS3}).");
                    $stats['already_on_s3']++;

                    if ($dryRun) {
                        $this->line("[dry-run] Would delete local file {$relativePath}");
                        continue;
                    }

                    if ($public->delete($relativePath)) {
                        $this->info("Removed local file {$relativePath}");
                        $stats['deleted']++;
                    } else {
                        $this->warn("S3 object exists but failed to delete local file {$relativePath}");
                    }

                    continue;
                }

                $fileName = 'agreement_' . $clientId . '_' . time() . '.docx';
                $s3Path = $prefix . $fileName;

                if ($dryRun) {
                    $this->line("[dry-run] Would upload {$basename} to s3://{$s3Path}");
                    $this->line("[dry-run] Would delete local file {$relativePath}");
                    continue;
                }

                $stream = fopen($localFullPath, 'r');
                if ($stream === false) {
                    throw new \RuntimeException('Unable to open local file for reading.');
                }

                try {
                    $uploaded = $s3Disk->put($s3Path, $stream);
                } finally {
                    fclose($stream);
                }

                if (!$uploaded || !$s3Disk->exists($s3Path)) {
                    throw new \RuntimeException('S3 put completed but object was not found.');
                }

                $downloadUrl = $s3Disk->url($s3Path);
                if (empty($downloadUrl)) {
                    throw new \RuntimeException('S3 upload succeeded but URL is empty.');
                }

                $this->info("Uploaded {$basename} to s3://{$s3Path}");
                $stats['uploaded']++;

                if ($public->delete($relativePath)) {
                    $this->info("Removed local file {$relativePath}");
                    $stats['deleted']++;
                } else {
                    $this->warn("S3 OK but failed to delete local file {$relativePath}");
                }

                Log::info('Retry local agreement DOCX to S3 succeeded', [
                    'client_id' => $clientId,
                    's3_key' => $s3Path,
                    's3_url' => $downloadUrl,
                    'local_path' => $relativePath,
                ]);
            } catch (\Throwable $e) {
                $stats['failed']++;
                $this->error("Failed {$basename}: {$e->getMessage()}");
                Log::warning('Retry local agreement DOCX to S3 failed', [
                    'client_id' => $clientId,
                    'local_path' => $relativePath,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            collect($stats)->map(fn ($v, $k) => [$k, $v])->values()->all()
        );

        return $stats['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function parseClientIdFromFilename(string $basename): ?string
    {
        if (!preg_match(self::FILENAME_PATTERN, $basename, $matches)) {
            return null;
        }

        $clientId = trim($matches[1]);

        return $clientId !== '' ? $clientId : null;
    }

    /**
     * If a prior generateagreement run uploaded to S3 but left the local copy, avoid duplicate uploads.
     */
    private function findExistingAgreementOnS3($s3Disk, string $prefix, string $clientId): ?string
    {
        try {
            $files = $s3Disk->files($prefix);
            $needle = 'agreement_' . $clientId;

            foreach ($files as $key) {
                if (str_contains(basename($key), $needle) && str_ends_with(strtolower($key), '.docx')) {
                    return $key;
                }
            }
        } catch (\Throwable $e) {
            Log::debug('Could not list S3 cost_assignment_form prefix', [
                'prefix' => $prefix,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }
}

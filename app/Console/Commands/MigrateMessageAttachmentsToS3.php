<?php

namespace App\Console\Commands;

use App\Services\MessageAttachmentStorageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Upload existing Client Portal message attachments from storage/app/public to S3.
 * Uses the same relative paths as stored in message_attachments.path.
 */
class MigrateMessageAttachmentsToS3 extends Command
{
    protected $signature = 'message-attachments:migrate-local-to-s3
                            {--dry-run : Report actions without uploading or deleting}
                            {--keep-local : Keep local copies after successful S3 upload}
                            {--scan-disk : Also upload files under message-attachments/ not linked in DB}';

    protected $description = 'Migrate Client Portal message attachments from local public storage to S3';

    public function handle(MessageAttachmentStorageService $storage): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $keepLocal = (bool) $this->option('keep-local');
        $deleteLocalAfter = !$keepLocal;

        if ($dryRun) {
            $this->warn('Dry run: no uploads or deletions will be performed.');
        }

        if (!Schema::hasTable('message_attachments')) {
            $this->error('Table message_attachments does not exist.');

            return self::FAILURE;
        }

        $stats = [
            'rows' => 0,
            'uploaded' => 0,
            'already_on_s3' => 0,
            'skipped' => 0,
            'not_found' => 0,
            'failed' => 0,
            'disk_orphans_uploaded' => 0,
        ];

        $rows = DB::table('message_attachments')
            ->orderBy('id')
            ->get(['id', 'path', 'original_filename']);

        $stats['rows'] = $rows->count();
        $this->info("Processing {$stats['rows']} message_attachments row(s).");

        $knownPaths = [];

        foreach ($rows as $row) {
            $path = trim((string) ($row->path ?? ''));
            if ($path === '') {
                $this->warn("Attachment id {$row->id}: empty path, skipping.");
                $stats['skipped']++;
                continue;
            }

            $knownPaths[$path] = true;

            if ($dryRun) {
                $onS3 = Storage::disk(MessageAttachmentStorageService::DISK_S3)->exists($path);
                $onLocal = Storage::disk(MessageAttachmentStorageService::DISK_PUBLIC)->exists($path);
                if ($onS3) {
                    $this->line("id {$row->id}: already on S3 ({$path})");
                    $stats['already_on_s3']++;
                } elseif ($onLocal) {
                    $this->line("[dry-run] Would upload id {$row->id} to s3://{$path}");
                } else {
                    $this->warn("id {$row->id}: file missing locally and on S3 ({$path})");
                    $stats['not_found']++;
                }
                continue;
            }

            $result = $storage->migrateLocalPathToS3($path, $deleteLocalAfter);

            if (!$result['success']) {
                $this->warn("id {$row->id}: {$result['message']} ({$path})");
                if (str_contains($result['message'], 'Not found on local')) {
                    $stats['not_found']++;
                } else {
                    $stats['failed']++;
                }
                continue;
            }

            if ($result['uploaded']) {
                $label = $row->original_filename ?? $path;
                $this->info("Uploaded id {$row->id}: {$label}");
                $stats['uploaded']++;
            } else {
                $stats['already_on_s3']++;
            }
        }

        if ($this->option('scan-disk')) {
            $this->info('Scanning local message-attachments/ directory for files not in DB...');
            $public = Storage::disk(MessageAttachmentStorageService::DISK_PUBLIC);
            $files = $public->allFiles(MessageAttachmentStorageService::BASE_PREFIX);

            foreach ($files as $path) {
                if (isset($knownPaths[$path])) {
                    continue;
                }

                if ($dryRun) {
                    if (!Storage::disk(MessageAttachmentStorageService::DISK_S3)->exists($path)) {
                        $this->line("[dry-run] Would upload orphan file to s3://{$path}");
                        $stats['disk_orphans_uploaded']++;
                    }
                    continue;
                }

                $result = $storage->migrateLocalPathToS3($path, $deleteLocalAfter);
                if ($result['success'] && $result['uploaded']) {
                    $this->info("Uploaded orphan: {$path}");
                    $stats['disk_orphans_uploaded']++;
                } elseif ($result['success']) {
                    $stats['already_on_s3']++;
                }
            }
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            collect($stats)->map(fn ($v, $k) => [$k, $v])->values()->all()
        );

        return $stats['failed'] > 0 ? self::FAILURE : self::SUCCESS; // not_found does not fail the command
    }
}

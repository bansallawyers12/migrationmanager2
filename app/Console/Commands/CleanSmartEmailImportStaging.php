<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Prune Smart Email Import staging folders older than 24 hours.
 *
 * The controller also prunes on every analyze call, but this command ensures
 * orphaned batches (e.g. from abandoned sessions) are cleaned up daily.
 *
 * Schedule: daily (see App\Console\Kernel)
 */
class CleanSmartEmailImportStaging extends Command
{
    protected $signature   = 'smart-email-import:clean {--hours=24 : Delete batches older than this many hours}';
    protected $description = 'Remove expired Smart Email Import staging folders from local storage';

    public function handle(): int
    {
        $hours      = (int) $this->option('hours');
        $cutoff     = now()->subHours($hours)->timestamp;
        $disk       = Storage::disk('local');
        $rootDir    = 'smart-email-import';
        $deleted    = 0;
        $errors     = 0;

        if (! $disk->exists($rootDir)) {
            $this->info('No staging directory found — nothing to clean.');
            return self::SUCCESS;
        }

        // Each sub-directory is a user_id
        foreach ($disk->directories($rootDir) as $userDir) {
            foreach ($disk->directories($userDir) as $batchDir) {
                try {
                    $metaPath = "{$batchDir}/meta.json";
                    $modified = $disk->exists($metaPath)
                        ? $disk->lastModified($metaPath)
                        : $disk->lastModified($batchDir);

                    if ($modified < $cutoff) {
                        $disk->deleteDirectory($batchDir);
                        $deleted++;
                        Log::info('CleanSmartEmailImportStaging: deleted', ['dir' => $batchDir]);
                    }
                } catch (\Exception $e) {
                    $errors++;
                    Log::warning('CleanSmartEmailImportStaging: failed to delete', [
                        'dir'   => $batchDir,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Remove empty user dir
            try {
                if (empty($disk->allFiles($userDir))) {
                    $disk->deleteDirectory($userDir);
                }
            } catch (\Exception $e) {
                // non-critical
            }
        }

        $this->info("Smart Email Import staging clean complete: {$deleted} batch(es) deleted, {$errors} error(s).");
        return self::SUCCESS;
    }
}

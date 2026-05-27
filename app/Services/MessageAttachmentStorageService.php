<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Client Portal message attachments: message-attachments/{Y}/{m}/{filename}
 * New files go to S3; reads fall back to local public disk until migrated.
 */
class MessageAttachmentStorageService
{
    public const DISK_S3 = 's3';

    public const DISK_PUBLIC = 'public';

    public const BASE_PREFIX = 'message-attachments';

    /**
     * Store an uploaded attachment on S3 under message-attachments/Y/m/.
     */
    public function storeUploadedFile(UploadedFile $file, string $safeName): ?string
    {
        try {
            $path = $file->storeAs(
                self::BASE_PREFIX . '/' . date('Y/m'),
                $safeName,
                self::DISK_S3
            );

            if (!$path || !Storage::disk(self::DISK_S3)->exists($path)) {
                throw new \RuntimeException('S3 store completed but object was not found.');
            }

            return $path;
        } catch (\Throwable $e) {
            Log::error('Failed to store message attachment on S3', [
                'safe_name' => $safeName,
                'original_name' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function exists(string $path): bool
    {
        if ($path === '') {
            return false;
        }

        return $this->resolveReadableDisk($path) !== null;
    }

    /**
     * Prefer S3, then local public (pre-migration files).
     */
    public function resolveReadableDisk(string $path): ?string
    {
        if ($path === '') {
            return null;
        }

        if (Storage::disk(self::DISK_S3)->exists($path)) {
            return self::DISK_S3;
        }

        if (Storage::disk(self::DISK_PUBLIC)->exists($path)) {
            return self::DISK_PUBLIC;
        }

        return null;
    }

    public function get(string $path): ?string
    {
        $disk = $this->resolveReadableDisk($path);

        if ($disk === null) {
            return null;
        }

        try {
            return Storage::disk($disk)->get($path);
        } catch (\Throwable $e) {
            Log::error('Failed to read message attachment', [
                'path' => $path,
                'disk' => $disk,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Copy a local public-disk file to S3 at the same relative path.
     *
     * @return array{success: bool, uploaded: bool, message: string}
     */
    public function migrateLocalPathToS3(string $path, bool $deleteLocalAfter = true): array
    {
        if ($path === '') {
            return ['success' => false, 'uploaded' => false, 'message' => 'Empty path.'];
        }

        $s3 = Storage::disk(self::DISK_S3);
        $public = Storage::disk(self::DISK_PUBLIC);

        if ($s3->exists($path)) {
            if ($deleteLocalAfter && $public->exists($path)) {
                $public->delete($path);
            }

            return ['success' => true, 'uploaded' => false, 'message' => 'Already on S3.'];
        }

        if (!$public->exists($path)) {
            return ['success' => false, 'uploaded' => false, 'message' => 'Not found on local public disk.'];
        }

        try {
            $localFullPath = storage_path('app/public/' . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path));
            if (!is_file($localFullPath) || !is_readable($localFullPath)) {
                return ['success' => false, 'uploaded' => false, 'message' => 'Local file missing or not readable.'];
            }

            $stream = fopen($localFullPath, 'r');
            if ($stream === false) {
                throw new \RuntimeException('Unable to open local file for reading.');
            }

            try {
                $uploaded = $s3->put($path, $stream);
            } finally {
                fclose($stream);
            }

            if (!$uploaded || !$s3->exists($path)) {
                throw new \RuntimeException('S3 put completed but object was not found.');
            }

            if ($deleteLocalAfter) {
                $public->delete($path);
            }

            return ['success' => true, 'uploaded' => true, 'message' => 'Uploaded to S3.'];
        } catch (\Throwable $e) {
            Log::error('Failed to migrate message attachment to S3', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'uploaded' => false, 'message' => $e->getMessage()];
        }
    }
}

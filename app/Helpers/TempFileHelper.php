<?php

namespace App\Helpers;

class TempFileHelper
{
    /**
     * Whether the path is a disposable temp file under storage/app (tmp_* prefix).
     */
    public static function isDeletableAppTempFile(?string $path): bool
    {
        if ($path === null || $path === '') {
            return false;
        }

        $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $appDir = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, storage_path('app')), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!str_starts_with($normalized, $appDir)) {
            return false;
        }

        return str_starts_with(basename($normalized), 'tmp_');
    }

    public static function delete(?string $path): void
    {
        if (self::isDeletableAppTempFile($path) && is_file($path)) {
            @unlink($path);
        }
    }

    /**
     * @param array<int, string|null> $paths
     */
    public static function deleteMany(array $paths): void
    {
        foreach ($paths as $path) {
            self::delete($path);
        }
    }
}

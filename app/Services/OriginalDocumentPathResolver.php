<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Resolves the original (unsigned) upload stored in myfile / myfile_key.
 *
 * Never consults signed_doc_link or the /signed/ artifact path.
 */
class OriginalDocumentPathResolver
{
    /**
     * @return array{disk: 'local', path: string}|array{disk: 's3', key: string}|null
     */
    public static function locateOriginalPdfFile(Document $document): ?array
    {
        $myfile = trim((string) ($document->myfile ?? ''));

        if ($myfile !== '' && filter_var($myfile, FILTER_VALIDATE_URL) && str_contains($myfile, 's3')) {
            $fromUrl = self::locateOnS3FromUrl($myfile);
            if ($fromUrl !== null) {
                return $fromUrl;
            }
        }

        if ($myfile !== '') {
            $localPath = self::resolveLocalPublicPath($myfile);
            if ($localPath !== null) {
                return ['disk' => 'local', 'path' => $localPath];
            }
        }

        if ($myfile !== '' && ! filter_var($myfile, FILTER_VALIDATE_URL)) {
            $fromKey = self::locateOnS3ByKey(ltrim($myfile, '/'));
            if ($fromKey !== null) {
                return $fromKey;
            }
        }

        return self::locateOnS3ByDocumentFields($document);
    }

    /**
     * @return array{disk: 's3', key: string}|null
     */
    private static function locateOnS3FromUrl(string $fileUrl): ?array
    {
        $parsed = parse_url($fileUrl);
        $urlPath = (string) ($parsed['path'] ?? '');
        if ($urlPath === '') {
            return null;
        }

        return self::locateOnS3ByKey(ltrim(urldecode($urlPath), '/'));
    }

    /**
     * @return array{disk: 's3', key: string}|null
     */
    private static function locateOnS3ByKey(string $key): ?array
    {
        $normalized = self::normalizeS3ObjectKey($key);
        if ($normalized === '' || str_starts_with($normalized, 'signed/') || str_starts_with($normalized, 'storage/')) {
            return null;
        }

        if (! Storage::disk('s3')->exists($normalized)) {
            return null;
        }

        return ['disk' => 's3', 'key' => $normalized];
    }

    /**
     * @return array{disk: 's3', key: string}|null
     */
    private static function locateOnS3ByDocumentFields(Document $document): ?array
    {
        $myfileKey = trim((string) ($document->myfile_key ?? ''));
        $docType = trim((string) ($document->doc_type ?? ''));
        if ($myfileKey === '' || $docType === '') {
            return null;
        }

        $prefixes = [];

        if ($document->client_id) {
            $admin = DB::table('admins')
                ->select('client_id')
                ->where('id', '=', $document->client_id)
                ->first();

            if ($admin && $admin->client_id) {
                $prefixes[] = $admin->client_id;
            }

            $prefixes[] = $document->client_id;
        }

        if ($document->user_id) {
            $prefixes[] = $document->user_id;
        }

        $fromMyfile = SignedDocumentS3PathResolver::parseMyfileS3Segments($document->myfile);
        if ($fromMyfile !== null) {
            $prefixes[] = $fromMyfile['prefix'];
        }

        $disk = Storage::disk('s3');

        foreach (array_unique($prefixes) as $prefix) {
            if ($prefix === null || $prefix === '') {
                continue;
            }

            $key = $prefix.'/'.$docType.'/'.$myfileKey;
            if ($disk->exists($key)) {
                return ['disk' => 's3', 'key' => $key];
            }
        }

        return null;
    }

    private static function resolveLocalPublicPath(string $fileUrl): ?string
    {
        $relativePath = self::extractLocalPublicRelativePath($fileUrl);
        if ($relativePath === null || ! Storage::disk('public')->exists($relativePath)) {
            return null;
        }

        try {
            $size = Storage::disk('public')->size($relativePath);
        } catch (\Throwable) {
            return null;
        }

        if ($size <= 0) {
            return null;
        }

        return Storage::disk('public')->path($relativePath);
    }

    private static function extractLocalPublicRelativePath(string $fileUrl): ?string
    {
        $parsed = parse_url($fileUrl);
        $urlPath = (string) ($parsed['path'] ?? '');

        if ($urlPath !== '' && str_contains($urlPath, '/storage/')) {
            $parts = explode('/storage/', $urlPath);
            $relative = end($parts);

            return $relative !== '' ? $relative : null;
        }

        if (! filter_var($fileUrl, FILTER_VALIDATE_URL)) {
            $relative = ltrim($fileUrl, '/');
            if ($relative !== '' && ! str_starts_with($relative, 'signed/')) {
                return $relative;
            }
        }

        return null;
    }

    private static function normalizeS3ObjectKey(string $key): string
    {
        $bucket = (string) config('filesystems.disks.s3.bucket', '');
        if ($bucket !== '' && str_starts_with($key, $bucket.'/')) {
            return substr($key, strlen($bucket) + 1);
        }

        return $key;
    }
}

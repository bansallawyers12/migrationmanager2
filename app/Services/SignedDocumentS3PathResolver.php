<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\DB;

/**
 * Resolves S3 storage paths for signed documents.
 *
 * Client-linked docs:  {admins.client_id}/{doc_type}/signed/{id}_signed.pdf
 * Ad-hoc docs (null client_id): {staff_admin_id}/{doc_type}/signed/{id}_signed.pdf
 *   — prefix parsed from myfile URL or fallback to documents.user_id
 */
class SignedDocumentS3PathResolver
{
    public static function resolvePrefix(Document $document): int|string|null
    {
        if ($document->client_id) {
            $admin = DB::table('admins')
                ->select('client_id')
                ->where('id', '=', $document->client_id)
                ->first();

            if ($admin && $admin->client_id) {
                return $admin->client_id;
            }

            return $document->client_id;
        }

        $fromMyfile = self::parseMyfileS3Segments($document->myfile);
        if ($fromMyfile !== null) {
            return $fromMyfile['prefix'];
        }

        if ($document->user_id) {
            return $document->user_id;
        }

        return null;
    }

    public static function resolveDocType(Document $document): string
    {
        $docType = trim((string) ($document->doc_type ?? ''));
        if ($docType !== '') {
            return $docType;
        }

        $fromMyfile = self::parseMyfileS3Segments($document->myfile);
        if ($fromMyfile !== null && $fromMyfile['doc_type'] !== '') {
            return $fromMyfile['doc_type'];
        }

        return 'ad_hoc_documents';
    }

    public static function resolveSignedPdfS3Key(Document $document): ?string
    {
        $prefix = self::resolvePrefix($document);
        if ($prefix === null || $prefix === '') {
            return null;
        }

        return $prefix . '/' . self::resolveDocType($document) . '/signed/' . $document->id . '_signed.pdf';
    }

    /**
     * Parse {prefix}/{doc_type}/... from an S3 URL or key stored in myfile.
     *
     * @return array{prefix: string, doc_type: string}|null
     */
    public static function parseMyfileS3Segments(?string $myfile): ?array
    {
        if ($myfile === null || trim($myfile) === '') {
            return null;
        }

        $path = trim($myfile);
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $parsed = parse_url($path);
            $path = ltrim((string) ($parsed['path'] ?? ''), '/');
        } else {
            $path = ltrim($path, '/');
        }

        if ($path === '' || str_starts_with($path, 'storage/') || str_starts_with($path, 'signed/')) {
            return null;
        }

        $segments = array_values(array_filter(explode('/', $path), static fn ($segment) => $segment !== ''));
        if (count($segments) < 2 || $segments[1] === 'signed') {
            return null;
        }

        return [
            'prefix' => $segments[0],
            'doc_type' => $segments[1],
        ];
    }
}

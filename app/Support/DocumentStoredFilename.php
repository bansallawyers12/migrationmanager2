<?php

namespace App\Support;

use App\Models\Admin;

/**
 * Builds the first segment of stored client document filenames (before _{checklist}_…).
 * Matches CRM behaviour: company clients use legal entity name; individuals use first name.
 */
final class DocumentStoredFilename
{
    /**
     * @param  string  $sanitizedFirstName  Already sanitized with the same rules as this class uses for company names (letters, digits, underscore, hyphen only).
     */
    public static function storedNamePrefix(?Admin $admin, string $sanitizedFirstName): string
    {
        if ($admin && (bool) $admin->is_company) {
            if (! $admin->relationLoaded('company')) {
                $admin->loadMissing('company');
            }
            $companyName = $admin->company?->company_name ?? '';
            if ($companyName !== '') {
                return preg_replace('/[^a-zA-Z0-9_\-]/', '_', $companyName);
            }

            return 'company';
        }

        return $sanitizedFirstName !== '' ? $sanitizedFirstName : 'client';
    }
}

<?php

namespace App\Support;

/**
 * Restricts CRM "From" addresses to configured domain suffixes (compose dropdown + send validation).
 *
 * Config: services.sendgrid.from_allowed_domains — string: comma-separated hostnames (no @), or "*"
 * for no filter. Array values are treated as a list of hostnames (empty array disables the filter).
 * Other non-string scalar config falls back to bansalimmigration.com.au only.
 *
 * Note: An explicit empty .env value for SENDGRID_FROM_ALLOWED_DOMAINS resolves to the string ""
 * (not the config default). That is treated as "use bansalimmigration.com.au" so the allowlist is
 * not accidentally disabled.
 */
final class SendGridFromAllowedDomains
{
    private const FALLBACK_DOMAIN = 'bansalimmigration.com.au';

    /**
     * @return list<string> Lowercased domain hostnames, no leading @
     */
    public static function domains(): array
    {
        $raw = config('services.sendgrid.from_allowed_domains', self::FALLBACK_DOMAIN);

        if (is_array($raw)) {
            return array_values(array_filter(array_map(static function ($part) {
                return strtolower(trim((string) $part));
            }, $raw)));
        }

        if (! is_string($raw)) {
            return [self::FALLBACK_DOMAIN];
        }

        $trimmed = trim($raw);
        if ($trimmed === '*') {
            return [];
        }

        if ($trimmed === '') {
            return [self::FALLBACK_DOMAIN];
        }

        return array_values(array_filter(array_map(static function ($part) {
            return strtolower(trim($part));
        }, explode(',', $trimmed))));
    }

    public static function isRestrictionActive(): bool
    {
        return self::domains() !== [];
    }

    public static function allowsEmail(string $email): bool
    {
        if (! self::isRestrictionActive()) {
            return true;
        }
        $email = strtolower(trim($email));
        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }
        foreach (self::domains() as $domain) {
            if ($domain !== '' && str_ends_with($email, '@'.$domain)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, array<string, mixed>>  $senders
     * @return array<int, array<string, mixed>>
     */
    public static function filterSenders(array $senders): array
    {
        if (! self::isRestrictionActive()) {
            return $senders;
        }

        return array_values(array_filter($senders, static function ($row) {
            $em = $row['email'] ?? '';

            return is_string($em) && self::allowsEmail($em);
        }));
    }
}

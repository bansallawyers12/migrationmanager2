<?php

namespace App\Support;

/**
 * Restricts CRM "From" addresses to configured domain suffixes (compose dropdown + send validation).
 *
 * Config: services.sendgrid.from_allowed_domains — comma-separated hostnames, or "*" for no filter.
 */
final class SendGridFromAllowedDomains
{
    /**
     * @return list<string> Lowercased domain hostnames, no leading @
     */
    public static function domains(): array
    {
        $raw = config('services.sendgrid.from_allowed_domains', 'bansalimmigration.com.au');
        if (! is_string($raw)) {
            return [];
        }
        $trimmed = trim($raw);
        if ($trimmed === '' || $trimmed === '*') {
            return [];
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
        if ($email === '' || ! str_contains($email, '@')) {
            return false;
        }
        foreach (self::domains() as $domain) {
            if (str_ends_with($email, '@'.$domain)) {
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

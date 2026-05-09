<?php

namespace App\Helpers;

use App\Models\Country;

class PhoneHelper
{
    /**
     * Normalize to +digits or null if empty/invalid (no default fallback).
     */
    public static function strictNormalizeDialCode($code): ?string
    {
        if ($code === null || (! is_string($code) && ! is_numeric($code))) {
            return null;
        }

        $code = trim((string) $code);
        if ($code === '') {
            return null;
        }

        $code = preg_replace('/[^\d+]/', '', $code);

        if ($code === '') {
            return null;
        }

        if (! str_starts_with($code, '+')) {
            $code = '+' . ltrim($code, '+');
        }

        $code = preg_replace('/\++/', '+', $code);

        if (! preg_match('/^\+\d{1,4}$/', $code)) {
            return null;
        }

        return $code;
    }

    /**
     * Normalize country dial code to +digits (non-empty input).
     * Empty input falls back to configured default.
     */
    public static function normalizeCountryCode($code): string
    {
        $strict = self::strictNormalizeDialCode($code);

        return $strict ?? self::getDefaultCountryCode();
    }

    /**
     * Normalize dial code when present; empty string stays empty (for optional selects).
     */
    public static function normalizeDialCodeOrEmpty(?string $code): string
    {
        if ($code === null || trim($code) === '') {
            return '';
        }

        return self::strictNormalizeDialCode(trim($code)) ?? '';
    }

    public static function getDefaultCountryCode(): string
    {
        return config('phone.default_country_code', '+61');
    }

    /**
     * Store normalized +XX or blank when input blank or invalid (never guess +61).
     */
    public static function formatForStorage(?string $code): string
    {
        if ($code === null || trim((string) $code) === '') {
            return '';
        }

        return self::strictNormalizeDialCode(trim((string) $code)) ?? '';
    }

    public static function isValidFormat($code): bool
    {
        return self::strictNormalizeDialCode($code) !== null;
    }

    public static function isValidCountryCode($code): bool
    {
        $normalized = self::strictNormalizeDialCode($code);
        if ($normalized === null) {
            return false;
        }

        if (! config('phone.validate_against_db')) {
            return true;
        }

        try {
            return Country::isValidPhoneCode($normalized);
        } catch (\Throwable $e) {
            return false;
        }
    }
}

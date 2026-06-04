<?php

namespace App\Support;

/**
 * Normalise compose-email POST payloads (WAF-safe transport + subject placeholders).
 */
final class ComposeEmailPayload
{
    /**
     * Decode message body when the front-end sent it base64-encoded to avoid WAF/mod_security blocks
     * on HTML, URLs, and special characters in multipart POST bodies.
     */
    public static function decodeMessage(array $requestData): string
    {
        $message = (string) ($requestData['message'] ?? '');

        if (($requestData['message_encoding'] ?? '') !== 'base64') {
            return $message;
        }

        $decoded = base64_decode($message, true);

        return $decoded !== false ? $decoded : $message;
    }

    /**
     * Restore subject after front-end WAF-safe placeholder encoding.
     */
    public static function decodeSubject(array $requestData): string
    {
        $subject = (string) ($requestData['subject'] ?? '');

        return str_replace('__AMP__', '&', $subject);
    }

    /**
     * Validate a document signing URL from compose email (must be http(s) with /sign/{id}/{token}).
     */
    public static function normalizeSigningUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '' || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }
        if (!preg_match('#/sign/\d+/[A-Za-z0-9]+#', $url)) {
            return null;
        }

        return $url;
    }

    /**
     * HTML anchor for the service agreement signing link in checklist / first-email templates.
     */
    public static function buildServiceAgreementSignLinkHtml(string $url): string
    {
        $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

        return '<a href="' . $safeUrl . '" target="_blank" rel="noopener noreferrer" style="color:#2563eb;text-decoration:underline;">Sign Service Agreement</a>';
    }

    /**
     * Ensure the message contains a clickable sign link when a signing URL was provided.
     * Replaces {PDF_url_for_sign} and repairs common TinyMCE underline-only label markup.
     */
    public static function applySigningLinkToMessage(string $message, ?string $signingUrl): string
    {
        $url = self::normalizeSigningUrl($signingUrl);
        if ($url === null) {
            return $message;
        }

        $link = self::buildServiceAgreementSignLinkHtml($url);
        $message = str_replace('{PDF_url_for_sign}', $link, $message);

        if (preg_match('#href\s*=\s*["\'][^"\']*/sign/#i', $message)) {
            return $message;
        }

        $patterns = [
            '#<span[^>]*>\s*Sign Service Agreement\s*</span>#i',
            '#<u>\s*Sign Service Agreement\s*</u>#i',
            '#<a(?![^>]*\bhref\s*=)[^>]*>\s*Sign Service Agreement\s*</a>#i',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message)) {
                return preg_replace($pattern, $link, $message, 1);
            }
        }

        return $message;
    }
}

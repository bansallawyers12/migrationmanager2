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
}

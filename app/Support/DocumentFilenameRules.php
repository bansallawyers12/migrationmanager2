<?php

namespace App\Support;

/**
 * Allowed characters for client document upload filenames (personal, visa, portal).
 */
final class DocumentFilenameRules
{
    public const ALLOWED_PATTERN = '/^[a-zA-Z0-9_\-\.\s\$\(\),&+\']+$/';

    public static function isAllowed(string $fileName): bool
    {
        return (bool) preg_match(self::ALLOWED_PATTERN, $fileName);
    }

    public static function validationMessage(): string
    {
        return 'File name can only contain letters, numbers, dashes (-), underscores (_), spaces, dots (.), dollar signs ($), parentheses (( )), commas (,), ampersands (&), apostrophes (\'), and plus signs (+). Please rename the file and try again.';
    }
}

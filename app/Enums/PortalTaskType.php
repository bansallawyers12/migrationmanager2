<?php

namespace App\Enums;

enum PortalTaskType: string
{
    case Upload = 'upload';
    case Sign = 'sign';
    case Pay = 'pay';

    public function label(): string
    {
        return match ($this) {
            self::Upload => 'Upload',
            self::Sign => 'Review and sign',
            self::Pay => 'Pay',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

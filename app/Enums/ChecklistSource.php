<?php

namespace App\Enums;

enum ChecklistSource: string
{
    case Workflow = 'workflow';
    case Portal = 'portal';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

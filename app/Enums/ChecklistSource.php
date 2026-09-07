<?php

namespace App\Enums;

enum ChecklistSource: string
{
    case Workflow = 'workflow';
    case Portal = 'portal';
    case PortalApp = 'portal_app';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Client Portal Documents / mobile API sources.
     *
     * @return list<string>
     */
    public static function portalValues(): array
    {
        return [
            self::Portal->value,
            self::PortalApp->value,
        ];
    }

    public function activityLabel(): string
    {
        return match ($this) {
            self::Workflow => 'by Workflow',
            self::Portal => 'by Portal',
            self::PortalApp => 'by Portal app',
        };
    }
}

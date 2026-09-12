<?php

namespace App\Support\Mcp;

use App\Models\Staff;
use App\Support\StaffClientVisibility;
use Illuminate\Support\Facades\Auth;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

/**
 * Auth + visibility helpers for CRM MCP tools (Staff Sanctum tokens only).
 */
final class CrmMcpAccess
{
    public static function isSuperAdmin(Staff $staff): bool
    {
        return (int) ($staff->role ?? 0) === 1;
    }

    public static function mayDiscontinueOrReopenMatter(Staff $staff): bool
    {
        return in_array(
            (int) ($staff->role ?? 0),
            config('crm.matter_discontinue_role_ids', [1, 17, 16]),
            true
        );
    }

    /**
     * Resolve the acting Staff and bind them to Auth so StaffClientVisibility works.
     */
    public static function staff(Request $request): Staff|Response
    {
        $user = $request->user();

        if (! $user instanceof Staff) {
            return Response::error('MCP CRM requires a Staff Sanctum personal access token.');
        }

        if ((int) ($user->status ?? 0) !== 1) {
            return Response::error('This staff account is inactive.');
        }

        Auth::guard('admin')->setUser($user);
        Auth::setUser($user);

        return $user;
    }

    public static function denyUnlessCanAccess(int $adminId, Staff $staff): ?Response
    {
        if (StaffClientVisibility::canAccessClientOrLead($adminId, $staff)) {
            return null;
        }

        return Response::error('You do not have access to this client or lead.');
    }

    public static function staffDisplayName(Staff $staff): string
    {
        return trim(($staff->first_name ?? '').' '.($staff->last_name ?? '')) ?: 'Staff #'.$staff->id;
    }
}

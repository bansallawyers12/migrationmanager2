<?php

namespace App\Support;

use App\Models\Admin;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Row-level visibility for CRM staff.
 *
 * Clients (admins.type=client): "Person Assisting" roles are limited to matters they assist
 * on or admins.user_id = staff id. Super admin (staff role 1) bypasses. Other roles see all clients.
 *
 * Leads (admins.type=lead): roles in lead_full_access_role_ids (default 1 Super Admin, 17 Admin, 12 PR)
 * see all leads; everyone else only sees rows where admins.user_id = staff.id (assigned staff).
 *
 * Note: Controllers that accept client_id in AJAX (documents, activities, etc.) must call
 * canAccessClientOrLead() — listing/detail alone is not enough.
 */
final class StaffClientVisibility
{
    private const DEFAULT_PERSON_ASSISTING_ROLE_IDS = [13];

    /** Super Admin (1), PR (12), Admin (17) — must stay aligned with config/crm.php */
    private const DEFAULT_LEAD_FULL_ACCESS_ROLE_IDS = [1, 12, 17];

    public static function personAssistingRoleIds(): array
    {
        $ids = config('crm.person_assisting_role_ids', self::DEFAULT_PERSON_ASSISTING_ROLE_IDS);
        $filtered = array_values(array_filter(
            array_map('intval', (array) $ids),
            static fn (int $id) => $id > 0
        ));

        return $filtered !== [] ? $filtered : self::DEFAULT_PERSON_ASSISTING_ROLE_IDS;
    }

    /**
     * Staff roles that see every lead (list + detail). Default: Super Admin (1), Admin (17), PR (12).
     *
     * @return list<int>
     */
    public static function leadFullAccessRoleIds(): array
    {
        $ids = config('crm.lead_full_access_role_ids', self::DEFAULT_LEAD_FULL_ACCESS_ROLE_IDS);
        $filtered = array_values(array_filter(
            array_map('intval', (array) $ids),
            static fn (int $id) => $id > 0
        ));

        return $filtered !== [] ? $filtered : self::DEFAULT_LEAD_FULL_ACCESS_ROLE_IDS;
    }

    /**
     * @return array{status: bool, message: string, error_type: string}
     */
    public static function unauthorizedPayload(): array
    {
        return [
            'status' => false,
            'message' => 'Unauthorized',
            'error_type' => 'forbidden',
        ];
    }

    public static function isRestrictedPersonAssisting(?Authenticatable $user): bool
    {
        if (!$user) {
            return false;
        }

        if ((int) ($user->role ?? 0) === 1) {
            return false;
        }

        return in_array((int) $user->role, self::personAssistingRoleIds(), true);
    }

    public static function personAssistingStaffIdOrNull(?Authenticatable $user): ?int
    {
        return self::isRestrictedPersonAssisting($user) ? (int) $user->id : null;
    }

    /**
     * Lead list queries: full-access roles see all leads; others only assigned leads (admins.user_id).
     * Does not change client listing — use restrictAdminEloquentQuery for clients.
     *
     * @param  Builder<\App\Models\Lead>  $query
     */
    public static function restrictLeadListQuery(Builder $query): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        if (in_array((int) ($user->role ?? 0), self::leadFullAccessRoleIds(), true)) {
            return;
        }

        $column = $query->getModel()->qualifyColumn('user_id');
        $query->where($column, (int) $user->id);
    }

    /**
     * @param  Builder<\App\Models\Admin>  $query
     */
    public static function restrictAdminEloquentQuery(Builder $query): void
    {
        $user = Auth::user();
        if (!self::isRestrictedPersonAssisting($user)) {
            return;
        }

        $staffId = (int) $user->id;

        $query->where(function (Builder $q) use ($staffId) {
            $q->whereExists(function ($sub) use ($staffId) {
                $sub->select(DB::raw('1'))
                    ->from('client_matters')
                    ->whereColumn('client_matters.client_id', 'admins.id')
                    ->where('client_matters.sel_person_assisting', $staffId);
            })->orWhere('admins.user_id', $staffId);
        });
    }

    public static function canAccessClientOrLead(int $adminId, ?Authenticatable $user = null): bool
    {
        $user = $user ?? Auth::user();

        if (!$user || (int) ($user->role ?? 0) === 1) {
            return true;
        }

        $row = Admin::query()
            ->where('id', $adminId)
            ->whereIn('type', ['client', 'lead'])
            ->first(['id', 'type', 'user_id']);

        if (!$row) {
            return false;
        }

        if (($row->type ?? '') === 'lead') {
            return self::canStaffAccessLeadRow($user, $row);
        }

        if (!self::isRestrictedPersonAssisting($user)) {
            return true;
        }

        $staffId = (int) $user->id;

        if (DB::table('client_matters')
            ->where('client_id', $adminId)
            ->where('sel_person_assisting', $staffId)
            ->exists()) {
            return true;
        }

        return (int) ($row->user_id ?? 0) === $staffId;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model|object  $leadRow  admins row with user_id
     */
    private static function canStaffAccessLeadRow(Authenticatable $user, object $leadRow): bool
    {
        if (in_array((int) ($user->role ?? 0), self::leadFullAccessRoleIds(), true)) {
            return true;
        }

        $assignedStaffId = (int) ($leadRow->user_id ?? 0);

        return $assignedStaffId > 0 && $assignedStaffId === (int) $user->id;
    }
}

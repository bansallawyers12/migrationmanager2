<?php

namespace App\Support;

use App\Models\Admin;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Row-level visibility for CRM staff whose role is "Person Assisting".
 *
 * Access is allowed when either:
 * - any client_matters row for that admins.id has sel_person_assisting = staff id, or
 * - admins.user_id = staff id (typical lead assignment).
 *
 * Super admin (staff role 1) bypasses. Other roles are unchanged.
 *
 * Note: Controllers that accept client_id in AJAX (documents, activities, etc.) must call
 * canAccessClientOrLead() — listing/detail alone is not enough.
 */
final class StaffClientVisibility
{
    private const DEFAULT_PERSON_ASSISTING_ROLE_IDS = [13];

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

        $row = Admin::query()
            ->where('id', $adminId)
            ->whereIn('type', ['client', 'lead'])
            ->first(['id', 'user_id']);

        return $row && (int) ($row->user_id ?? 0) === $staffId;
    }
}

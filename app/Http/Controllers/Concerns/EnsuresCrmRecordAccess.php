<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Admin;
use App\Support\StaffClientVisibility;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait EnsuresCrmRecordAccess
{
    /**
     * Abort with 403 unless the current staff may access this client/lead row (admins.id).
     */
    protected function ensureCrmRecordAccess(int $adminId): void
    {
        if ($adminId <= 0) {
            return;
        }

        if (! Admin::query()->where('id', $adminId)->whereIn('type', ['client', 'lead'])->exists()) {
            return;
        }

        $user = Auth::guard('admin')->user();
        if (StaffClientVisibility::canAccessClientOrLead($adminId, $user)) {
            return;
        }

        if (request()->expectsJson() || request()->ajax()) {
            throw new HttpResponseException(
                response()->json(StaffClientVisibility::unauthorizedPayload(), 403)
            );
        }

        abort(403, 'Unauthorized');
    }

    /**
     * Run access check for the first present request key (client_id, admin_id, lead_id).
     */
    protected function ensureCrmRecordAccessFromRequest(Request $request, array $keys = ['client_id', 'admin_id', 'lead_id']): void
    {
        foreach ($keys as $key) {
            if (! $request->filled($key)) {
                continue;
            }
            $v = (int) $request->input($key);
            if ($v > 0) {
                $this->ensureCrmRecordAccess($v);

                return;
            }
        }
    }
}

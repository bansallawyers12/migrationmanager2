<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\ClientAccessGrant;
use App\Models\Team;
use App\Services\CrmAccess\CrmAccessDeniedException;
use App\Services\CrmAccess\CrmAccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AccessGrantController extends Controller
{
    public function __construct(
        protected CrmAccessService $crmAccess
    ) {}

    public function meta(): JsonResponse
    {
        $user = Auth::guard('admin')->user();
        if (! $user) {
            abort(401);
        }

        $teamId = $user->team;
        $teamIdInt = is_numeric($teamId) ? (int) $teamId : null;

        return response()->json([
            'branches' => Branch::query()->orderBy('office_name')->get(['id', 'office_name']),
            'teams' => Team::query()->orderBy('name')->get(['id', 'name', 'color']),
            'quick_reasons' => collect(config('crm_access.quick_reason_options', []))
                ->map(fn (string $label, string $code) => ['code' => $code, 'label' => $label])
                ->values(),
            'staff_office_id' => $user->office_id,
            'staff_team_id' => $teamIdInt,
            'ui' => [
                'show_quick' => \App\Support\StaffClientVisibility::crossAccessUiFlags($user)['show_quick'],
                'show_supervisor' => \App\Support\StaffClientVisibility::crossAccessUiFlags($user)['show_supervisor'],
                'quick_only_role' => \App\Support\StaffClientVisibility::isQuickAccessOnly($user),
            ],
        ]);
    }

    public function quick(Request $request): JsonResponse
    {
        $user = $this->requireStaff();

        $data = $request->validate([
            'admin_id' => ['required', 'integer', 'min:1'],
            'record_type' => ['required', Rule::in(['client', 'lead'])],
            'office_id' => ['required', 'integer', 'min:1'],
            'team_id' => ['nullable', 'integer', 'min:1'],
            'reason_code' => ['required', 'string', 'max:50'],
        ]);

        if (! Admin::query()->where('id', $data['admin_id'])->where('type', $data['record_type'])->exists()) {
            return response()->json(['message' => 'Record not found.'], 422);
        }

        try {
            $grant = $this->crmAccess->requestQuickGrant(
                $user,
                (int) $data['admin_id'],
                $data['record_type'],
                (int) $data['office_id'],
                isset($data['team_id']) ? (int) $data['team_id'] : null,
                $data['reason_code']
            );
        } catch (CrmAccessDeniedException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'grant' => $grant,
            'message' => 'Quick access granted.',
        ]);
    }

    public function supervisor(Request $request): JsonResponse
    {
        $user = $this->requireStaff();

        $data = $request->validate([
            'admin_id' => ['required', 'integer', 'min:1'],
            'record_type' => ['required', Rule::in(['client', 'lead'])],
            'office_id' => ['required', 'integer', 'min:1'],
            'team_id' => ['nullable', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        if (! Admin::query()->where('id', $data['admin_id'])->where('type', $data['record_type'])->exists()) {
            return response()->json(['message' => 'Record not found.'], 422);
        }

        try {
            $grant = $this->crmAccess->requestSupervisorGrant(
                $user,
                (int) $data['admin_id'],
                $data['record_type'],
                (int) $data['office_id'],
                isset($data['team_id']) ? (int) $data['team_id'] : null,
                (string) ($data['note'] ?? '')
            );
        } catch (CrmAccessDeniedException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'grant' => $grant,
            'message' => 'Supervisor request submitted.',
        ]);
    }

    public function queuePage()
    {
        $user = $this->requireStaff();
        if (! $this->crmAccess->isApprover($user)) {
            abort(403, 'Not authorized.');
        }

        return view('crm.access.queue', [
            'dataUrl' => route('crm.access.queue.data'),
        ]);
    }

    public function queueData(): JsonResponse
    {
        $user = $this->requireStaff();
        if (! $this->crmAccess->isApprover($user)) {
            abort(403, 'Not authorized.');
        }

        $items = ClientAccessGrant::query()
            ->with(['staff:id,first_name,last_name,email', 'admin:id,first_name,last_name,client_id,type'])
            ->where('status', 'pending')
            ->where('grant_type', 'supervisor_approved')
            ->orderByDesc('requested_at')
            ->limit(200)
            ->get();

        return response()->json(['items' => $items]);
    }

    public function approve(ClientAccessGrant $grant): JsonResponse
    {
        $user = $this->requireStaff();
        try {
            $grant = $this->crmAccess->approveGrant($user, (int) $grant->id);
        } catch (CrmAccessDeniedException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['grant' => $grant, 'message' => 'Approved.']);
    }

    public function reject(Request $request, ClientAccessGrant $grant): JsonResponse
    {
        $user = $this->requireStaff();
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);
        try {
            $grant = $this->crmAccess->rejectGrant($user, (int) $grant->id, (string) ($data['reason'] ?? ''));
        } catch (CrmAccessDeniedException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['grant' => $grant, 'message' => 'Rejected.']);
    }

    public function myGrantsPage()
    {
        $this->requireStaff();

        return view('crm.access.my_grants');
    }

    public function myGrantsData(): JsonResponse
    {
        $user = $this->requireStaff();

        $items = ClientAccessGrant::query()
            ->where('staff_id', (int) $user->id)
            ->orderByDesc('requested_at')
            ->limit(100)
            ->get();

        return response()->json(['items' => $items]);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $user = $this->requireStaff();
        if (! $this->crmAccess->isApprover($user)) {
            abort(403, 'Not authorized.');
        }

        $pending = ClientAccessGrant::query()->where('status', 'pending')->count();
        $active = ClientAccessGrant::query()->where('status', 'active')->count();

        $recent = ClientAccessGrant::query()
            ->with(['staff:id,first_name,last_name', 'admin:id,first_name,last_name'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return response()->json([
            'pending_count' => $pending,
            'active_count' => $active,
            'recent' => $recent,
        ]);
    }

    protected function requireStaff(): \App\Models\Staff
    {
        $user = Auth::guard('admin')->user();
        if (! $user instanceof \App\Models\Staff) {
            abort(401);
        }

        return $user;
    }
}

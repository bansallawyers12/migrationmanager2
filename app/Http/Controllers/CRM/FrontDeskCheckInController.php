<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\BookingAppointment;
use App\Models\CheckinHistory;
use App\Models\CheckinLog;
use App\Models\FrontDeskCheckIn;
use App\Models\Staff;
use App\Services\FrontDesk\CheckInAppointmentService;
use App\Services\FrontDesk\CheckInLookupService;
use App\Services\FrontDesk\CheckInNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FrontDeskCheckInController extends Controller
{
    /**
     * Roles permitted to use the front-desk check-in wizard.
     * 1 = Super Admin, 12 = Admin, 17 = Reception (exempt role per crm_access config).
     */
    private const ALLOWED_ROLES = [1, 12, 17];

    public function __construct(
        private readonly CheckInLookupService      $lookup,
        private readonly CheckInAppointmentService $appointments,
        private readonly CheckInNotificationService $notifier,
    ) {
        $this->middleware('auth:admin');
    }

    // -------------------------------------------------------------------------
    // GET /front-desk/checkin
    // -------------------------------------------------------------------------

    public function index(): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        if (!$this->authorised()) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access the front-desk check-in.');
        }

        $visitReasons = FrontDeskCheckIn::visitReasons();

        return view('crm.front_desk.checkin', compact('visitReasons'));
    }

    // -------------------------------------------------------------------------
    // POST /front-desk/checkin/lookup  (AJAX)
    // -------------------------------------------------------------------------

    public function lookupContact(Request $request): JsonResponse
    {
        if (!$this->authorised()) {
            return response()->json(['error' => 'Unauthorised'], 403);
        }

        $request->validate([
            'phone' => 'required|string|min:6|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $matches = $this->lookup->lookup(
            $request->input('phone'),
            $request->input('email')
        );

        $data = $matches->map(fn (Admin $a) => $this->lookup->formatForWizard($a))->values();

        return response()->json([
            'matches'          => $data,
            'phone_normalized' => $this->lookup->normalizePhone($request->input('phone')),
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /front-desk/checkin/appointments  (AJAX)
    // -------------------------------------------------------------------------

    public function getAppointments(Request $request): JsonResponse
    {
        if (!$this->authorised()) {
            return response()->json(['error' => 'Unauthorised'], 403);
        }

        $request->validate(['admin_id' => 'required|integer|min:1']);

        $adminId = (int) $request->input('admin_id');
        $appts   = $this->appointments->getTodaysAppointments($adminId);

        $data = $appts->map(fn (BookingAppointment $a) => $this->appointments->formatForWizard($a))->values();

        return response()->json(['appointments' => $data]);
    }

    // -------------------------------------------------------------------------
    // POST /front-desk/checkin/submit
    // -------------------------------------------------------------------------

    public function submit(Request $request): JsonResponse
    {
        if (!$this->authorised()) {
            return response()->json(['error' => 'Unauthorised'], 403);
        }

        $validated = $request->validate([
            'phone'              => 'required|string|min:6|max:20',
            'email'              => 'nullable|email|max:255',
            'admin_id'           => 'nullable|integer',       // matched CRM record id (client or lead)
            'admin_type'         => 'nullable|in:client,lead',
            'appointment_id'     => 'nullable|integer',
            'claimed_appointment'=> 'nullable|boolean',
            'visit_reason'       => 'nullable|string|max:100',
            'visit_notes'        => 'nullable|string|max:2000',
        ]);

        // "Other" reason requires notes
        if (($validated['visit_reason'] ?? null) === 'other' && empty($validated['visit_notes'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide notes when selecting "Other" as the reason.',
                'errors'  => ['visit_notes' => ['Notes are required for "Other" reason.']],
            ], 422);
        }

        $phoneNormalized = $this->lookup->normalizePhone($validated['phone']);
        $adminId         = isset($validated['admin_id']) ? (int) $validated['admin_id'] : null;
        $adminType       = $validated['admin_type'] ?? null;

        // Resolve client_id / lead_id
        $clientId = null;
        $leadId   = null;
        if ($adminId && $adminType) {
            if ($adminType === 'client') {
                $clientId = $adminId;
            } else {
                $leadId = $adminId;
            }
        }

        // Validate appointment belongs to today and to the matched admin
        $appointmentId = null;
        if (!empty($validated['appointment_id'])) {
            $appt = BookingAppointment::find((int) $validated['appointment_id']);
            if ($appt && $adminId && (int) $appt->client_id === $adminId) {
                $appointmentId = $appt->id;
            }
        }

        try {
            DB::beginTransaction();

            /** @var Staff $staff */
            $staff = Auth::guard('admin')->user();

            $checkIn = FrontDeskCheckIn::create([
                'admin_id'            => $staff->id,
                'phone_normalized'    => $phoneNormalized,
                'email'               => $validated['email'] ?? null,
                'client_id'           => $clientId,
                'lead_id'             => $leadId,
                'appointment_id'      => $appointmentId,
                'claimed_appointment' => (bool) ($validated['claimed_appointment'] ?? false),
                'visit_reason'        => $validated['visit_reason'] ?? null,
                'visit_notes'         => $validated['visit_notes'] ?? null,
                'metadata'            => [
                    'submitted_at' => now()->toIso8601String(),
                    'ip'           => $request->ip(),
                ],
            ]);

            // Send notification to the assignee / consultant
            $notified = $this->notifier->notify($checkIn, $staff);

            DB::commit();

            return response()->json([
                'success'          => true,
                'message'          => 'Check-in recorded successfully.',
                'check_in_id'      => $checkIn->id,
                'notified_staff'   => $notified ? $notified->full_name : null,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[FrontDeskCheckIn] Submit failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the check-in. Please try again.',
            ], 500);
        }
    }

    // -------------------------------------------------------------------------
    // Authorisation helper
    // -------------------------------------------------------------------------

    private function authorised(): bool
    {
        $user = Auth::guard('admin')->user();
        if (!$user) {
            return false;
        }
        $allowedRoles = array_merge(
            self::ALLOWED_ROLES,
            config('crm_access.exempt_role_ids', [])
        );
        return in_array((int) $user->role, $allowedRoles, true);
    }
}

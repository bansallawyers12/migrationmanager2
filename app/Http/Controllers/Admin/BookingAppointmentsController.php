<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingAppointment;
use App\Models\AppointmentConsultant;
use App\Models\AppointmentSyncLog;
use App\Models\ActivitiesLog;
use App\Services\BansalAppointmentSync\AppointmentSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class BookingAppointmentsController extends Controller
{
    protected AppointmentSyncService $syncService;

    public function __construct(AppointmentSyncService $syncService)
    {
        $this->middleware('auth:admin');
        $this->syncService = $syncService;
    }

    /**
     * Display appointment list
     */
    public function index(Request $request)
    {
        $query = BookingAppointment::with(['client', 'consultant']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('consultant_id')) {
            $query->where('consultant_id', $request->consultant_id);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('appointment_datetime', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('appointment_datetime', '<=', $request->date_to);
        }
        
        // Paginate appointments
        $appointments = $query->latest('appointment_datetime')->paginate(20);
        
        // Get consultants for filter
        $consultants = AppointmentConsultant::active()->get();
        
        // Calculate statistics
        $stats = [
            'pending' => BookingAppointment::where('status', 'pending')->count(),
            'confirmed' => BookingAppointment::where('status', 'confirmed')->count(),
            'today' => BookingAppointment::whereDate('appointment_datetime', today())->count(),
            'total' => BookingAppointment::count(),
        ];
        
        return view('Admin.booking.appointments.index', compact('appointments', 'consultants', 'stats'));
    }

    /**
     * Get appointments for DataTables
     */
    public function getAppointments(Request $request)
    {
        $query = BookingAppointment::with(['client', 'consultant']);

        // Filter by calendar type (consultant type)
        if ($request->filled('type')) {
            $query->whereHas('consultant', function($q) use ($request) {
                $q->where('calendar_type', $request->type);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by consultant
        if ($request->filled('consultant_id')) {
            $query->where('consultant_id', $request->consultant_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('appointment_datetime', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('appointment_datetime', '<=', $request->date_to);
        }

        // Check if calendar format is requested
        if ($request->get('format') === 'calendar') {
            $appointments = $query->get();
            
            // Debug logging
            \Log::info('Calendar API Request', [
                'type' => $request->get('type'),
                'start' => $request->get('start'),
                'end' => $request->get('end'),
                'appointments_count' => $appointments->count()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $appointments->map(function ($appointment) {
                    return [
                        'id' => $appointment->id,
                        'client_name' => $appointment->client_name,
                        'client_email' => $appointment->client_email,
                        'client_phone' => $appointment->client_phone,
                        'service_type' => $appointment->service_type,
                        'appointment_datetime' => $appointment->appointment_datetime->toIso8601String(),
                        'duration_minutes' => $appointment->duration_minutes,
                        'status' => $appointment->status,
                        'location' => $appointment->location,
                        'is_paid' => $appointment->is_paid,
                        'consultant' => $appointment->consultant ? [
                            'id' => $appointment->consultant->id,
                            'name' => $appointment->consultant->name,
                        ] : null,
                    ];
                })
            ]);
        }

        // Default: Return DataTables format
        return DataTables::of($query)
            ->addColumn('client_info', function ($appointment) {
                if ($appointment->client_id) {
                    $clientLink = route('admin.clients.detail', base64_encode(convert_uuencode($appointment->client_id)));
                    
                    return '<a href="' . $clientLink . '" target="_blank">' . 
                           '<strong>' . e($appointment->client_name) . '</strong><br>' .
                           '<small>' . e($appointment->client_email) . '</small>' .
                           '</a>';
                }
                
                return '<strong>' . e($appointment->client_name) . '</strong><br>' .
                       '<small>' . e($appointment->client_email) . '</small>';
            })
            ->addColumn('appointment_info', function ($appointment) {
                return '<strong>' . $appointment->appointment_datetime->format('d/m/Y') . '</strong><br>' .
                       '<small>' . ($appointment->timeslot_full ?? $appointment->appointment_datetime->format('h:i A')) . '</small>';
            })
            ->addColumn('consultant_info', function ($appointment) {
                return $appointment->consultant 
                    ? '<span class="badge badge-info">' . e($appointment->consultant->name) . '</span>'
                    : '<span class="badge badge-secondary">Unassigned</span>';
            })
            ->addColumn('status_badge', function ($appointment) {
                $color = $appointment->status_badge;
                $label = ucfirst(str_replace('_', ' ', $appointment->status));
                return '<span class="badge badge-' . $color . '">' . $label . '</span>';
            })
            ->addColumn('payment_info', function ($appointment) {
                if ($appointment->is_paid) {
                    return '<span class="badge badge-success">Paid</span><br>' .
                           '<small>$' . number_format($appointment->final_amount, 2) . '</small>';
                }
                return '<span class="badge badge-secondary">Free</span>';
            })
            ->addColumn('actions', function ($appointment) {
                return '<a href="' . route('booking.appointments.show', $appointment->id) . '" class="btn btn-sm btn-primary">' .
                       '<i class="fas fa-eye"></i> View' .
                       '</a>';
            })
            ->rawColumns(['client_info', 'appointment_info', 'consultant_info', 'status_badge', 'payment_info', 'actions'])
            ->make(true);
    }

    /**
     * Show appointment detail
     */
    public function show($id)
    {
        $appointment = BookingAppointment::with(['client', 'consultant', 'assignedBy'])->findOrFail($id);
        $consultants = AppointmentConsultant::active()->get();
        
        return view('Admin.booking.appointments.show', compact('appointment', 'consultants'));
    }

    /**
     * Calendar view by type
     */
    public function calendar($type)
    {
        $validTypes = ['paid', 'jrp', 'education', 'tourist', 'adelaide'];
        
        if (!in_array($type, $validTypes)) {
            abort(404);
        }

        $appointments = BookingAppointment::with(['client', 'consultant'])
            ->where(function ($query) use ($type) {
                $query->whereHas('consultant', function ($q) use ($type) {
                    $q->where('calendar_type', $type);
                })->orWhereNull('consultant_id');
            })
            ->where('appointment_datetime', '>=', now()->subDays(30))
            ->orderBy('appointment_datetime')
            ->get();

        $calendarTitle = match($type) {
            'paid' => 'Pr_complex matters',
            'jrp' => 'JRP/Skill Assessment',
            'education' => 'Education/Student Visa',
            'tourist' => 'Tourist Visa',
            'adelaide' => 'Adelaide Office',
            default => ucfirst($type)
        };
        
        // Calculate statistics for this calendar type
        $stats = [
            'this_month' => BookingAppointment::whereHas('consultant', function ($q) use ($type) {
                    $q->where('calendar_type', $type);
                })
                ->whereMonth('appointment_datetime', now()->month)
                ->count(),
            'today' => BookingAppointment::whereHas('consultant', function ($q) use ($type) {
                    $q->where('calendar_type', $type);
                })
                ->whereDate('appointment_datetime', today())
                ->count(),
            'upcoming' => BookingAppointment::whereHas('consultant', function ($q) use ($type) {
                    $q->where('calendar_type', $type);
                })
                ->where('appointment_datetime', '>', now())
                ->count(),
            'pending' => BookingAppointment::whereHas('consultant', function ($q) use ($type) {
                    $q->where('calendar_type', $type);
                })
                ->where('status', 'pending')
                ->count(),
            'no_show' => BookingAppointment::whereHas('consultant', function ($q) use ($type) {
                    $q->where('calendar_type', $type);
                })
                ->where('status', 'no_show')
                ->count(),
        ];

        // Use FullCalendar v6 version
        return view('Admin.booking.appointments.calendar-v6', compact('type', 'appointments', 'calendarTitle', 'stats'));
    }

    /**
     * Update appointment status
     */
    public function updateStatus(Request $request, $id)
    {
        $appointment = BookingAppointment::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled,no_show,rescheduled',
            'cancellation_reason' => 'nullable|string'
        ]);

        $oldStatus = $appointment->status;
        $appointment->status = $request->status;

        // Set timestamp based on status
        switch ($request->status) {
            case 'confirmed':
                $appointment->confirmed_at = now();
                break;
            case 'completed':
                $appointment->completed_at = now();
                break;
            case 'cancelled':
                $appointment->cancelled_at = now();
                if ($request->filled('cancellation_reason')) {
                    $appointment->cancellation_reason = $request->cancellation_reason;
                }
                break;
        }

        $appointment->save();

        // Log activity using existing codebase pattern (only if client exists)
        if ($appointment->client_id) {
            $activityLog = new ActivitiesLog;
            $activityLog->client_id = $appointment->client_id;
            $activityLog->created_by = Auth::id();
            $activityLog->subject = 'Booking appointment status updated';
            $activityLog->description = '<p><strong>Status changed:</strong> ' . ucfirst($oldStatus) . ' → ' . ucfirst($request->status) . '</p>' .
                                       ($request->cancellation_reason ? '<p><strong>Reason:</strong> ' . e($request->cancellation_reason) . '</p>' : '');
            $activityLog->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    /**
     * Update consultant assignment
     */
    public function updateConsultant(Request $request, $id)
    {
        $appointment = BookingAppointment::findOrFail($id);
        
        $request->validate([
            'consultant_id' => 'required|exists:appointment_consultants,id'
        ]);

        $oldConsultantId = $appointment->consultant_id;
        $appointment->consultant_id = $request->consultant_id;
        $appointment->assigned_by_admin_id = Auth::id();
        $appointment->save();

        // Log activity using existing codebase pattern (only if client exists)
        if ($appointment->client_id) {
            $consultant = AppointmentConsultant::find($request->consultant_id);
            $activityLog = new ActivitiesLog;
            $activityLog->client_id = $appointment->client_id;
            $activityLog->created_by = Auth::id();
            $activityLog->subject = 'Booking appointment consultant reassigned';
            $activityLog->description = '<p><strong>Consultant assigned:</strong> ' . ($consultant ? e($consultant->name) : 'N/A') . '</p>';
            $activityLog->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Consultant assigned successfully'
        ]);
    }

    /**
     * Add admin note
     */
    public function addNote(Request $request, $id)
    {
        $appointment = BookingAppointment::findOrFail($id);
        
        $request->validate([
            'note' => 'required|string|max:2000'
        ]);

        $timestamp = now()->format('Y-m-d H:i');
        $adminName = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $newNote = "[{$timestamp} - {$adminName}]\n" . $request->note;

        $appointment->admin_notes = $appointment->admin_notes 
            ? $appointment->admin_notes . "\n\n" . $newNote
            : $newNote;
        
        $appointment->save();

        // Log activity using existing codebase pattern
        if ($appointment->client_id) {
            $activityLog = new ActivitiesLog;
            $activityLog->client_id = $appointment->client_id;
            $activityLog->created_by = Auth::id();
            $activityLog->subject = 'Note added to booking appointment';
            $activityLog->description = '<p>' . e($request->note) . '</p>';
            $activityLog->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Note added successfully',
            'notes' => $appointment->admin_notes
        ]);
    }

    /**
     * Sync dashboard
     */
    public function syncDashboard()
    {
        // Get sync logs with pagination
        $syncLogs = AppointmentSyncLog::orderBy('created_at', 'desc')->paginate(20);
        
        // Get last successful sync
        $lastSync = AppointmentSyncLog::where('status', 'success')
            ->latest('created_at')
            ->first();
        
        // Determine system status
        $lastLog = AppointmentSyncLog::latest('created_at')->first();
        $systemStatus = [
            'status' => 'success',
            'message' => 'All systems operational'
        ];
        
        if ($lastLog) {
            if ($lastLog->status === 'failed') {
                $systemStatus = [
                    'status' => 'error',
                    'message' => 'Last sync failed: ' . ($lastLog->error_message ?? 'Unknown error')
                ];
            } elseif ($lastLog->status === 'running') {
                $systemStatus = [
                    'status' => 'running',
                    'message' => 'Sync currently in progress'
                ];
            }
        }
        
        // Calculate next sync time (every 10 minutes)
        $nextSync = $lastSync ? $lastSync->created_at->addMinutes(10)->diffForHumans() : 'Within 10 minutes';
        
        // Calculate statistics
        $totalSyncs = AppointmentSyncLog::where('status', 'success')->count();
        $failedSyncs = AppointmentSyncLog::where('status', 'failed')->count();
        $totalAttempts = $totalSyncs + $failedSyncs;
        $successRate = $totalAttempts > 0 ? round(($totalSyncs / $totalAttempts) * 100) : 100;
        
        $stats = [
            'total_synced' => BookingAppointment::count(),
            'today' => AppointmentSyncLog::whereDate('created_at', today())->count(),
            'failed' => $failedSyncs,
            'success_rate' => $successRate,
        ];

        return view('Admin.booking.sync.dashboard', compact('syncLogs', 'systemStatus', 'lastSync', 'nextSync', 'stats'));
    }

    /**
     * Manual sync trigger (admin only)
     */
    public function manualSync(Request $request)
    {
        // Check authorization using Gate
        if (!Gate::allows('trigger-manual-sync')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            $minutes = $request->input('minutes', 60);
            $stats = $this->syncService->syncRecentAppointments($minutes);

            // Log activity - no need to log this as it's already logged in AppointmentSyncLog

            return response()->json([
                'success' => true,
                'message' => 'Sync completed successfully',
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get appointment details as JSON (for modal/AJAX)
     */
    public function getAppointmentJson($id)
    {
        $appointment = BookingAppointment::with(['client', 'consultant', 'assignedBy'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $appointment->id,
                'bansal_appointment_id' => $appointment->bansal_appointment_id,
                'client_id' => $appointment->client_id,
                'client_name' => $appointment->client_name,
                'client_email' => $appointment->client_email,
                'client_phone' => $appointment->client_phone,
                'appointment_datetime' => $appointment->appointment_datetime,
                'formatted_date' => $appointment->formatted_date,
                'formatted_time' => $appointment->formatted_time,
                'timeslot_full' => $appointment->timeslot_full,
                'location' => $appointment->location,
                'location_display' => $appointment->location_display,
                'full_address' => $appointment->full_address,
                'service_type' => $appointment->service_type,
                'enquiry_type' => $appointment->enquiry_type,
                'enquiry_details' => $appointment->enquiry_details,
                'meeting_type' => $appointment->meeting_type,
                'status' => $appointment->status,
                'status_badge' => $appointment->status_badge,
                'is_paid' => $appointment->is_paid,
                'final_amount' => $appointment->final_amount,
                'payment_status' => $appointment->payment_status,
                'payment_method' => $appointment->payment_method,
                'paid_at' => $appointment->paid_at?->format('d/m/Y h:i A'),
                'promo_code' => $appointment->promo_code,
                'admin_notes' => $appointment->admin_notes,
                'consultant' => $appointment->consultant ? [
                    'id' => $appointment->consultant->id,
                    'name' => $appointment->consultant->name,
                    'calendar_type' => $appointment->consultant->calendar_type,
                ] : null,
                'synced_from_bansal_at' => $appointment->synced_from_bansal_at?->format('d/m/Y h:i A'),
                'updated_at' => $appointment->updated_at?->format('d/m/Y h:i A'),
            ]
        ]);
    }

    /**
     * Update follow-up settings
     */
    public function updateFollowUp(Request $request, $id)
    {
        $appointment = BookingAppointment::findOrFail($id);
        
        $request->validate([
            'follow_up_required' => 'required|boolean',
            'follow_up_date' => 'nullable|date'
        ]);

        $appointment->follow_up_required = $request->follow_up_required;
        $appointment->follow_up_date = $request->follow_up_date;
        $appointment->save();

        return response()->json([
            'success' => true,
            'message' => 'Follow-up settings updated'
        ]);
    }

    /**
     * Send reminder manually
     */
    public function sendReminder(Request $request, $id)
    {
        $appointment = BookingAppointment::findOrFail($id);
        
        $notificationService = app(\App\Services\BansalAppointmentSync\NotificationService::class);
        
        $request->validate([
            'type' => 'required|in:email,sms,both'
        ]);

        $results = [
            'email' => null,
            'sms' => null
        ];

        if (in_array($request->type, ['email', 'both'])) {
            $results['email'] = $notificationService->sendDetailedConfirmationEmail($appointment);
        }

        if (in_array($request->type, ['sms', 'both'])) {
            $results['sms'] = $notificationService->sendReminderSms($appointment);
        }

        $success = ($results['email'] !== false && $results['sms'] !== false);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Reminder sent successfully' : 'Failed to send reminder',
            'results' => $results
        ]);
    }

    /**
     * Get sync statistics
     */
    public function syncStats()
    {
        $stats = [
            'today' => [
                'syncs' => AppointmentSyncLog::today()->count(),
                'successful' => AppointmentSyncLog::today()->success()->count(),
                'failed' => AppointmentSyncLog::today()->failed()->count(),
                'appointments_synced' => AppointmentSyncLog::today()->sum('appointments_new'),
            ],
            'last_24h' => [
                'appointments' => BookingAppointment::where('created_at', '>=', now()->subDay())->count(),
                'pending' => BookingAppointment::where('created_at', '>=', now()->subDay())->where('status', 'pending')->count(),
            ],
            'last_sync' => AppointmentSyncLog::latest('started_at')->first(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Export appointments to CSV
     */
    public function export(Request $request)
    {
        $query = BookingAppointment::with(['client', 'consultant']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('consultant_id')) {
            $query->where('consultant_id', $request->consultant_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('appointment_datetime', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('appointment_datetime', '<=', $request->date_to);
        }

        $appointments = $query->orderBy('appointment_datetime', 'desc')->get();

        $filename = 'booking_appointments_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($appointments) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'ID',
                'Bansal ID',
                'Client Name',
                'Email',
                'Phone',
                'Appointment Date',
                'Time',
                'Location',
                'Service Type',
                'Consultant',
                'Status',
                'Payment Status',
                'Amount',
                'Synced At'
            ]);

            // Data
            foreach ($appointments as $apt) {
                fputcsv($file, [
                    $apt->id,
                    $apt->bansal_appointment_id,
                    $apt->client_name,
                    $apt->client_email,
                    $apt->client_phone,
                    $apt->appointment_datetime->format('d/m/Y'),
                    $apt->timeslot_full ?? $apt->appointment_datetime->format('h:i A'),
                    $apt->location,
                    $apt->service_type,
                    $apt->consultant?->name ?? 'Unassigned',
                    $apt->status,
                    $apt->is_paid ? 'Paid' : 'Free',
                    $apt->is_paid ? $apt->final_amount : '0.00',
                    $apt->synced_from_bansal_at?->format('d/m/Y h:i A')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'appointment_ids' => 'required|array',
            'appointment_ids.*' => 'exists:booking_appointments,id',
            'status' => 'required|in:pending,confirmed,completed,cancelled,no_show'
        ]);

        $updated = 0;
        
        foreach ($request->appointment_ids as $id) {
            $appointment = BookingAppointment::find($id);
            if ($appointment) {
                $appointment->status = $request->status;
                
                if ($request->status === 'confirmed') {
                    $appointment->confirmed_at = now();
                } elseif ($request->status === 'completed') {
                    $appointment->completed_at = now();
                } elseif ($request->status === 'cancelled') {
                    $appointment->cancelled_at = now();
                }
                
                $appointment->save();
                $updated++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Updated {$updated} appointments"
        ]);
    }
}


<?php
namespace App\Services;

use App\Models\ClientMatter;
use App\Models\Note;
use App\Models\Notification;
use App\Models\CheckinLog;
use App\Models\Admin;
use App\Models\ClientVisaCountry;
use App\Models\ActivitiesLog;
use App\Models\WorkflowStage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get all dashboard data
     */
    public function getDashboardData($request): array
    {
        $user = Auth::user();
        
        return [
            'data' => $this->getClientMatters($request, $user),
            'notesData' => $this->getNotesData($user),
            'cases_requiring_attention_data' => $this->getCasesRequiringAttention($user),
            'count_active_matter' => $this->getActiveMatterCount(),
            'count_note_deadline' => $this->getNoteDeadlineCount($user),
            'count_cases_requiring_attention_data' => $this->getCasesRequiringAttentionCount($user),
            'filters' => [
                'client_name' => $request->client_name ?? '',
                'client_stage' => $request->client_stage ?? ''
            ],
            'visibleColumns' => $this->getVisibleColumns(),
            'workflowStages' => $this->getWorkflowStages(),
            'assignee' => $this->getAssignees()
        ];
    }

    /**
     * Get client matters with proper relationships
     */
    private function getClientMatters($request, $user)
    {
        // Load all relationships without column restrictions
        // Column restrictions can prevent relationships from loading if data doesn't match exactly
        $query = ClientMatter::with([
            'client',           // Load full client record
            'migrationAgent',  // Load full migration agent record
            'personResponsible', // Load full person responsible record
            'personAssisting',  // Load full person assisting record
            'workflowStage',    // Load workflow stage
            'matter'            // Load matter type
        ]);

        // Apply role-based filtering
        $this->applyRoleBasedFiltering($query, $user);

        // Apply client name filter
        if ($request->has('client_name') && !empty($request->client_name)) {
            $clientName = trim($request->client_name);
            $clientNameLower = strtolower($clientName);
            $query->whereHas('client', function ($q) use ($clientName, $clientNameLower) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ['%' . $clientNameLower . '%'])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . $clientNameLower . '%'])
                  ->orWhereRaw("LOWER(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) LIKE ?", ['%' . $clientNameLower . '%'])
                  ->orWhereRaw('LOWER(client_id) LIKE ?', ['%' . $clientNameLower . '%']);
            });
        }

        // Apply stage filter
        if ($request->has('client_stage') && !empty($request->client_stage)) {
            $query->where('workflow_stage_id', $request->client_stage);
        } else {
            $query->where('workflow_stage_id', '!=', 14);
        }

        return $query->orderBy('updated_at', 'DESC')->paginate(10);
    }

    /**
     * Get all actions (notes with folloup = 1) for the user
     * Shows actions with deadlines first (ordered by urgency), then actions without deadlines
     */
    private function getNotesData($user)
    {
        $query = Note::with(['client:id,first_name,last_name,client_id', 'assignedUser:id,first_name,last_name'])
            ->where('type', 'client')
            ->where('folloup', 1)
            ->whereNotNull('client_id')
            ->whereNotNull('unique_group_id')
            ->where('status', '!=', 1)
            ->whereHas('client'); // Ensure client relationship exists

        // Admin sees ALL actions (no assigned_to filter) - matching action page behavior
        // Other roles only see notes assigned to them
        if ($user->role != 1) {
            $query->where('assigned_to', $user->id);
        }

        // Order: Actions with deadlines first (by deadline ASC), then actions without deadlines (by created_at DESC)
        return $query->orderByRaw('CASE WHEN note_deadline IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('note_deadline', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->limit(6) // Show only 6 most recent/urgent actions
            ->get();
    }

    /**
     * Get cases requiring attention
     */
    private function getCasesRequiringAttention($user)
    {
        $query = ClientMatter::with([
                'client:id,first_name,last_name,client_id',
                'matter:id,title',
                'personResponsible:id,first_name,last_name'
            ])
            ->where('matter_status', 1)
            ->where('updated_at', '>=', Carbon::now()->subDays(100));

        // Apply role-based filtering
        $this->applyRoleBasedFiltering($query, $user);

        $cases = $query->orderByDesc('updated_at')
            ->limit(50) // Limit to 50 most recent cases to avoid timeout
            ->get();
        
        // Enrich only the first 20 cases with activity (those displayed on dashboard)
        // This prevents timeout when there are thousands of cases
        foreach ($cases->take(20) as $case) {
            $case->latest_activity = $this->getLatestActivity($case);
        }
        
        // Set default activity for remaining cases
        foreach ($cases->slice(20) as $case) {
            $case->latest_activity = [
                'type' => 'default',
                'date' => $case->updated_at
            ];
        }

        return $cases;
    }

    /**
     * Get the latest activity for a case
     * Optimized to check only the most relevant activity sources
     */
    private function getLatestActivity($case)
    {
        $activities = [];
        $clientId = $case->client_id;
        
        try {
            // Use a single optimized query to get the most recent activity from each source
            // This is much faster than multiple separate queries
            
            // Check activities_log first (most common source)
            $latestActivityLog = ActivitiesLog::where('client_id', $clientId)
                ->select('subject', 'created_at')
                ->latest('created_at')
                ->first();
            
            if ($latestActivityLog) {
                $subject = strtolower($latestActivityLog->subject ?? '');
                $type = 'default';
                
                if (str_contains($subject, 'stage') || str_contains($subject, 'workflow')) {
                    $type = 'stage_updated';
                } elseif (str_contains($subject, 'status')) {
                    $type = 'status_changed';
                } elseif (str_contains($subject, 'appointment') || str_contains($subject, 'meeting')) {
                    $type = 'appointment_scheduled';
                } elseif (str_contains($subject, 'payment') || str_contains($subject, 'invoice')) {
                    $type = 'payment_received';
                } elseif (str_contains($subject, 'note')) {
                    $type = 'note_added';
                } elseif (str_contains($subject, 'email')) {
                    $type = 'email_sent';
                } elseif (str_contains($subject, 'document') || str_contains($subject, 'upload')) {
                    $type = 'document_uploaded';
                } elseif (str_contains($subject, 'sign')) {
                    $type = 'signed';
                }
                
                return [
                    'type' => $type,
                    'date' => $latestActivityLog->created_at
                ];
            }
            
            // Fallback to case update time
            return [
                'type' => 'default',
                'date' => $case->updated_at
            ];
            
        } catch (\Exception $e) {
            Log::debug('Error fetching activity: ' . $e->getMessage());
            return [
                'type' => 'default',
                'date' => $case->updated_at
            ];
        }
    }

    /**
     * Apply role-based filtering to queries
     */
    private function applyRoleBasedFiltering($query, $user)
    {
        switch ($user->role) {
            case 16: // Migration Agent
                $query->where('client_matters.sel_migration_agent', $user->id);
                break;
            case 12: // Person Responsible
                $query->where('client_matters.sel_person_responsible', $user->id);
                break;
            case 13: // Person Assisting
                $query->where('client_matters.sel_person_assisting', $user->id);
                break;
            // Role 1 (Admin) sees all data
        }
    }

    /**
     * Get active matter count with caching
     */
    private function getActiveMatterCount(): int
    {
        return Cache::remember('active_matter_count', 300, function () {
            return ClientMatter::where('matter_status', 1)->count();
        });
    }

    /**
     * Get note deadline count (all actions count)
     */
    private function getNoteDeadlineCount($user): int
    {
        $query = Note::where('type', 'client')
            ->where('folloup', 1)
            ->whereNotNull('client_id')
            ->whereNotNull('unique_group_id')
            ->where('status', '!=', 1);

        // Admin sees ALL actions (no assigned_to filter) - matching action page behavior
        if ($user->role != 1) {
            $query->where('assigned_to', $user->id);
        }

        return $query->count();
    }

    /**
     * Get cases requiring attention count
     */
    private function getCasesRequiringAttentionCount($user): int
    {
        $query = ClientMatter::join('admins as clients', 'client_matters.client_id', '=', 'clients.id')
            ->where('client_matters.matter_status', 1)
            ->where('client_matters.updated_at', '>=', Carbon::now()->subDays(100));

        $this->applyRoleBasedFiltering($query, $user);

        return $query->count();
    }

    /**
     * Get visible columns from session
     */
    private function getVisibleColumns(): array
    {
        $defaultColumns = [
            'matter', 'client_id', 'client_name', 'dob', 
            'migration_agent', 'person_responsible', 
            'person_assisting', 'stage'
        ];

        return session('dashboard_column_preferences', $defaultColumns);
    }

    /**
     * Get workflow stages
     */
    private function getWorkflowStages()
    {
        return Cache::remember('workflow_stages', 3600, function () {
            return WorkflowStage::orderBy('id', 'ASC')
                ->get();
        });
    }

    /**
     * Get assignees for action creation
     */
    private function getAssignees()
    {
        return Admin::select('id', 'first_name', 'email')
            ->where('role', '!=', 1)
            ->get();
    }

    /**
     * Save column preferences
     */
    public function saveColumnPreferences($request): void
    {
        $visibleColumns = $request->input('visible_columns', []);
        
        $validColumns = [
            'matter', 'client_id', 'client_name', 'dob', 
            'migration_agent', 'person_responsible', 
            'person_assisting', 'stage'
        ];
        
        $filteredColumns = array_intersect($visibleColumns, $validColumns);
        
        session(['dashboard_column_preferences' => $filteredColumns]);
    }

    /**
     * Update client matter stage
     */
    public function updateClientMatterStage($itemId, $stageId): array
    {
        $item = ClientMatter::find($itemId);
        
        if (!$item) {
            return ['success' => false, 'message' => 'Matter not found!'];
        }

        $item->workflow_stage_id = $stageId;
        $item->save();

        return ['success' => true, 'message' => 'Matter stage updated successfully!'];
    }

    /**
     * Get notifications
     */
    public function getNotifications(): array
    {
        $count = Notification::where('receiver_id', Auth::id())
            ->where('receiver_status', 0)
            ->count();

        return ['count' => $count];
    }

    /**
     * Get office visit notifications
     */
    public function getOfficeVisitNotifications(): array
    {
        $notifications = Notification::with(['sender:id,first_name,last_name'])
            ->where('receiver_id', Auth::id())
            ->where('notification_type', 'officevisit')
            ->where('receiver_status', 0)
            ->orderBy('created_at', 'DESC')
            ->get();

        $data = [];
        foreach ($notifications as $notification) {
            $checkinLog = CheckinLog::find($notification->module_id);
            
            if (!$checkinLog || $checkinLog->status != 0) {
                continue;
            }

            // Get client information
            $client = $checkinLog->contact_type == 'Lead' 
                ? \App\Models\Lead::find($checkinLog->client_id)
                : Admin::where('role', '7')->find($checkinLog->client_id);

            $data[] = [
                'id' => $notification->id,
                'checkin_id' => $checkinLog->id,
                'message' => $notification->message,
                'sender_name' => $notification->sender 
                    ? $notification->sender->first_name . ' ' . $notification->sender->last_name 
                    : 'System',
                'client_name' => $client 
                    ? $client->first_name . ' ' . $client->last_name 
                    : 'Unknown Client',
                'visit_purpose' => $checkinLog->visit_purpose,
                'created_at' => $notification->created_at->format('d/m/Y h:i A'),
                'url' => $notification->url
            ];
        }

        return $data;
    }

    /**
     * Mark notification as seen
     */
    public function markNotificationAsSeen($notificationId): array
    {
        $notification = Notification::find($notificationId);
        
        if (!$notification || $notification->receiver_id != Auth::id()) {
            return ['status' => 'error'];
        }

        $notification->receiver_status = 1;
        $notification->save();

        return ['status' => 'success'];
    }

    /**
     * Extend note deadline
     */
    public function extendNoteDeadline($data): array
    {
        try {
            $notes = Note::where('unique_group_id', $data['unique_group_id'])
                ->whereNotNull('unique_group_id')
                ->get();

            if ($notes->isEmpty()) {
                return ['success' => false, 'message' => 'No notes found with the provided unique group ID'];
            }

            $updated = Note::where('unique_group_id', $data['unique_group_id'])
                ->whereNotNull('unique_group_id')
                ->update([
                    'description' => $data['description'],
                    'note_deadline' => $data['note_deadline'],
                    'user_id' => Auth::id()
                ]);

            if ($updated > 0) {
                // Create notification and activity log for the first note
                $firstNote = $notes->first();
                $this->createNotificationAndActivityLog($firstNote);

                return [
                    'success' => true, 
                    'message' => 'Successfully updated', 
                    'clientID' => $firstNote->client_id
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to update notes'];
            }
        } catch (\Exception $e) {
            Log::error('Error extending note deadline: ' . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while extending the deadline'];
        }
    }

    /**
     * Update action completion status
     */
    public function updateActionCompleted($noteId, $uniqueGroupId): array
    {
        $updated = Note::where('id', $noteId)
            ->where('unique_group_id', $uniqueGroupId)
            ->update(['status' => 1]);

        return $updated 
            ? ['success' => true, 'message' => 'Action completed successfully']
            : ['success' => false, 'message' => 'Failed to complete action'];
    }

    /**
     * Get visa expiry message
     */
    public function getVisaExpiryMessage($clientId): string
    {
        $visaInfo = ClientVisaCountry::where('client_id', $clientId)
            ->latest('id')
            ->first();

        if (!$visaInfo || !$visaInfo->visa_expiry_date) {
            return '';
        }

        $visaExpiredAt = Carbon::parse($visaInfo->visa_expiry_date);
        $today = Carbon::now();
        $sevenDaysFromNow = Carbon::now()->addDays(7);

        if ($visaExpiredAt->lt($today)) {
            return 'Your visa is expired';
        } elseif ($visaExpiredAt->gte($today) && $visaExpiredAt->lte($sevenDaysFromNow)) {
            $daysRemaining = $visaExpiredAt->diffInDays($today);
            return "Your visa is expiring in next $daysRemaining day" . ($daysRemaining == 1 ? '' : 's');
        }

        return '';
    }

    /**
     * Create notification and activity log
     */
    private function createNotificationAndActivityLog($note): void
    {
        try {
            // Create notification only if assigned_to exists
            if ($note->assigned_to) {
                Notification::create([
                    'sender_id' => Auth::id(),
                    'receiver_id' => $note->assigned_to,
                    'module_id' => $note->client_id,
                    'url' => url('/clients/detail/' . $note->client_id),
                    'notification_type' => 'client',
                    'message' => 'Action Extended by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name . ' on ' . date('d/M/Y h:i A')
                ]);
            }

            // Create activity log
            ActivitiesLog::create([
                'client_id' => $note->client_id,
                'created_by' => Auth::id(),
                'subject' => 'Extended Note Deadline',
                'description' => '<span class="text-semi-bold">' . ($note->title ?? 'Note') . '</span><p>' . ($note->description ?? '') . '</p>',
                'use_for' => Auth::id() != $note->user_id ? $note->user_id : '',
                'followup_date' => $note->followup_date ?? null,
                'task_group' => $note->task_group ?? null,
                'task_status' => 0,
                'pin' => 0,
            ]);
        } catch (\Exception $e) {
            // Log the error but don't break the main functionality
            Log::error('Error creating notification/activity log: ' . $e->getMessage());
        }
    }
}

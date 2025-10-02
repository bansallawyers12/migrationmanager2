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
        $query = ClientMatter::with([
            'client:id,first_name,last_name,dob,client_id',
            'migrationAgent:id,first_name,last_name',
            'personResponsible:id,first_name,last_name',
            'personAssisting:id,first_name,last_name',
            'workflowStage:id,name'
        ]);

        // Apply role-based filtering
        $this->applyRoleBasedFiltering($query, $user);

        // Apply client name filter
        if ($request->has('client_name') && !empty($request->client_name)) {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->client_name . '%')
                  ->orWhere('last_name', 'like', '%' . $request->client_name . '%')
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$request->client_name}%"])
                  ->orWhere('client_id', $request->client_name);
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
     * Get notes data with proper relationships
     */
    private function getNotesData($user)
    {
        $query = Note::with(['client:id,first_name,last_name,client_id'])
            ->whereNotNull('note_deadline')
            ->where('status', '!=', 1);

        if ($user->role == 1) {
            // Admin sees all notes
        } else {
            $query->where('assigned_to', $user->id);
        }

        return $query->orderBy('note_deadline', 'DESC')->get();
    }

    /**
     * Get cases requiring attention
     */
    private function getCasesRequiringAttention($user)
    {
        $query = ClientMatter::with([
                'client:id,first_name,last_name,client_id',
                'matter:id,title'
            ])
            ->where('matter_status', 1)
            ->where('updated_at', '>=', Carbon::now()->subDays(100));

        // Apply role-based filtering
        $this->applyRoleBasedFiltering($query, $user);

        return $query->orderByDesc('updated_at')
            ->get();
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
     * Get note deadline count
     */
    private function getNoteDeadlineCount($user): int
    {
        $query = Note::whereNotNull('note_deadline')->where('status', '!=', 1);

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
            'person_assisting', 'stage', 'action'
        ];

        return session('dashboard_column_preferences', $defaultColumns);
    }

    /**
     * Get workflow stages
     */
    private function getWorkflowStages()
    {
        return Cache::remember('workflow_stages', 3600, function () {
            return WorkflowStage::where('id', '!=', '')
                ->orderBy('id', 'ASC')
                ->get();
        });
    }

    /**
     * Get assignees for task creation
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
            'person_assisting', 'stage', 'action'
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
        $notes = Note::where('unique_group_id', $data['unique_group_id'])
            ->whereNotNull('assigned_to')
            ->whereNotNull('unique_group_id')
            ->get();

        if ($notes->isEmpty()) {
            return ['success' => false, 'message' => 'No notes found'];
        }

        foreach ($notes as $note) {
            Note::where('unique_group_id', $note->unique_group_id)
                ->whereNotNull('assigned_to')
                ->whereNotNull('unique_group_id')
                ->update([
                    'description' => $data['description'],
                    'note_deadline' => $data['note_deadline'],
                    'user_id' => Auth::id()
                ]);

            // Create notification and activity log
            $this->createNotificationAndActivityLog($note);
        }

        return [
            'success' => true, 
            'message' => 'Successfully updated', 
            'clientID' => $notes->first()->client_id
        ];
    }

    /**
     * Update task completion status
     */
    public function updateTaskCompleted($noteId, $uniqueGroupId): array
    {
        $updated = Note::where('id', $noteId)
            ->where('unique_group_id', $uniqueGroupId)
            ->update(['status' => 1]);

        return $updated 
            ? ['success' => true, 'message' => 'Task completed successfully']
            : ['success' => false, 'message' => 'Failed to complete task'];
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
        // Create notification
        Notification::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $note->assigned_to,
            'module_id' => $note->client_id,
            'url' => url('/admin/clients/detail/' . $note->client_id),
            'notification_type' => 'client',
            'message' => 'Followup Assigned by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name . ' on ' . date('d/M/Y h:i A', strtotime($note->followup_date))
        ]);

        // Create activity log
        ActivitiesLog::create([
            'client_id' => $note->client_id,
            'created_by' => Auth::id(),
            'subject' => 'Extended Note Deadline',
            'description' => '<span class="text-semi-bold">' . $note->title . '</span><p>' . $note->description . '</p>',
            'use_for' => Auth::id() != $note->user_id ? $note->user_id : '',
            'followup_date' => $note->followup_date,
            'task_group' => $note->task_group
        ]);
    }
}

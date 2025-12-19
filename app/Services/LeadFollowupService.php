<?php

namespace App\Services;

use App\Models\LeadFollowup;
use App\Models\ActivitiesLog;
use Illuminate\Support\Facades\Auth;

class LeadFollowupService
{
    /**
     * Schedule a new follow-up
     */
    public function scheduleFollowup($leadId, array $data)
    {
        $followup = LeadFollowup::create([
            'lead_id' => $leadId,
            'assigned_to' => $data['assigned_to'],
            'created_by' => Auth::id(),
            'followup_type' => $data['type'],
            'priority' => $data['priority'] ?? 'medium',
            'scheduled_date' => $data['scheduled_date'],
            'notes' => $data['notes'] ?? null,
        ]);
        
        // Log activity
        ActivitiesLog::create([
            'client_id' => $leadId,
            'created_by' => Auth::id(),
            'subject' => "Follow-up scheduled: {$followup->followup_type}",
            'description' => "Scheduled for " . $followup->scheduled_date->format('d/m/Y H:i') . 
                           ". Priority: {$followup->priority}. " . 
                           ($data['notes'] ?? ''),
            'activity_type' => 'followup_scheduled',
            'task_status' => 0,
        ]);
        
        return $followup;
    }
    
    /**
     * Complete a follow-up and optionally schedule next one
     */
    public function completeFollowup($followupId, array $data)
    {
        $followup = LeadFollowup::findOrFail($followupId);
        
        $followup->update([
            'status' => 'completed',
            'completed_at' => now(),
            'outcome' => $data['outcome'],
            'notes' => ($followup->notes ?? '') . "\n\n[Outcome - " . now()->format('d/m/Y H:i') . "]: " . ($data['notes'] ?? ''),
        ]);
        
        // Auto-schedule next follow-up based on outcome
        if (isset($data['schedule_next']) && $data['schedule_next']) {
            $daysUntilNext = match($data['outcome']) {
                'interested' => 3,
                'callback_later' => 7,
                'not_interested' => null,
                'no_response' => 2,
                default => 7
            };
            
            if ($daysUntilNext) {
                $nextFollowup = $this->autoScheduleNextFollowup($followup, $daysUntilNext);
                $followup->update(['next_followup_date' => $nextFollowup->scheduled_date]);
            }
        }
        
        // Log activity
        ActivitiesLog::create([
            'client_id' => $followup->lead_id,
            'created_by' => Auth::id(),
            'subject' => "Follow-up completed: {$followup->followup_type}",
            'description' => "Outcome: {$data['outcome']}. " . ($data['notes'] ?? ''),
            'activity_type' => 'followup_completed',
            'task_status' => 0,
        ]);
        
        return $followup;
    }
    
    /**
     * Auto-schedule next follow-up
     */
    protected function autoScheduleNextFollowup(LeadFollowup $previousFollowup, int $daysFromNow)
    {
        $nextFollowup = LeadFollowup::create([
            'lead_id' => $previousFollowup->lead_id,
            'assigned_to' => $previousFollowup->assigned_to,
            'created_by' => Auth::id(),
            'followup_type' => $previousFollowup->followup_type,
            'priority' => $previousFollowup->priority,
            'scheduled_date' => now()->addDays($daysFromNow),
            'notes' => "Auto-scheduled follow-up from previous " . $previousFollowup->followup_type,
        ]);
        
        // Log activity
        ActivitiesLog::create([
            'client_id' => $previousFollowup->lead_id,
            'created_by' => Auth::id(),
            'subject' => "Follow-up auto-scheduled: {$nextFollowup->followup_type}",
            'description' => "Scheduled for " . $nextFollowup->scheduled_date->format('d/m/Y H:i') . " (in {$daysFromNow} days)",
            'activity_type' => 'followup_scheduled',
            'task_status' => 0,
        ]);
        
        return $nextFollowup;
    }
    
    /**
     * Reschedule a follow-up
     */
    public function rescheduleFollowup($followupId, $newDate, $reason = null)
    {
        $followup = LeadFollowup::findOrFail($followupId);
        $oldDate = $followup->scheduled_date;
        
        $followup->update([
            'status' => 'rescheduled',
            'scheduled_date' => $newDate,
            'reminder_sent' => false,
            'notes' => ($followup->notes ?? '') . "\n\n[Rescheduled - " . now()->format('d/m/Y H:i') . "]: " .
                      "From " . $oldDate->format('d/m/Y H:i') . " to " . $newDate->format('d/m/Y H:i') . 
                      ($reason ? ". Reason: " . $reason : ''),
        ]);
        
        // Create new pending follow-up
        $newFollowup = LeadFollowup::create([
            'lead_id' => $followup->lead_id,
            'assigned_to' => $followup->assigned_to,
            'created_by' => Auth::id(),
            'followup_type' => $followup->followup_type,
            'priority' => $followup->priority,
            'scheduled_date' => $newDate,
            'notes' => "Rescheduled from " . $oldDate->format('d/m/Y H:i') . 
                      ($reason ? ". Reason: " . $reason : ''),
        ]);
        
        // Log activity
        ActivitiesLog::create([
            'client_id' => $followup->lead_id,
            'created_by' => Auth::id(),
            'subject' => "Follow-up rescheduled",
            'description' => "From " . $oldDate->format('d/m/Y H:i') . " to " . $newDate->format('d/m/Y H:i'),
            'activity_type' => 'followup_rescheduled',
            'task_status' => 0,
        ]);
        
        return $newFollowup;
    }
    
    /**
     * Cancel a follow-up
     */
    public function cancelFollowup($followupId, $reason = null)
    {
        $followup = LeadFollowup::findOrFail($followupId);
        
        $followup->update([
            'status' => 'cancelled',
            'notes' => ($followup->notes ?? '') . "\n\n[Cancelled - " . now()->format('d/m/Y H:i') . "]: " .
                      ($reason ?? 'No reason provided'),
        ]);
        
        // Log activity
        ActivitiesLog::create([
            'client_id' => $followup->lead_id,
            'created_by' => Auth::id(),
            'subject' => "Follow-up cancelled",
            'description' => $reason ?? 'No reason provided',
            'activity_type' => 'followup_cancelled',
            'task_status' => 0,
        ]);
        
        return $followup;
    }
    
    /**
     * Send reminders for upcoming follow-ups
     */
    public function sendReminders()
    {
        // Get follow-ups due in next hour that haven't had reminders sent
        $upcoming = LeadFollowup::where('status', 'pending')
            ->where('reminder_sent', false)
            ->whereBetween('scheduled_date', [now(), now()->addHour()])
            ->with(['lead', 'assignedAgent'])
            ->get();
            
        $remindersSent = 0;
        
        foreach ($upcoming as $followup) {
            // Browser notification via broadcasting (to be implemented)
            // event(new FollowupReminderEvent($followup));
            
            $followup->update([
                'reminder_sent' => true,
                'reminder_sent_at' => now(),
            ]);
            
            $remindersSent++;
        }
        
        return $remindersSent;
    }
    
    /**
     * Mark overdue follow-ups
     */
    public function markOverdueFollowups()
    {
        $overdue = LeadFollowup::where('status', 'pending')
            ->where('scheduled_date', '<', now())
            ->update(['status' => 'overdue']);
        
        return $overdue;
    }
    
    /**
     * Get follow-up statistics for a lead
     */
    public function getLeadStats($leadId)
    {
        return [
            'total' => LeadFollowup::where('lead_id', $leadId)->count(),
            'pending' => LeadFollowup::where('lead_id', $leadId)->where('status', 'pending')->count(),
            'completed' => LeadFollowup::where('lead_id', $leadId)->where('status', 'completed')->count(),
            'overdue' => LeadFollowup::where('lead_id', $leadId)->where('status', 'overdue')->count(),
            'last_followup' => LeadFollowup::where('lead_id', $leadId)
                ->where('status', 'completed')
                ->orderBy('completed_at', 'desc')
                ->first(),
            'next_followup' => LeadFollowup::where('lead_id', $leadId)
                ->where('status', 'pending')
                ->orderBy('scheduled_date', 'asc')
                ->first(),
        ];
    }
    
    /**
     * Get agent statistics
     */
    public function getAgentStats($agentId)
    {
        return [
            'today' => LeadFollowup::forAgent($agentId)->dueToday()->count(),
            'overdue' => LeadFollowup::forAgent($agentId)->overdue()->count(),
            'upcoming' => LeadFollowup::forAgent($agentId)->upcoming()->count(),
            'completed_this_week' => LeadFollowup::forAgent($agentId)
                ->where('status', 'completed')
                ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'completion_rate' => $this->getCompletionRate($agentId),
        ];
    }
    
    /**
     * Calculate completion rate for an agent
     */
    protected function getCompletionRate($agentId)
    {
        $total = LeadFollowup::forAgent($agentId)->count();
        if ($total === 0) return 0;
        
        $completed = LeadFollowup::forAgent($agentId)->where('status', 'completed')->count();
        
        return round(($completed / $total) * 100, 2);
    }
}


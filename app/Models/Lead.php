<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;

class Lead extends Admin
{
    use Notifiable, Sortable;
    
    // Use the same table as Admin
    protected $table = 'admins';
    
    // Lead-specific sortable columns
    public $sortable = [
        'id', 
        'first_name', 
        'last_name', 
        'email', 
        'phone',
        'lead_quality',
        'status',
        'created_at', 
        'updated_at'
    ];
    
    /**
     * Boot method to add global scopes
     */
    protected static function booted()
    {
        // Automatically filter all queries to leads only
        static::addGlobalScope('lead', function (Builder $builder) {
            $builder->where('role', 7)
                    ->where('type', 'lead')
                    ->whereNull('is_deleted');
        });
        
        // Automatically set type and role when creating a new lead
        static::creating(function ($lead) {
            $lead->type = 'lead';
            $lead->role = 7;
            if (!isset($lead->is_archived)) {
                $lead->is_archived = 0;
            }
        });
    }
    
    /**
     * Include archived leads in query
     * Usage: Lead::withArchived()->get()
     */
    public function scopeWithArchived(Builder $query)
    {
        return $query->withoutGlobalScope('lead')
                    ->where('role', 7)
                    ->where('type', 'lead')
                    ->whereNull('is_deleted');
    }
    
    /**
     * Get only archived leads
     * Usage: Lead::onlyArchived()->get()
     */
    public function scopeOnlyArchived(Builder $query)
    {
        return $query->withoutGlobalScope('lead')
                    ->where('role', 7)
                    ->where('type', 'lead')
                    ->where('is_archived', 1)
                    ->whereNull('is_deleted');
    }
    
    /**
     * Filter by lead quality
     * Usage: Lead::quality('hot')->get()
     */
    public function scopeQuality(Builder $query, $quality)
    {
        return $query->where('lead_quality', $quality);
    }
    
    /**
     * Filter by lead status
     * Usage: Lead::status('active')->get()
     */
    public function scopeStatus(Builder $query, $status)
    {
        return $query->where('status', $status);
    }
    
    /**
     * Filter by assigned agent
     * Usage: Lead::assignedTo($userId)->get()
     */
    public function scopeAssignedTo(Builder $query, $userId)
    {
        return $query->where('assignee', $userId);
    }
    
    /**
     * Filter by lead source
     * Usage: Lead::fromSource('website')->get()
     */
    public function scopeFromSource(Builder $query, $source)
    {
        return $query->where('source', $source);
    }
    
    /**
     * Get the assigned agent/staff member
     */
    public function assignedAgent()
    {
        return $this->belongsTo(Admin::class, 'assignee', 'id')
                    ->where('type', '!=', 'lead')
                    ->where('type', '!=', 'client');
    }
    
    /**
     * Get the user who created this lead
     */
    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'user_id', 'id');
    }
    
    /**
     * Assign lead to a user/agent
     */
    public function assignToUser($userId)
    {
        $this->assignee = $userId;
        return $this->save();
    }
    
    /**
     * Convert lead to client
     * Preserves follow-up history and logs conversion
     */
    public function convertToClient()
    {
        // Count existing follow-ups for activity log
        $followupCount = \App\Models\LeadFollowup::where('lead_id', $this->id)->count();
        $completedFollowups = \App\Models\LeadFollowup::where('lead_id', $this->id)
            ->where('status', 'completed')
            ->count();
        
        // Mark lead as converted
        $this->type = 'client';
        $this->lead_status = 'converted';
        $this->save();
        
        // Log the conversion in activities
        \App\Models\ActivitiesLog::create([
            'client_id' => $this->id,
            'created_by' => \Auth::id(),
            'subject' => 'Lead converted to Client',
            'description' => "Lead successfully converted to client. " .
                           "Total follow-ups: {$followupCount}, Completed: {$completedFollowups}. " .
                           "Follow-up history has been preserved.",
            'activity_type' => 'lead_converted',
        ]);
        
        // Mark all pending follow-ups with a note
        \App\Models\LeadFollowup::where('lead_id', $this->id)
            ->where('status', 'pending')
            ->update([
                'notes' => DB::raw("(COALESCE(notes, '') || '\n\n[" . now()->format('d/m/Y H:i') . 
                          "]: Lead converted to client. This follow-up has been preserved for client management.')"),
            ]);
        
        // Return as Admin model instance with client type
        return Admin::find($this->id);
    }
    
    /**
     * Archive this lead
     */
    public function archive()
    {
        $this->is_archived = 1;
        return $this->save();
    }
    
    /**
     * Unarchive this lead
     */
    public function unarchive()
    {
        $this->is_archived = 0;
        return $this->save();
    }
    
    /**
     * Soft delete (set is_deleted timestamp)
     */
    public function softDelete()
    {
        $this->is_deleted = now();
        return $this->save();
    }
    
    /**
     * Check if lead is archived
     */
    public function isArchived()
    {
        return $this->is_archived == 1;
    }
    
    /**
     * Check if lead has been assigned
     */
    public function isAssigned()
    {
        return !empty($this->assignee);
    }
    
    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
    
    /**
     * Get lead quality badge HTML (optional helper)
     */
    public function getQualityBadge()
    {
        $badges = [
            'hot' => '<span class="badge bg-danger">Hot</span>',
            'warm' => '<span class="badge bg-warning">Warm</span>',
            'cold' => '<span class="badge bg-info">Cold</span>',
        ];
        
        return $badges[$this->lead_quality] ?? '<span class="badge bg-secondary">Unknown</span>';
    }
}

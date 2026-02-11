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
     * Filter by lead status
     * Usage: Lead::status('active')->get()
     */
    public function scopeStatus(Builder $query, $status)
    {
        return $query->where('status', $status);
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
     * Get the user who created this lead
     */
    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'user_id', 'id');
    }
    
    /**
     * Convert lead to client
     */
    public function convertToClient()
    {
        // Mark lead as converted
        $this->type = 'client';
        $this->lead_status = 'converted';
        $this->save();
        
        // Log the conversion in activities
        \App\Models\ActivitiesLog::create([
            'client_id' => $this->id,
            'created_by' => \Auth::id(),
            'subject' => 'Lead converted to Client',
            'description' => "Lead successfully converted to client.",
            'activity_type' => 'lead_converted',
            'task_status' => 0,
            'pin' => 0,
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
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
    
}

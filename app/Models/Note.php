<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

/**
 * Note Model
 * 
 * Represents both regular notes and actions (formerly called tasks/followups) in the system.
 * 
 * Database field clarifications for the Action feature:
 * - folloup (field name preserved): When set to 1, this note is an Action item. 0 = regular note
 * - task_group (field name preserved): The action category (Call, Checklist, Review, Query, Urgent, Personal Action)
 * - followup_date (field name preserved): The scheduled date for the action
 * - task_status (in ActivitiesLog): Action completion status (0 = incomplete, 1 = completed)
 * - assigned_to: The user assigned to complete this action
 * - status: '0' = active/incomplete, '1' = completed
 * 
 * Note: Field names contain "task" and "followup" for database compatibility but refer to Actions in the UI
 */
class Note extends Model
{
    use Notifiable;

    protected $fillable = [
        'id','user_id','client_id','lead_id','unique_group_id','title','description','note_deadline','mail_id','type','pin','followup_date','folloup','assigned_to','status','task_group','matter_id','mobile_number','created_at', 'updated_at'
    ];

	public $sortable = ['id', 'created_at', 'updated_at','task_group','followup_date'];


    /**
     * Get the client that owns the note.
     */
    public function client()
    {
        return $this->belongsTo(Admin::class, 'client_id');
    }

    /**
     * Get the user who created the note.
     */
    public function user()
    {
        return $this->belongsTo(Staff::class, 'user_id');
    }

    /**
     * Get the user assigned to the note.
     */
    public function assignedUser()
    {
        return $this->belongsTo(Staff::class, 'assigned_to');
    }

    /**
     * Legacy method for backward compatibility
     */
    public function noteClient()
    {
        return $this->client();
    }

    /**
     * Legacy method for backward compatibility
     */
    public function noteUser()
    {
        return $this->user();
    }

    /**
     * Legacy method for backward compatibility
     */
    public function assigned_user()
    {
        return $this->assignedUser();
    }

    /**
     * Legacy relationship - Appointment model has been removed
     * This relationship is kept for backward compatibility but will return null
     * 
     * @deprecated Appointment system has been removed
     */
    public function lead()
    {
        // Appointment model no longer exists - old appointment system removed
        // Returning null to prevent errors
        return null;
    }

}

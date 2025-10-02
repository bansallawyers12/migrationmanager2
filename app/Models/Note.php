<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use Notifiable;

    protected $fillable = [
        'id','user_id','client_id','title','mail_id','type','assigned_to','pin','followup_date','folloup','status','description','created_at', 'updated_at','task_group'
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
        return $this->belongsTo(Admin::class, 'user_id');
    }

    /**
     * Get the user assigned to the note.
     */
    public function assignedUser()
    {
        return $this->belongsTo(Admin::class, 'assigned_to');
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

    public function lead()
    {
        return $this->belongsTo('App\\Models\\Models\Appointment','lead_id','id');
    }

}

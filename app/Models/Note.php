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


    public function noteClient()
    {
        return $this->belongsTo('App\\Models\\Models\Admin','client_id','id');
    }

    public function noteUser()
    {
        return $this->belongsTo('App\\Models\\Models\Admin','user_id','id');
    }

    public function assigned_user()
    {
        return $this->belongsTo('App\\Models\\Models\Admin','assigned_to','id');
    }

    public function lead()
    {
        return $this->belongsTo('App\\Models\\Models\Appointment','lead_id','id');
    }

}

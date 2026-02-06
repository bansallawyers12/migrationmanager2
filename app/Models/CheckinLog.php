<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CheckinLog extends Model
{
	use Sortable;

	protected $fillable = ['id', 'client_id', 'user_id', 'visit_purpose', 'office', 'contact_type', 'status', 'date', 'sesion_start', 'sesion_end', 'wait_time', 'attend_time', 'wait_type', 'created_at', 'updated_at'];
	
	public $sortable = ['id','created_at', 'updated_at'];
	
	/**
     * Get the client associated with this checkin log
     */
    public function client()
    {
        if ($this->contact_type == 'Lead') {
            return $this->belongsTo(Lead::class, 'client_id');
        } else {
            return $this->belongsTo(Admin::class, 'client_id')->where('role', '7');
        }
    }
    
    /**
     * Get the assignee associated with this checkin log
     */
    public function assignee()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    /**
     * Get the office (branch) associated with this checkin log
     */
    public function office()
    {
        return $this->belongsTo(Branch::class, 'office');
    }

    /**
     * Get the check-in history records for this checkin log
     */
    public function histories()
    {
        return $this->hasMany(CheckinHistory::class, 'checkin_id');
    }
}
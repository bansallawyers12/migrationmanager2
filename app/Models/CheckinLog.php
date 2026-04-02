<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CheckinLog extends Model
{
	use Sortable;

	protected $fillable = [
        'id',
        'client_id',
        'walk_in_phone',
        'walk_in_email',
        'user_id',
        'visit_purpose',
        'office',
        'contact_type',
        'status',
        'date',
        'sesion_start',
        'sesion_end',
        'wait_time',
        'attend_time',
        'wait_type',
        'created_at',
        'updated_at',
    ];
	
	public $sortable = ['id','created_at', 'updated_at'];

	/**
	 * Linked client or lead on admins. Uses a direct admins query so Contact Name still resolves when
	 * contact_type/Lead global scope would miss the row (e.g. client_id points at type client, or soft-deleted lead).
	 */
	public function resolveCrmContact(): ?Admin
	{
		if ($this->contact_type === 'Walk-in' || empty($this->client_id)) {
			return null;
		}

		return Admin::whereIn('type', ['client', 'lead'])->where('id', $this->client_id)->first();
	}
	
	/**
     * Get the client associated with this checkin log
     */
    public function client()
    {
        if ($this->contact_type == 'Lead') {
            return $this->belongsTo(Lead::class, 'client_id');
        }

        return $this->belongsTo(Admin::class, 'client_id')->whereIn('type', ['client', 'lead']);
    }
    
    /**
     * Get the assignee associated with this checkin log
     */
    public function assignee()
    {
        return $this->belongsTo(Staff::class, 'user_id');
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
<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ActivitiesLog extends Authenticatable
{
    use Notifiable;
	use Sortable;
	
	protected $fillable = [
		'client_id',
		'created_by',
		'subject',
		'description',
		'sms_log_id',
		'activity_type',
		'use_for',
		'followup_date',
		'task_group',
		'pin',
	];

	protected $casts = [
		'followup_date' => 'datetime',
		'pin' => 'boolean',
	];

	public $sortable = [
		'id',
		'client_id',
		'created_by',
		'activity_type',
		'created_at',
	];

	/**
	 * Get the client this activity belongs to
	 */
	public function client()
	{
		return $this->belongsTo(Admin::class, 'client_id');
	}

	/**
	 * Get the admin user who created this activity
	 */
	public function creator()
	{
		return $this->belongsTo(Admin::class, 'created_by');
	}

	/**
	 * Get the SMS log if this is an SMS activity
	 */
	public function smsLog()
	{
		return $this->belongsTo(SmsLog::class, 'sms_log_id');
	}

	/**
	 * Scope: Filter by client
	 */
	public function scopeForClient($query, $clientId)
	{
		return $query->where('client_id', $clientId);
	}

	/**
	 * Scope: Filter by activity type
	 */
	public function scopeByType($query, $type)
	{
		return $query->where('activity_type', $type);
	}

	/**
	 * Scope: SMS activities only
	 */
	public function scopeSmsActivities($query)
	{
		return $query->where('activity_type', 'sms');
	}

	/**
	 * Scope: Pinned activities
	 */
	public function scopePinned($query)
	{
		return $query->where('pin', 1);
	}

	/**
	 * Check if this is an SMS activity
	 */
	public function isSmsActivity()
	{
		return $this->activity_type === 'sms';
	}

	/**
	 * Check if activity is pinned
	 */
	public function isPinned()
	{
		return (bool) $this->pin;
	}

	/**
	 * Get activity icon based on type
	 */
	public function getIconAttribute()
	{
		return match($this->activity_type) {
			'sms' => 'fa-sms',
			'email' => 'fa-envelope',
			'document' => 'fa-file-alt',
			'note' => 'fa-sticky-note',
			default => 'fa-sticky-note',
		};
	}

	/**
	 * Get activity icon color based on type
	 */
	public function getIconColorAttribute()
	{
		return match($this->activity_type) {
			'sms' => 'text-success',
			'email' => 'text-primary',
			'document' => 'text-info',
			'note' => 'text-warning',
			default => 'text-secondary',
		};
	}
}

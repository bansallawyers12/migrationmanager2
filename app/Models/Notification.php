<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Notification extends Authenticatable
{
    use Notifiable;
	use Sortable;
	
	protected $fillable = ['sender_id', 'receiver_id', 'module_id', 'url', 'notification_type', 'message', 'receiver_status', 'seen'];
 
	public $sortable = ['id', 'created_at', 'updated_at'];
	
	/**
     * Get the checkin log associated with this notification
     */
    public function checkinLog()
    {
        return $this->belongsTo(CheckinLog::class, 'module_id');
    }
    
    /**
     * Get the sender of this notification
     */
    public function sender()
    {
        return $this->belongsTo(Admin::class, 'sender_id');
    }
    
    /**
     * Get the receiver of this notification
     */
    public function receiver()
    {
        return $this->belongsTo(Admin::class, 'receiver_id');
    }
}

<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ScheduleItem extends Authenticatable
{
    use Notifiable;
	
	protected $fillable = [
		'id', 'created_at', 'updated_at'
    ];

}
<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class WebsiteSetting extends Authenticatable
{
    use Notifiable;

	protected $fillable = [
        'id', 'phone', 'ofc_timing', 'email', 'logo', 'created_at', 'updated_at'
    ];
}
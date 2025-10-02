<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class State extends Authenticatable
{
    use Notifiable;

	protected $fillable = [
        'id', 'country_id', 'name', 'created_at', 'updated_at'
    ];
}
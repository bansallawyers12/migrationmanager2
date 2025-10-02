<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Country extends Authenticatable
{
    use Notifiable;

	protected $fillable = [
        'id', 'sortname', 'name', 'phonecode', 'created_at', 'updated_at'
    ];
}
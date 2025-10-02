<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Group extends Authenticatable
{
    use Notifiable;

	protected $fillable = [
        'id', 'group_name', 'created_at', 'updated_at'
    ];
}
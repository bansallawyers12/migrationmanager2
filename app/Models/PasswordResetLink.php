<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class PasswordResetLink extends Authenticatable
{
    use Notifiable;

	protected $fillable = [
        'id', 'email', 'token', 'expire', 'created_at', 'updated_at'
    ];
}
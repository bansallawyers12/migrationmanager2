<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TaxRate extends Authenticatable
{
    use Notifiable;

	protected $fillable = [
        'id', 'name', 'created_at', 'updated_at'
    ];
}
<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ShareInvoice extends Authenticatable
{
    use Notifiable;
	
	protected $fillable = [
		'id', 'created_at', 'updated_at'
    ];
	
	public function company()
    {
        return $this->belongsTo('App\\Models\\\Models\\Admin','user_id','id');
    }
}
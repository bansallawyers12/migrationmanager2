<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CheckinHistory extends Authenticatable
{
    use Notifiable;
	use Sortable;

	protected $fillable = [
        'id', 'subject', 'created_by', 'checkin_id', 'description', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'created_at', 'updated_at'];
	
	
}
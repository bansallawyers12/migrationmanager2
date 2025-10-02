<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class MatterEmailTemplate extends Authenticatable
{
    use Notifiable;
	use Sortable;

	protected $fillable = [
        'id', 'matter_id','name', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'name', 'created_at', 'updated_at'];
	
}
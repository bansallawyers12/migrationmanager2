<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserRole extends Authenticatable
{
    use Notifiable;
	use Sortable;

	protected $fillable = [
        'id', 'usertype', 'module_access', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'name'];
	
	public function usertypedata()
    {
        return $this->belongsTo('App\\Models\\UserType','usertype','id');
    }
}
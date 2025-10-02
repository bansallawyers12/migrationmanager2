<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Course extends Authenticatable
{
    use Notifiable;
	use Sortable;

	protected $fillable = [
        'id', 'course_name', 'plans', 'status', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'course_name', 'created_at', 'updated_at'];
}
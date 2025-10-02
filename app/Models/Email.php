<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Email extends Authenticatable
{
    use Notifiable;
	use Sortable; 
	
    /** 
     * The attributes that are mass assignable.
     *
     * @var array
     */
		
	public $sortable = ['id', 'created_at', 'updated_at'];

} 

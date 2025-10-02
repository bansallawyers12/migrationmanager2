<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class EmailTemplate extends Authenticatable
{
    use Notifiable;
	use Sortable;

	protected $fillable = [
        'id', 'title', 'subject', 'variables', 'alias', 'email_from', 'description', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'title'];
}
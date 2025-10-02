<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class FreeDownload extends Authenticatable
{
    use Notifiable;
	use Sortable;

	protected $fillable = [
        'id', 'resource', 'professor_name', 'subject', 'type', 'content', 'duration', 'free_img', 'status', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'professor_name', 'subject', 'created_at', 'updated_at'];
}
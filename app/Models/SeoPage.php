<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SeoPage extends Authenticatable
{
    use Notifiable;
	
	protected $fillable = [
		'id', 'page_title', 'page_slug', 'meta_title', 'meta_keyword', 'meta_desc', 'created_at', 'updated_at'
    ];
}
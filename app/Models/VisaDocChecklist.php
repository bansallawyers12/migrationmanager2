<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class VisaDocChecklist extends Authenticatable
{
    use Notifiable;
	use Sortable;

	protected $fillable = [
        'id', 'name','matter_id','status','created_at', 'updated_at'
    ];

	public $sortable = ['id','created_at', 'updated_at'];

}

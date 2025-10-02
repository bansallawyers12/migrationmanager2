<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Matter extends Model
{
	use Sortable;
	
	protected $table = 'matters';
	
	protected $fillable = [
		'id', 'title', 'nick_name', 'created_at', 'updated_at'
	];
	
	public $sortable = ['id', 'title', 'nick_name', 'created_at', 'updated_at'];
	
	// Relationship with MatterOtherEmailTemplate
	public function otherEmailTemplates()
	{
		return $this->hasMany('App\\Models\\MatterOtherEmailTemplate', 'matter_id');
	}
}

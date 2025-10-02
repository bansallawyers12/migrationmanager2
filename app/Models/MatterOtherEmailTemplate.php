<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class MatterOtherEmailTemplate extends Model
{
	use Sortable;

	protected $table = 'matter_other_email_templates';

	protected $fillable = [
        'id', 'matter_id','name', 'subject', 'description', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'name', 'subject', 'created_at', 'updated_at'];
	
	// Relationship with Matter
	public function matter()
	{
		return $this->belongsTo('App\\Models\\Matter', 'matter_id');
	}
}
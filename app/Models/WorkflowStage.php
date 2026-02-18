<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class WorkflowStage extends Model
{
	use Sortable;
	
	protected $table = 'workflow_stages';
	
	protected $fillable = [
        'id', 'name', 'workflow_id', 'sort_order', 'created_at', 'updated_at'
    ];
  
	public $sortable = ['id', 'name', 'created_at', 'updated_at'];

	/**
	 * Get the workflow this stage belongs to.
	 */
	public function workflow()
	{
		return $this->belongsTo(Workflow::class, 'workflow_id');
	}
}

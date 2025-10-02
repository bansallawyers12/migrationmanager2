<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Lead extends Authenticatable
{
    use Notifiable;
	use Sortable;

	protected $fillable = [
        'id', 'name', 'status', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'name', 'created_at', 'updated_at'];
	
	public function package_detail()
    {
        return $this->belongsTo('App\\Models\\Package','package_id','id');
    }
	
	public function user()
    {
        return $this->belongsTo('App\\Models\\User','user_id','id');
    }
	
	public function agentdetail()
    {
        return $this->belongsTo('App\\Models\\User','agent_id','id');
    }
	
	public function staffuser()
    {
        return $this->belongsTo('App\\Models\\\Models\\Admin','assign_to','id');
    }
   
}
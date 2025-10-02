<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Followup extends Authenticatable
{
    use Notifiable;
	use Sortable;

	protected $fillable = [
        'id', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'created_at', 'updated_at'];
	
	public function user()
    {
        return $this->belongsTo('App\\Models\\\Models\\Admin','user_id','id');
    }
	 public function post()
    {
        return $this->belongsTo('App\\Models\\Lead','lead_id');
    }
	public function followutype()
    {
        return $this->belongsTo('App\\Models\\FollowupType','followup_type','type');
    }

}
<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Appointment extends Authenticatable
{
    use Notifiable;
	use Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    protected $fillable = [
        'id','user_id','client_id','client_unique_id','timezone','email','noe_id','service_id','assinee','full_name','date','time','title','description','invites','status','related_to','preferred_language','inperson_address','timeslot_full','appointment_details','order_hash','created_at', 'updated_at'
    ];

	public $sortable = ['id', 'created_at', 'updated_at'];

	public function clients()
    {
        return $this->belongsTo('App\\Models\\\Models\\Admin','client_id','id');
    }

	public function partners()
    {
        return $this->belongsTo('App\\Models\\Partner','client_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\\Models\\\Models\\Admin','user_id','id');
    }

    public function assignee_user()
    {
        return $this->belongsTo('App\\Models\\\Models\\Admin','assignee','id');
    }

    public function service()
    {
        return $this->belongsTo('App\\Models\\BookService','service_id','id');
    }

    public function natureOfEnquiry()
    {
        return $this->belongsTo('App\\Models\\NatureOfEnquiry','noe_id','id');
    }
}

<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Contact extends Authenticatable
{
    use Notifiable;
	use Sortable; 
	
    /** 
     * The attributes that are mass assignable.
     *
     * @var array
     */
	
	 
	protected $fillable = [
        'id', 'srname', 'first_name', 'middle_name', 'last_name', 'company_name', 'contact_display_name', 'contact_email', 'contact_phone', 'work_phone', 'website', 'designation', 'department', 'skype_name', 'facebook_name', 'twitter_name', 'linkedin_name', 'instagram_name', 'youtube_name', 'country', 'address', 'city', 'zipcode', 'phone', 'created_at', 'updated_at'
    ]; 
  
	public $sortable = ['id', 'created_at', 'updated_at'];
 
	 public function currencydata() 
    {
        return $this->belongsTo('App\\Models\\Currency','currency','id');
    }
	
	public function company()
    {
        return $this->belongsTo('App\\Models\\\Models\\Admin','user_id','id');
    }
	
	/*public function desmedia() 
    {
        return $this->belongsTo('App\\Models\\MediaImage','dest_image','id');
    }
	
	public function mypackage() 
    {
        return $this->hasMany('App\\Models\\Package','destination','id');
    } */
} 

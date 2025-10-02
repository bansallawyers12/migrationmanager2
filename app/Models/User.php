<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	 
	/*protected $fillable = [
        'id', 'first_name', 'last_name', 'email', 'email_verified_at', 'password', 'phone', 'course_level', 'country', 'state', 'city', 'address', 'zip', 'profile_img', 'provider', 'provider_id', 'first_time_msg', 'status', 'created_at', 'updated_at'
    ];*/
	 
	protected $fillable = [
        'id', 'client_id', 'name', 'email', 'password', 'phone', 'city', 'address', 'dob',  'wedding_anniversary', 'created_at', 'updated_at'
    ];

    /** 
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */ 
    protected $hidden = [
        'password', 'remember_token',
    ];
	
	public function countryData()
    {
        return $this->belongsTo('App\\Models\\Country','country');
    }
	
	public function stateData()
    {
        return $this->belongsTo('App\\Models\\State','state');
    }
	
}

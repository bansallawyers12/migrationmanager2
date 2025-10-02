<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admin extends Authenticatable
{
    use Notifiable, Sortable, HasFactory, HasApiTokens; // Add HasApiTokens

	// The authentication guard for admin
    protected $guard = 'admin';

	/**
      * The attributes that are mass assignable.
      *
      * @var array
	*/
	protected $fillable = [
        'id', 'role', 'first_name', 'last_name', 'email', 'password', 'decrypt_password', 'country', 'state', 'city', 'address', 'zip', 'profile_img', 'status', 'service_token', 'token_generated_at', 'cp_status','cp_random_code','cp_code_verify','cp_token_generated_at','created_at', 'updated_at'
    ];

	/**
      * The attributes that should be hidden for arrays.
      *
      * @var array
	*/
    protected $hidden = [
        'password', 'remember_token', 'cp_random_code'
    ];

	public $sortable = ['id', 'first_name', 'last_name', 'email', 'created_at', 'updated_at'];

	public function countryData()
    {
        return $this->belongsTo('App\\Models\\Country','country');
    }

	public function stateData()
    {
        return $this->belongsTo('App\\Models\\State','state');
    }
	public function usertype()
    {
        return $this->belongsTo('App\\Models\\UserRole', 'role', 'id');
    }


	/**
     * Get the forms related to this client.
     */
    public function forms(): HasMany
    {
        return $this->hasMany(Form956::class);
    }

    /**
     * Get full name
    */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the emails for this admin.
     */
    /*public function emails(): HasMany
    {
        return $this->hasMany(EmailUpload::class, 'user_id');
    }*/

    /**
     * Get the labels for this admin.
     */
    /*public function labels(): HasMany
    {
        return $this->hasMany(Label::class, 'user_id');
    }*/
}

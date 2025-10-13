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
        'id', 'role', 'first_name', 'last_name', 'email', 'password', 'decrypt_password', 'country', 'state', 'city', 'address', 'zip', 'profile_img', 'status', 'service_token', 'token_generated_at', 'cp_status','cp_random_code','cp_code_verify','cp_token_generated_at', 'visa_expiry_verified_at', 'visa_expiry_verified_by', 'naati_test', 'py_test', 'naati_date', 'py_date', 'created_at', 'updated_at',
        // Lead-specific fields (exist in database but were missing from fillable array)
        'type', 'service', 'lead_quality', 'att_phone', 'att_email', 'client_id', 'is_archived', 'is_deleted', 'lead_status', 'lead_id', 'comments_note', 'phone', 'dob', 'gender', 'marital_status', 'contact_type', 'email_type',
        // EOI qualification fields for points calculation
        'australian_study', 'australian_study_date', 'specialist_education', 'specialist_education_date', 'regional_study', 'regional_study_date'
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

    /**
     * Get the EOI/ROI references for this client.
     */
    public function eoiReferences(): HasMany
    {
        return $this->hasMany(\App\Models\ClientEoiReference::class, 'client_id');
    }

    /**
     * Get relationships needed for points calculation
     */
    public function testScores(): HasMany
    {
        return $this->hasMany(\App\Models\ClientTestScore::class, 'client_id');
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(\App\Models\ClientExperience::class, 'client_id');
    }

    public function qualifications(): HasMany
    {
        return $this->hasMany(\App\Models\ClientQualification::class, 'client_id');
    }

    public function partner()
    {
        return $this->hasOne(\App\Models\ClientSpouseDetail::class, 'client_id');
    }

    public function occupations(): HasMany
    {
        return $this->hasMany(\App\Models\ClientOccupation::class, 'client_id');
    }

    public function clientPartners(): HasMany
    {
        return $this->hasMany(\App\Models\ClientRelationship::class, 'client_id');
    }
}

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
        'id', 
        // Core Identity
        'first_name', 'last_name', 'email', 'password', 'decrypt_password',
        // Role & Permissions
        'role', 'position', 'team', 'permission', 'office_id',
        // Staff ID
        'staff_id',
        // Contact Information
        'phone', 'country_code', 'telephone',
        // Address
        'country', 'state', 'city', 'address', 'zip',
        // Profile
        'profile_img', 'status', 'verified',
        // Business/Professional Info
        'marn_number', 'legal_practitioner_number', 'exempt_person_reason',
        'company_name', 'company_website', 'primary_email',
        'gst_no', 'gstin', 'gst_date', 'is_business_gst',
        'ABN_number', 'business_mobile', 'business_fax', 'company_fax',
        // Email Configuration
        'smtp_host', 'smtp_port', 'smtp_enc', 'smtp_username', 'smtp_password',
        // API/Service Tokens
        'service_token', 'token_generated_at',
        // Client Portal (for staff access)
        'cp_status', 'cp_random_code', 'cp_code_verify', 'cp_token_generated_at',
        // Verification (staff can verify documents)
        'visa_expiry_verified_at', 'visa_expiry_verified_by',
        // Permissions
        'show_dashboard_per',
        // Personal (staff might need some personal info)
        'marital_status', 'time_zone',
        // Timestamps
        'created_at', 'updated_at'
    ];

	/**
      * The attributes that should be hidden for arrays.
      *
      * @var array
	*/
    protected $hidden = [
        'password', 'remember_token', 'cp_random_code'
    ];

	public $sortable = [
        'id',
        'client_id',
        'first_name',
        'last_name',
        'email',
        'rating',
        'status',
        'created_at',
        'updated_at'
    ];

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
     * Get full name attribute
    */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    // ============================================================
    // STAFF RELATIONSHIPS
    // ============================================================

    /**
     * Get the office this staff member belongs to
     */
    public function office()
    {
        return $this->belongsTo(\App\Models\OurOffice::class, 'office_id');
    }

    /**
     * Get the clients assigned to this staff member (as agent)
     */
    public function assignedClients(): HasMany
    {
        return $this->hasMany(\App\Models\Admin::class, 'agent_id');
    }

    /**
     * Get the followups assigned to this staff member
     */
    public function assignedFollowups(): HasMany
    {
        return $this->hasMany(\App\Models\LeadFollowup::class, 'assigned_to');
    }

    /**
     * Get the documents created by this staff member
     */
    public function createdDocuments(): HasMany
    {
        return $this->hasMany(\App\Models\Document::class, 'created_by');
    }

    /**
     * Alias for createdDocuments() - for backward compatibility
     */
    public function documents(): HasMany
    {
        return $this->hasMany(\App\Models\Document::class, 'created_by');
    }

    /**
     * Get DOB verifications done by this staff member
     */
    public function dobVerifications(): HasMany
    {
        return $this->hasMany(\App\Models\Admin::class, 'dob_verified_by');
    }

    /**
     * Get phone verifications done by this staff member
     */
    public function phoneVerifications(): HasMany
    {
        return $this->hasMany(\App\Models\Admin::class, 'phone_verified_by');
    }

    /**
     * Get visa expiry verifications done by this staff member
     */
    public function visaExpiryVerifications(): HasMany
    {
        return $this->hasMany(\App\Models\Admin::class, 'visa_expiry_verified_by');
    }

    /**
     * Get the EOI/ROI references for this client
     * Note: Admins table also stores client records
     */
    public function eoiReferences(): HasMany
    {
        return $this->hasMany(\App\Models\ClientEoiReference::class, 'client_id');
    }
}

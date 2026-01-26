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
        // Migration Agent Flag & Details
        'is_migration_agent',
        // Business/Professional Info
        'marn_number', 'legal_practitioner_number', 'exempt_person_reason',
        'business_address', 'business_phone', 'business_mobile', 'business_email', 'business_fax',
        'tax_number',
        'company_name', 'company_website', 'primary_email',
        'gst_no', 'gstin', 'gst_date', 'is_business_gst',
        'ABN_number', 'company_fax',
        // Company Lead/Client Flag (company data stored in companies table)
        'is_company',
        // Email Configuration
        'smtp_host', 'smtp_port', 'smtp_enc', 'smtp_username', 'smtp_password',
        // API/Service Tokens
        'service_token', 'token_generated_at',
        // Client Portal (for staff access)
        'cp_status', 'cp_random_code', 'cp_code_verify', 'cp_token_generated_at',
        // EOI Qualification Fields (for immigration points calculation)
        'australian_study', 'australian_study_date', 'specialist_education', 'specialist_education_date', 'regional_study', 'regional_study_date',
        // Verification (staff can verify documents)
        'visa_expiry_verified_at', 'visa_expiry_verified_by',
        // Permissions
        'show_dashboard_per',
        // Archive fields
        'is_archived', 'archived_by',
        // Personal (staff might need some personal info)
        'marital_status', 'time_zone',
        // Client/Lead Tags
        'tagname',
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

    /**
     * Get age attribute - calculates from DOB on-the-fly
     * Falls back to stored age if DOB is not available
     * Always returns accurate age when DOB exists
     * 
     * @return string|null
     */
    public function getAgeAttribute($value)
    {
        // If DOB exists, calculate age on-the-fly (always accurate)
        if ($this->dob && $this->dob !== null) {
            try {
                $dobDate = \Carbon\Carbon::parse($this->dob);
                $now = \Carbon\Carbon::now();
                
                // Don't calculate for future dates
                if ($dobDate->isFuture()) {
                    return $value; // Return stored value or null
                }
                
                $diff = $now->diff($dobDate);
                return $diff->y . ' years ' . $diff->m . ' months';
            } catch (\Exception $e) {
                // If calculation fails, return stored value
                return $value;
            }
        }
        
        // If no DOB, return stored age value (or null)
        return $value;
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

    // ============================================================
    // CLIENT RELATIONSHIPS (For Immigration/EOI Data)
    // ============================================================

    /**
     * Get the partner/spouse details for this client
     * Used for EOI points calculation
     */
    public function partner()
    {
        return $this->hasOne(\App\Models\ClientSpouseDetail::class, 'client_id');
    }

    /**
     * Get the test scores (IELTS, PTE, TOEFL, etc.) for this client
     * Used for English proficiency points calculation
     */
    public function testScores(): HasMany
    {
        return $this->hasMany(\App\Models\ClientTestScore::class, 'client_id');
    }

    /**
     * Get the occupations/skills assessments for this client
     * Used for occupation and work experience points calculation
     */
    public function occupations(): HasMany
    {
        return $this->hasMany(\App\Models\ClientOccupation::class, 'client_id');
    }

    /**
     * Get the relationships (partner, children, parents, etc.) for this client
     * Used for family member information
     */
    public function relationships(): HasMany
    {
        return $this->hasMany(\App\Models\ClientRelationship::class, 'client_id');
    }

    // ============================================================
    // COMPANY LEAD/CLIENT RELATIONSHIPS
    // ============================================================

    /**
     * Get the company data for this admin (if it's a company)
     */
    public function company()
    {
        return $this->hasOne(Company::class, 'admin_id', 'id');
    }

    /**
     * Get companies where this person is the contact person
     */
    public function companiesAsContactPerson()
    {
        return $this->hasMany(Company::class, 'contact_person_id', 'id');
    }

    /**
     * Check if this is a company
     */
    public function isCompany(): bool
    {
        return (bool) $this->is_company;
    }

    /**
     * Get display name (company name or personal name)
     * For companies: "Company Name (Contact: Person Name)"
     * For personal: "First Name Last Name"
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->is_company && $this->company) {
            $companyName = $this->company->company_name ?? 'Unnamed Company';
            if ($this->company->contactPerson) {
                $contactName = trim($this->company->contactPerson->first_name . ' ' . $this->company->contactPerson->last_name);
                return "{$companyName} (Contact: {$contactName})";
            }
            return $companyName;
        }
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get company name or fallback to personal name
     */
    public function getCompanyNameOrPersonalNameAttribute(): string
    {
        if ($this->is_company && $this->company) {
            return $this->company->company_name ?? 'Unnamed Company';
        }
        return trim($this->first_name . ' ' . $this->last_name);
    }
}

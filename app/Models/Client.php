<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Client Model
 * 
 * Represents clients and leads in the CRM system.
 * Separated from Admin (staff) model for better data organization.
 */
class Client extends Authenticatable
{
    use Notifiable, Sortable, HasFactory, HasApiTokens;

    // The authentication guard for clients
    protected $guard = 'client';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'client_id', 'client_counter', 'type',
        // Core Identity
        'first_name', 'last_name', 'email', 'password', 'decrypt_password',
        // Personal Information
        'dob', 'age', 'gender', 'marital_status', 'profile_img',
        // Contact Information
        'phone', 'country_code', 'contact_type', 'email_type',
        'att_phone', 'att_email', 'att_country_code',
        'emergency_country_code', 'emergency_contact_no', 'emergency_contact_type',
        // Address
        'country', 'state', 'city', 'address', 'zip', 'latitude', 'longitude',
        // Immigration/Visa
        'passport_number', 'country_passport', 'visa_type', 'visaExpiry', 'visaGrant',
        'visa_opt', 'prev_visa', 'preferredIntake', 'applications', 'is_visa_expire_mail_sent',
        // Verification
        'dob_verified_date', 'dob_verified_by', 'phone_verified_date', 'phone_verified_by',
        'visa_expiry_verified_at', 'visa_expiry_verified_by', 'email_verified_at', 'dob_verify_document',
        // EOI/Skills Assessment
        'nomi_occupation', 'skill_assessment', 'high_quali_aus', 'high_quali_overseas',
        'relevant_work_exp_aus', 'relevant_work_exp_over', 'naati_test', 'py_test',
        'naati_date', 'py_date', 'naati_py', 'married_partner', 'total_points', 'start_process',
        'qualification_level', 'qualification_name', 'experience_job_title', 'experience_country',
        'nati_language', 'py_field', 'regional_points',
        // Australian Study
        'australian_study', 'australian_study_date', 'specialist_education', 'specialist_education_date',
        'regional_study', 'regional_study_date',
        // CRM/Lead Management
        'lead_id', 'lead_status', 'lead_quality', 'service', 'source', 'assignee', 'followers',
        'tagname', 'tags', 'rating', 'comments_note', 'followup_date',
        // Status
        'status', 'verified', 'is_archived', 'archived_on', 'is_deleted', 'is_star_client',
        // Client Portal
        'cp_status', 'cp_random_code', 'cp_token_generated_at', 'cp_code_verify',
        // Relationships
        'user_id', 'agent_id', 'office_id', 'wp_customer_id', 'not_picked_call',
        // Files
        'related_files',
        // Timestamps
        'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'cp_random_code', 'decrypt_password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'date',
        'visaExpiry' => 'date',
        'visaGrant' => 'date',
        'preferredIntake' => 'date',
        'naati_date' => 'date',
        'py_date' => 'date',
        'australian_study_date' => 'date',
        'specialist_education_date' => 'date',
        'regional_study_date' => 'date',
        'dob_verified_date' => 'date',
        'phone_verified_date' => 'date',
        'visa_expiry_verified_at' => 'date',
        'archived_on' => 'date',
        'followup_date' => 'datetime',
        'cp_token_generated_at' => 'timestamp',
        'australian_study' => 'boolean',
        'specialist_education' => 'boolean',
        'regional_study' => 'boolean',
    ];

    /**
     * Sortable columns
     *
     * @var array
     */
    public $sortable = [
        'id', 'client_id', 'first_name', 'last_name', 'email', 'type', 
        'lead_status', 'created_at', 'updated_at'
    ];

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

    /**
     * Get the agent assigned to this client
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'agent_id');
    }

    /**
     * Get the staff member who verified DOB
     */
    public function dobVerifier(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'dob_verified_by');
    }

    /**
     * Get the staff member who verified phone
     */
    public function phoneVerifier(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'phone_verified_by');
    }

    /**
     * Get the staff member who verified visa expiry
     */
    public function visaExpiryVerifier(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'visa_expiry_verified_by');
    }

    /**
     * Get the country data
     */
    public function countryData(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country');
    }

    /**
     * Get the state data
     */
    public function stateData(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state');
    }

    /**
     * Get the office associated with this client
     */
    public function office(): BelongsTo
    {
        return $this->belongsTo(OurOffice::class, 'office_id');
    }

    /**
     * Get the forms (Form 956) for this client
     */
    public function forms(): HasMany
    {
        return $this->hasMany(Form956::class, 'client_id', 'id');
    }

    /**
     * Get the EOI/ROI references for this client
     */
    public function eoiReferences(): HasMany
    {
        return $this->hasMany(ClientEoiReference::class, 'client_id');
    }

    /**
     * Get the test scores for this client
     */
    public function testScores(): HasMany
    {
        return $this->hasMany(ClientTestScore::class, 'client_id');
    }

    /**
     * Get the work experiences for this client
     */
    public function experiences(): HasMany
    {
        return $this->hasMany(ClientExperience::class, 'client_id');
    }

    /**
     * Get the qualifications for this client
     */
    public function qualifications(): HasMany
    {
        return $this->hasMany(ClientQualification::class, 'client_id');
    }

    /**
     * Get the spouse/partner details for this client
     */
    public function partner(): HasOne
    {
        return $this->hasOne(ClientSpouseDetail::class, 'client_id');
    }

    /**
     * Get the occupations for this client
     */
    public function occupations(): HasMany
    {
        return $this->hasMany(ClientOccupation::class, 'client_id');
    }

    /**
     * Get the relationships (family members, etc.) for this client
     */
    public function clientPartners(): HasMany
    {
        return $this->hasMany(ClientRelationship::class, 'client_id');
    }

    /**
     * Get the followups for this lead/client
     */
    public function followups(): HasMany
    {
        return $this->hasMany(LeadFollowup::class, 'lead_id', 'id');
    }

    /**
     * Get the documents for this client
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'client_id', 'id');
    }

    // ============================================================
    // SCOPES
    // ============================================================

    /**
     * Scope to get only clients (not leads)
     */
    public function scopeClients($query)
    {
        return $query->where('type', 'client');
    }

    /**
     * Scope to get only leads
     */
    public function scopeLeads($query)
    {
        return $query->where('type', 'lead');
    }

    /**
     * Scope to get active clients/leads
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('is_deleted', 0)
                     ->where('is_archived', 0);
    }

    /**
     * Scope to get archived clients/leads
     */
    public function scopeArchived($query)
    {
        return $query->where('is_archived', 1);
    }

    /**
     * Scope to get deleted clients/leads
     */
    public function scopeDeleted($query)
    {
        return $query->where('is_deleted', 1);
    }

    /**
     * Scope to get star clients
     */
    public function scopeStarClients($query)
    {
        return $query->where('is_star_client', '1');
    }

    // ============================================================
    // ACCESSORS & MUTATORS
    // ============================================================

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Check if this is a client (not a lead)
     */
    public function isClient(): bool
    {
        return $this->type === 'client';
    }

    /**
     * Check if this is a lead
     */
    public function isLead(): bool
    {
        return $this->type === 'lead';
    }

    /**
     * Check if archived
     */
    public function isArchived(): bool
    {
        return $this->is_archived == 1;
    }

    /**
     * Check if deleted
     */
    public function isDeleted(): bool
    {
        return $this->is_deleted == 1;
    }

    /**
     * Check if active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->isDeleted() && !$this->isArchived();
    }
}

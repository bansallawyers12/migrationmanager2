<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Authenticatable
{
    use Notifiable, Sortable, HasApiTokens;

    /**
     * The authentication guard for staff (CRM login uses 'admin' guard).
     */
    protected $guard = 'admin';

    /**
     * The table associated with the model.
     */
    protected $table = 'staff';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'country_code',
        'phone',
        'telephone',
        'profile_img',
        'status',
        'verified',
        'role',
        'position',
        'team',
        'permission',
        'office_id',
        'show_dashboard_per',
        'time_zone',
        'is_migration_agent',
        'marn_number',
        'legal_practitioner_number',
        'company_name',
        'company_website',
        'business_address',
        'business_phone',
        'business_mobile',
        'business_email',
        'tax_number',
        'ABN_number',
        'is_archived',
        'archived_by',
        'archived_on',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'status' => 'integer',
        'verified' => 'integer',
        'show_dashboard_per' => 'integer',
        'is_migration_agent' => 'integer',
        'is_archived' => 'integer',
        'archived_on' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Sortable columns for listings.
     */
    public $sortable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'status',
        'created_at',
        'updated_at',
    ];

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

    /**
     * Get the office/branch this staff member belongs to.
     */
    public function office(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'office_id');
    }

    /**
     * Get the role/user type for this staff member.
     */
    public function usertype(): BelongsTo
    {
        return $this->belongsTo(UserRole::class, 'role', 'id');
    }

    /**
     * Get the staff member who archived this record.
     */
    public function archivedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'archived_by');
    }

    /**
     * Get the clients assigned to this staff member (as migration agent).
     * Clients are in admins table with agent_id = this staff's id.
     */
    public function assignedClients(): HasMany
    {
        return $this->hasMany(Admin::class, 'agent_id');
    }

    // ============================================================
    // ATTRIBUTES
    // ============================================================

    /**
     * Get full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name) ?: $this->email;
    }

    /**
     * Alias for business_address (used by views expecting address).
     */
    public function getAddressAttribute(): ?string
    {
        return $this->business_address;
    }

    /**
     * Set address (maps to business_address).
     */
    public function setAddressAttribute(?string $value): void
    {
        $this->attributes['business_address'] = $value;
    }

    /**
     * State from office/branch if available.
     */
    public function getStateAttribute(): ?string
    {
        return $this->office?->state ?? null;
    }

    /**
     * City from office/branch if available.
     */
    public function getCityAttribute(): ?string
    {
        return $this->office?->city ?? null;
    }

    /**
     * Zip from office/branch if available.
     */
    public function getZipAttribute(): ?string
    {
        return $this->office?->zip ?? null;
    }

    /**
     * Scope for active staff (status = 1).
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope for non-archived staff.
     */
    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', 0);
    }
}

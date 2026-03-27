<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'admin_id',
        'company_name',
        'trading_name',
        'has_trading_name',
        'ABN_number',
        'ACN',
        'company_type',
        'company_website',
        'contact_person_id',
        'contact_person_position',
        // Employer sponsorship fields
        'trust_name',
        'trust_abn',
        'trustee_name',
        'trustee_details',
        'sponsorship_type',
        'sponsorship_status',
        'sponsorship_start_date',
        'sponsorship_end_date',
        'trn',
        'regional_sponsorship',
        'adverse_information',
        'previous_sponsorship_notes',
        'annual_turnover',
        'wages_expenditure',
        'workforce_australian_citizens',
        'workforce_permanent_residents',
        'workforce_temp_visa_holders',
        'workforce_total',
        'workforce_foreign_494',
        'workforce_foreign_other_temp_activity',
        'workforce_foreign_overseas_students',
        'workforce_foreign_working_holiday',
        'workforce_foreign_other',
        'business_operating_since',
        'main_business_activity',
        'lmt_required',
        'lmt_start_date',
        'lmt_end_date',
        'lmt_notes',
        'training_position_title',
        'trainer_name',
    ];

    protected $casts = [
        'has_trading_name' => 'boolean',
        'regional_sponsorship' => 'boolean',
        'adverse_information' => 'boolean',
        'lmt_required' => 'boolean',
        'sponsorship_start_date' => 'date',
        'sponsorship_end_date' => 'date',
        'business_operating_since' => 'date',
        'lmt_start_date' => 'date',
        'lmt_end_date' => 'date',
        'annual_turnover' => 'decimal:2',
        'wages_expenditure' => 'decimal:2',
    ];

    /**
     * Get the admin (lead/client) record this company belongs to
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    /**
     * Get the primary contact person for this company
     */
    public function contactPerson(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'contact_person_id', 'id');
    }

    /**
     * Get trading names (multiple per company).
     * Display logic: if tradingNames has records use those; else fall back to trading_name.
     */
    public function tradingNames(): HasMany
    {
        return $this->hasMany(CompanyTradingName::class)->orderBy('sort_order');
    }

    /**
     * Get directors
     */
    public function directors(): HasMany
    {
        return $this->hasMany(CompanyDirector::class)->orderBy('sort_order');
    }

    /**
     * Get nominations
     */
    public function nominations(): HasMany
    {
        return $this->hasMany(CompanyNomination::class)->orderBy('sort_order');
    }

    /**
     * Employer sponsorship rows (multiple per company).
     */
    public function sponsorships(): HasMany
    {
        return $this->hasMany(CompanySponsorship::class)->orderBy('sort_order')->orderBy('id');
    }
}

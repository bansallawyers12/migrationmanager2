<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyFinancial extends Model
{
    protected $table = 'company_financials';

    protected $fillable = [
        'company_id',
        'financial_year',
        'annual_turnover',
        'wages_expenditure',
        'sort_order',
    ];

    protected $casts = [
        'annual_turnover' => 'decimal:2',
        'wages_expenditure' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

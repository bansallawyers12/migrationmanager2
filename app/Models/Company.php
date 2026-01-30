<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'admin_id',
        'company_name',
        'trading_name',
        'ABN_number',
        'ACN',
        'company_type',
        'company_website',
        'contact_person_id',
        'contact_person_position',
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
}

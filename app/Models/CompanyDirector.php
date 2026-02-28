<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyDirector extends Model
{
    protected $table = 'company_directors';

    protected $fillable = [
        'company_id',
        'director_name',
        'director_dob',
        'director_role',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'director_dob' => 'date',
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

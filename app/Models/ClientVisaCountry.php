<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientVisaCountry extends Model
{
    protected $table = 'client_visa_countries'; // Table name

    protected $fillable = [
        'client_id',
        'admin_id',
        'visa_country',      // Country of the visa
        'visa_type',         // Type of visa
        'visa_description',
        'visa_expiry_date',  // Expiry date of the visa
        'visa_grant_date',
    ];

    /**
     * Get the matter (visa type) for this visa
     * Enables eager loading to prevent N+1 queries
     */
    public function matter()
    {
        return $this->belongsTo(Matter::class, 'visa_type', 'id');
    }
}

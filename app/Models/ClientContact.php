<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientContact extends Model
{
    protected $table = 'client_contacts';

    protected $fillable = [
        'admin_id',
        'client_id',
        'contact_type',
        'country_code',
        'phone',
        'is_verified',
        'verified_at',
        'verified_by'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    // Relationships
    public function verifications()
    {
        return $this->hasMany(PhoneVerification::class);
    }

    public function latestVerification()
    {
        return $this->hasOne(PhoneVerification::class)->latest();
    }

    public function verifier()
    {
        return $this->belongsTo(Admin::class, 'verified_by');
    }

    // Helper methods
    public function isAustralianNumber()
    {
        return $this->country_code === '+61';
    }

    public function needsVerification()
    {
        return $this->isAustralianNumber() && !$this->is_verified;
    }
}

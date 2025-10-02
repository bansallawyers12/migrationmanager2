<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RefreshToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'device_name',
        'expires_at',
        'is_revoked',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_revoked' => 'boolean',
    ];

    /**
     * Get the user that owns the refresh token.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    /**
     * Scope to get active (non-revoked) refresh tokens
     */
    public function scopeActive($query)
    {
        return $query->where('is_revoked', false)
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope to get refresh tokens for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Generate a new refresh token
     */
    public static function generateToken($userId, $deviceName = null, $expiryDays = 30)
    {
        $token = Str::random(64);
        $expiresAt = now()->addDays($expiryDays);

        return self::create([
            'user_id' => $userId,
            'token' => $token,
            'device_name' => $deviceName,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Check if token is valid (not revoked and not expired)
     */
    public function isValid()
    {
        return !$this->is_revoked && $this->expires_at > now();
    }

    /**
     * Revoke the token
     */
    public function revoke()
    {
        $this->update(['is_revoked' => true]);
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientEoiReference extends Model
{
    protected $table = 'client_eoi_references';

    protected $fillable = [
        'client_id',
        'admin_id',
        'EOI_number',
        'EOI_subclass',
        'EOI_occupation',
        'EOI_point',
        'EOI_state',
        'EOI_submission_date',
        'EOI_ROI',
        'EOI_password',
        // New fields
        'eoi_subclasses',
        'eoi_states',
        'eoi_invitation_date',
        'eoi_nomination_date',
        'eoi_status',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'eoi_subclasses' => 'array',
        'eoi_states' => 'array',
        'EOI_submission_date' => 'date',
        'eoi_invitation_date' => 'date',
        'eoi_nomination_date' => 'date',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['formatted_subclasses', 'formatted_states'];

    /**
     * Encrypt password when setting
     */
    public function setEOIPasswordAttribute($value)
    {
        $this->attributes['EOI_password'] = $value ? encrypt($value) : null;
    }

    /**
     * Decrypt password for authorized viewing
     * Use this method explicitly when password needs to be revealed
     */
    public function getEOIPasswordDecrypted(): ?string
    {
        return $this->EOI_password ? decrypt($this->EOI_password) : null;
    }

    /**
     * Automatically sync scalar fields from arrays before saving
     * Maintains backward compatibility by setting first array value to scalar field
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Sync EOI_subclass from eoi_subclasses array (first value)
            if ($model->eoi_subclasses && is_array($model->eoi_subclasses) && count($model->eoi_subclasses) > 0) {
                $model->EOI_subclass = $model->eoi_subclasses[0];
            }

            // Sync EOI_state from eoi_states array (first value)
            if ($model->eoi_states && is_array($model->eoi_states) && count($model->eoi_states) > 0) {
                $model->EOI_state = $model->eoi_states[0];
            }

            // Track who is making changes
            if (auth()->guard('admin')->check()) {
                $adminId = auth()->guard('admin')->id();
                
                if (!$model->exists) {
                    $model->created_by = $adminId;
                }
                $model->updated_by = $adminId;
            }
        });
    }

    /**
     * Relationship to client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'client_id');
    }

    /**
     * Relationship to admin who created
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Relationship to admin who last updated
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    /**
     * Get formatted subclasses as comma-separated string
     */
    public function getFormattedSubclassesAttribute(): string
    {
        return $this->eoi_subclasses ? implode(', ', $this->eoi_subclasses) : ($this->EOI_subclass ?? '');
    }

    /**
     * Get formatted states as comma-separated string
     */
    public function getFormattedStatesAttribute(): string
    {
        return $this->eoi_states ? implode(', ', $this->eoi_states) : ($this->EOI_state ?? '');
    }
}



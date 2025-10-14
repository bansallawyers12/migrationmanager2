<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class SmsTemplate extends Model
{
    use Sortable;

    protected $fillable = [
        'title',
        'message',
        'variables',
        'category',
        'alias',
        'is_active',
        'usage_count',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'usage_count' => 'integer',
    ];

    public $sortable = [
        'id',
        'title',
        'category',
        'is_active',
        'usage_count',
        'created_at',
    ];

    /**
     * Get the admin user who created the template
     */
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Get all SMS logs that used this template
     */
    public function smsLogs()
    {
        return $this->hasMany(SmsLog::class, 'template_id');
    }

    /**
     * Scope: Active templates only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Find by alias
     */
    public function scopeByAlias($query, $alias)
    {
        return $query->where('alias', $alias);
    }

    /**
     * Get template variables as array
     */
    public function getVariablesArrayAttribute()
    {
        if (empty($this->variables)) {
            return [];
        }

        return array_map('trim', explode(',', $this->variables));
    }

    /**
     * Replace variables in message
     */
    public function replaceVariables($variableValues)
    {
        $message = $this->message;

        foreach ($variableValues as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }

        return $message;
    }

    /**
     * Get category badge color
     */
    public function getCategoryBadgeAttribute()
    {
        return match($this->category) {
            'verification' => 'primary',
            'reminder' => 'warning',
            'notification' => 'info',
            'manual' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get formatted usage count
     */
    public function getFormattedUsageCountAttribute()
    {
        if ($this->usage_count >= 1000) {
            return number_format($this->usage_count / 1000, 1) . 'k';
        }

        return number_format($this->usage_count);
    }

    /**
     * Increment usage count
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    /**
     * Get missing variables from provided values
     */
    public function getMissingVariables($variableValues)
    {
        $templateVars = $this->variables_array;
        $providedVars = array_keys($variableValues);

        return array_diff($templateVars, $providedVars);
    }

    /**
     * Validate that all required variables are provided
     */
    public function hasAllVariables($variableValues)
    {
        $missing = $this->getMissingVariables($variableValues);
        return empty($missing);
    }

    /**
     * Get preview with sample data
     */
    public function getPreviewAttribute()
    {
        $sampleData = [
            'client_name' => 'John Smith',
            'first_name' => 'John',
            'matter_number' => 'M2024001',
            'appointment_date' => '15 Oct 2025',
            'appointment_time' => '10:00 AM',
            'staff_name' => 'Sarah',
            'office_phone' => '02 1234 5678',
            'verification_code' => '123456',
            'invoice_number' => 'INV-2024-001',
        ];

        return $this->replaceVariables($sampleData);
    }

    /**
     * Get character count
     */
    public function getCharacterCountAttribute()
    {
        return mb_strlen($this->message);
    }

    /**
     * Estimate SMS segments (160 chars per segment)
     */
    public function getEstimatedSegmentsAttribute()
    {
        $length = $this->character_count;
        
        if ($length <= 160) {
            return 1;
        }
        
        // Multi-part SMS uses 153 chars per segment
        return (int) ceil($length / 153);
    }
}


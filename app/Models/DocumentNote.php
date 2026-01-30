<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentNote extends Model
{
    protected $fillable = [
        'document_id',
        'created_by',
        'action_type',
        'note',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the document this note belongs to
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the admin who created this note
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Scope: Filter by document
     */
    public function scopeForDocument($query, $documentId)
    {
        return $query->where('document_id', $documentId);
    }

    /**
     * Scope: Filter by action type
     */
    public function scopeByAction($query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Get formatted action text
     */
    public function getActionTextAttribute()
    {
        return match($this->action_type) {
            'associated' => 'Associated document',
            'detached' => 'Detached document',
            'status_changed' => 'Status changed',
            'sent' => 'Document sent',
            'signed' => 'Document signed',
            'email_sent' => 'Email sent',
            'email_failed' => 'Email failed',
            'email_delivered' => 'Email delivered',
            default => ucfirst(str_replace('_', ' ', $this->action_type))
        };
    }
}

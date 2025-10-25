<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailReport extends Authenticatable
{
    use Notifiable;
	use Sortable;

	protected $fillable = [
        'id',
        'user_id',
        'from_mail',
        'to_mail',
        'cc',
        'template_id',
        'subject',
        'message',
        'type',
        'reciept_id',
        'attachments',
        'mail_type',
        'client_id',
        'client_matter_id',
        'conversion_type',
        'mail_body_type',
        'fetch_mail_sent_time',
        'uploaded_doc_id',
        'mail_is_read',
        // Python analysis fields
        'python_analysis',
        'python_rendering',
        'category',
        'priority',
        'sentiment',
        'language',
        'enhanced_html',
        'rendered_html',
        'text_preview',
        'security_issues',
        'thread_info',
        'processed_at',
        // Additional metadata
        'message_id',
        'thread_id',
        'received_date',
        'last_accessed_at',
        'file_hash',
        'created_at',
        'updated_at'
    ];

	public $sortable = ['id', 'created_at', 'updated_at', 'subject', 'from_mail', 'category', 'priority'];

    protected $casts = [
        'python_analysis' => 'array',
        'python_rendering' => 'array',
        'security_issues' => 'array',
        'thread_info' => 'array',
        'processed_at' => 'datetime',
        'received_date' => 'datetime',
        'last_accessed_at' => 'datetime',
        'mail_is_read' => 'boolean',
    ];

    /**
     * Get the attachments for the email.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(MailReportAttachment::class, 'mail_report_id');
    }

    /**
     * Get the labels for the email.
     */
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(EmailLabel::class, 'email_label_mail_report', 'mail_report_id', 'email_label_id')
                    ->withTimestamps();
    }

    /**
     * Get the client that owns the email.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'client_id');
    }

    /**
     * Get the user who uploaded the email.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    /**
     * Check if the email has attachments.
     */
    public function hasAttachments(): bool
    {
        return $this->attachments()->count() > 0;
    }

    /**
     * Get the number of attachments.
     */
    public function getAttachmentCountAttribute(): int
    {
        return $this->attachments()->count();
    }

    /**
     * Get the formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!isset($this->attributes['file_size'])) {
            return '0 B';
        }
        
        $bytes = $this->attributes['file_size'];
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if email has Python analysis.
     */
    public function hasPythonAnalysis(): bool
    {
        return !empty($this->python_analysis);
    }

    /**
     * Check if email has security issues.
     */
    public function hasSecurityIssues(): bool
    {
        return !empty($this->security_issues);
    }

    /**
     * Check if email is a reply.
     */
    public function isReply(): bool
    {
        return isset($this->thread_info['is_reply']) && $this->thread_info['is_reply'];
    }

    /**
     * Get priority badge class for UI.
     */
    public function getPriorityBadgeClassAttribute(): string
    {
        return match($this->priority ?? 'low') {
            'high' => 'bg-red-100 text-red-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'low' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get category badge class for UI.
     */
    public function getCategoryBadgeClassAttribute(): string
    {
        return match($this->category ?? 'Uncategorized') {
            'Business' => 'bg-blue-100 text-blue-800',
            'Personal' => 'bg-purple-100 text-purple-800',
            'Spam' => 'bg-red-100 text-red-800',
            'Newsletter' => 'bg-green-100 text-green-800',
            'System' => 'bg-gray-100 text-gray-800',
            'Migration Related' => 'bg-orange-100 text-orange-800',
            'Visa Grant' => 'bg-green-100 text-green-800',
            'Visa Refusal' => 'bg-red-100 text-red-800',
            'Request for Information' => 'bg-yellow-100 text-yellow-800',
            'Natural Justice Letter' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to filter by sentiment.
     */
    public function scopeBySentiment($query, $sentiment)
    {
        return $query->where('sentiment', $sentiment);
    }

    /**
     * Scope to search emails.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('subject', 'like', "%{$search}%")
              ->orWhere('from_mail', 'like', "%{$search}%")
              ->orWhere('to_mail', 'like', "%{$search}%")
              ->orWhere('message', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to filter by label.
     */
    public function scopeWithLabel($query, $labelId)
    {
        return $query->whereHas('labels', function ($q) use ($labelId) {
            $q->where('email_labels.id', $labelId);
        });
    }

    /**
     * Scope to filter emails with attachments.
     */
    public function scopeWithAttachments($query)
    {
        return $query->has('attachments');
    }

    /**
     * Scope to filter emails without attachments.
     */
    public function scopeWithoutAttachments($query)
    {
        return $query->doesntHave('attachments');
    }
}

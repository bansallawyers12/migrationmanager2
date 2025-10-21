<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Admin;
use App\Models\Lead;

class Document extends Model
{
    use Sortable, HasFactory;

    protected $table = 'documents';

    protected $fillable = [
        'file_name',
        'filetype', 
        'myfile',
        'myfile_key',
        'user_id',
        'client_id',
        'file_size',
        'type',
        'doc_type',
        'folder_name',
        'mail_type',
        'client_matter_id',
        'checklist',
        'checklist_verified_by',
        'checklist_verified_at',
        'not_used_doc',
        'status',
        'signature_doc_link',
        'signed_doc_link',
        'is_client_portal_verify',
        'client_portal_verified_by',
        'client_portal_verified_at',
        'created_by',
        'origin',
        'documentable_type',
        'documentable_id',
        'title',
        'document_type',
        'labels',
        'due_at',
        'priority',
        'primary_signer_email',
        'signer_count',
        'last_activity_at',
        'archived_at'
    ];

    protected $casts = [
        'labels' => 'array',
        'checklist_verified_at' => 'datetime',
        'client_portal_verified_at' => 'datetime',
        'due_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'archived_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $sortable = [
        'id',
        'file_name',
        'status',
        'created_at',
        'updated_at',
        'title',
        'document_type',
        'priority'
    ];

    // Relationships
    public function signers(): HasMany
    {
        return $this->hasMany(Signer::class);
    }

    public function signatureFields(): HasMany
    {
        return $this->hasMany(SignatureField::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(DocumentNote::class)->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAssociated($query)
    {
        return $query->whereNotNull('documentable_type')
                    ->whereNotNull('documentable_id');
    }

    public function scopeAdhoc($query)
    {
        return $query->whereNull('documentable_type')
                    ->whereNull('documentable_id');
    }

    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Scope to filter documents based on user visibility permissions
     * Implements the same logic as DocumentPolicy::view()
     */
    public function scopeVisible($query, $user)
    {
        // Super admins can see everything
        if ($user->role === 1) {
            return $query;
        }

        return $query->where(function($q) use ($user) {
            // Documents created by the user
            $q->where('created_by', $user->id)
              // Documents where user is a signer
              ->orWhereHas('signers', function($signerQuery) use ($user) {
                  $signerQuery->where('email', $user->email);
              })
              // Documents associated with entities the user owns/manages
              ->orWhere(function($assocQuery) use ($user) {
                  $assocQuery->where(function($adminDocs) use ($user) {
                      // Admin (client) associations
                      $adminDocs->where('documentable_type', Admin::class)
                                ->where('documentable_id', $user->id);
                  })
                  ->orWhere(function($leadDocs) use ($user) {
                      // Lead associations where user is the owner
                      $leadDocs->where('documentable_type', Lead::class)
                               ->whereHas('documentable', function($leadQuery) use ($user) {
                                   $leadQuery->where('user_id', $user->id);
                               });
                  });
              });
        });
    }

    // Accessors
    public function getDisplayTitleAttribute()
    {
        return $this->title ?: $this->file_name;
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => 'secondary',
            'sent' => 'warning', 
            'signed' => 'success',
            default => 'secondary'
        };
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_at && $this->due_at->isPast() && $this->status !== 'signed';
    }

    /**
     * Get visibility type for current authenticated user
     * Returns: 'owner', 'signer', 'associated', or null
     * 
     * NOTE: Use eager loading for signers relationship to avoid N+1 queries
     * Example: Document::with('signers')->get()
     */
    public function getVisibilityTypeAttribute()
    {
        $user = auth('admin')->user();
        if (!$user) {
            return null;
        }

        // Check if user is the creator (highest priority)
        if ($this->created_by === $user->id) {
            return 'owner';
        }

        // Check if user is a signer (use collection if loaded, query if not)
        if ($this->relationLoaded('signers')) {
            $isSigner = $this->signers->contains('email', $user->email);
        } else {
            $isSigner = $this->signers()->where('email', $user->email)->exists();
        }
        
        if ($isSigner) {
            return 'signer';
        }

        // Check if document is associated with user's entity
        if ($this->documentable_type && $this->documentable_id) {
            if ($this->documentable_type === Admin::class && $this->documentable_id === $user->id) {
                return 'associated';
            }
            if ($this->documentable_type === Lead::class) {
                // Use loaded relationship if available
                if ($this->relationLoaded('documentable')) {
                    $lead = $this->documentable;
                } else {
                    // Query using the polymorphic relationship
                    $lead = $this->documentable;
                }
                if ($lead && isset($lead->user_id) && $lead->user_id === $user->id) {
                    return 'associated';
                }
            }
        }

        // Admin viewing all
        if ($user->role === 1) {
            return 'admin';
        }

        return null;
    }

    /**
     * Get visibility icon and label
     */
    public function getVisibilityBadgeAttribute()
    {
        return match($this->visibility_type) {
            'owner' => ['icon' => '🔒', 'label' => 'My Document', 'class' => 'badge-owner'],
            'signer' => ['icon' => '✍️', 'label' => 'Need to Sign', 'class' => 'badge-signer'],
            'associated' => ['icon' => '🔗', 'label' => 'Associated', 'class' => 'badge-associated'],
            'admin' => ['icon' => '🌐', 'label' => 'Organization', 'class' => 'badge-admin'],
            default => ['icon' => '👁️', 'label' => 'Visible', 'class' => 'badge-visible']
        };
    }
}

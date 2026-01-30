<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientArtReference extends Model
{
    protected $table = 'client_art_references';

    protected $fillable = [
        'client_id',
        'client_matter_id',
        'other_reference',
        'submission_last_date',
        'status_of_file',
        'hearing_time',
        'member_name',
        'outcome',
        'comments',
        'staff_verified',
        'verification_date',
        'verified_by',
        'client_confirmation_status',
        'client_last_confirmation',
        'client_confirmation_notes',
        'client_confirmation_token',
        'confirmation_email_sent_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'submission_last_date' => 'date',
        'staff_verified' => 'boolean',
        'verification_date' => 'datetime',
        'client_last_confirmation' => 'datetime',
        'confirmation_email_sent_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'client_id');
    }

    public function clientMatter(): BelongsTo
    {
        return $this->belongsTo(ClientMatter::class, 'client_matter_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'verified_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }
}

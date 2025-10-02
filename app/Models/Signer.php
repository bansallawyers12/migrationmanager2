<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Signer extends Authenticatable
{
    protected $fillable = [
        'document_id', 
        'email', 
        'name', 
        'token', 
        'status', 
        'signed_at',
        'opened_at',
        'last_reminder_sent_at',
        'reminder_count'
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'opened_at' => 'datetime',
        'last_reminder_sent_at' => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function getStatusDisplayAttribute()
    {
        if ($this->status === 'signed') {
            return 'Signed';
        } elseif ($this->opened_at && $this->status === 'pending') {
            return 'Opened - Not Signed';
        } elseif ($this->status === 'pending') {
            return 'Pending';
        }
        return 'Unknown';
    }

    public function getStatusColorAttribute()
    {
        if ($this->status === 'signed') {
            return 'green';
        } elseif ($this->opened_at && $this->status === 'pending') {
            return 'yellow';
        } elseif ($this->status === 'pending') {
            return 'gray';
        }
        return 'gray';
    }
}
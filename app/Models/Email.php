<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;

class Email extends Authenticatable
{
    use Notifiable;
    use Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'display_name',
        'status',
        'email_signature',
        'user_id',
        'password',
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
    ];

    /**
     * SMTP mailbox password — never expose in arrays/JSON.
     * Stored in plaintext for SMTP (not hashed).
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
        'smtp_port' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $sortable = ['id', 'created_at', 'updated_at'];
}

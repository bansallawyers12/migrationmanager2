<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
        'password',
        'display_name',
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
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

    /**
     * Get SMTP host with default fallback
     */
    public function getSmtpHostAttribute($value)
    {
        return $value ?? 'smtp.zoho.com';
    }

    /**
     * Get SMTP port with default fallback
     */
    public function getSmtpPortAttribute($value)
    {
        return $value ?? 587;
    }

    /**
     * Get SMTP encryption with default fallback
     */
    public function getSmtpEncryptionAttribute($value)
    {
        return $value ?? 'tls';
    }
} 

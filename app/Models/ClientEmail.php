<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientEmail extends Model
{
    protected $table = 'client_emails';

    protected $fillable = [
        'admin_id',
		'client_id',
        'email_type',
        'email',

    ];
}



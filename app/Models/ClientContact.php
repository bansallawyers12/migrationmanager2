<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientContact extends Model
{
    protected $table = 'client_contacts';

    protected $fillable = [
        'admin_id',
		'client_id',
        'contact_type',
        'country_code',
        'phone',
    ];
}



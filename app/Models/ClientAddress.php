<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientAddress extends Model
{
    protected $table = 'client_addresses';

    protected $fillable = [
        'admin_id',
		'client_id', // New field added
        'address',
        'city',
        'state',
        'zip',
		'regional_code',
        'start_date',
        'end_date',
        'is_current'
    ];
}



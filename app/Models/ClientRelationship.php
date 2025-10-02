<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientRelationship extends Model
{
    protected $table = 'client_relationships';

    protected $fillable = [
        'admin_id',
        'client_id',
        'related_client_id',
        'details',
        'relationship_type',
        'company_type',
        'email',
        'first_name',
        'last_name',
        'phone',
        'gender',
        'dob',
    ];
}



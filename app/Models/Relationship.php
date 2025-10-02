<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relationship extends Model
{
    /*protected $table = 'client_addresses';

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
    ];*/
	
	protected $fillable = ['user_id', 'related_user_id', 'relation_type'];

    public function relatedUser()
    {
        return $this->belongsTo(Admin::class, 'related_user_id');
    }
}



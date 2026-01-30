<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Agent extends Authenticatable
{
    use Notifiable;
	use Sortable;
	protected $guard = 'agents';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    /** 
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */ 
    protected $hidden = [
        'password', 'remember_token',
    ];
	
    protected $fillable = [
        'id', 'full_name', 'agent_type', 'related_office', 'struture', 'business_name', 'tax_number', 'contract_expiry_date', 'country_code', 'phone', 'email', 'address', 'city', 'state', 'created_at', 'updated_at', 'country', 'income_sharing', 'claim_revenue'
    ];
	
}



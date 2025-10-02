<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Product extends Authenticatable
{
    use Notifiable;
	use Sortable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	 
	protected $fillable = [
        'id', 'name', 'partner', 'branches', 'product_type', 'revenue_type', 'duration', 'intakemonth', 'descripton', 'note', 'created_at', 'updated_at'
    ];

    /** 
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */ 
    protected $hidden = [
        'password', 'remember_token',
    ];	
	
	public function branchdetail()
    {
        return $this->belongsTo('App\\Models\\PartnerBranch','branches','id');
    }
	
	public function partnerdetail()
    {
        return $this->belongsTo('App\\Models\\Partner','partner','id');
    }
}

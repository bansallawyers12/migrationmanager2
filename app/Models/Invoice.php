<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Invoice extends Authenticatable
{
    use Notifiable;
	use Sortable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	
	
	protected $fillable = [
        'id', 'customer_id', 'created_at', 'updated_at'
    ];
  
	public $sortable = ['id', 'created_at', 'updated_at'];
 
	public function user()
    {
        return $this->belongsTo('App\\Models\\\Models\\Admin','user_id','id');
    }
	
	public function company()
    {
        return $this->belongsTo('App\\Models\\\Models\\Admin','user_id','id');
    }
	
	public function staff()
    {
        return $this->belongsTo('App\\Models\\\Models\\Admin','seller_id','id');
    }
	
	public function customer()
    {
        return $this->belongsTo('App\\Models\\\Models\\Admin','client_id','id');
    }
	public function invoicedetail() 
    {
        return $this->hasMany('App\\Models\\InvoiceDetail','invoice_id','id');
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class NatureOfEnquiry extends Model {
	use Sortable;


    protected $table = 'nature_of_enquiry';

	protected $fillable = [
        'id','title','status','created_at','updated_at'
    ];
	
	public $sortable = ['id','title','status','created_at','updated_at'];
}
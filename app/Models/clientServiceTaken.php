<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
// use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class clientServiceTaken extends Authenticatable {
    use Notifiable;
	// use Sortable;

	protected $fillable = [
        'id', '	client_id', 'service_type', 'mig_ref_no', 'mig_service','mig_notes','edu_course','edu_college','edu_service_start_date','edu_notes','created_at', 'updated_at'
    ];

	public $sortable = ['id','created_at', 'updated_at'];

}

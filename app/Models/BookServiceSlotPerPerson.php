<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class BookServiceSlotPerPerson extends Authenticatable {
    use Notifiable;
	use Sortable;
	 protected $table = 'book_service_slot_per_persons';
	protected $fillable = ['id', 'person_id', 'service_type', 'start_time', 'end_time', 'weekend' ,'disabledates', 'created_at', 'updated_at'];

	public $sortable = ['id', 'created_at', 'updated_at'];
}

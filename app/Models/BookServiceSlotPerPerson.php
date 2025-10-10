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

	// Relationship to BookService
	public function bookService()
	{
		return $this->belongsTo(BookService::class, 'service_type');
	}

	// Relationship to BookServiceDisableSlots
	public function disableSlots()
	{
		return $this->hasMany(BookServiceDisableSlot::class, 'book_service_slot_per_person_id');
	}

	// Get person name
	public function getPersonNameAttribute()
	{
		$personNames = [
			1 => 'Arun',
			2 => 'Shubam', 
			3 => 'Tourist',
			4 => 'Education',
			5 => 'Adelaide'
		];
		
		return $personNames[$this->person_id] ?? "User{$this->person_id}";
	}
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class BookServiceDisableSlot extends Model {
    use Sortable;

    protected $table = 'book_service_disable_slots';
    
    protected $fillable = [
        'id', 'book_service_slot_per_person_id', 'disabledates', 'slots', 'block_all', 'created_at', 'updated_at'
    ];

    public $sortable = ['id', 'disabledates', 'created_at', 'updated_at'];

    // Relationship to BookServiceSlotPerPerson
    public function slotPerPerson()
    {
        return $this->belongsTo(BookServiceSlotPerPerson::class, 'book_service_slot_per_person_id');
    }

    // Accessor for formatted date
    public function getFormattedDateAttribute()
    {
        return date('d/m/Y', strtotime($this->disabledates));
    }

    // Accessor for formatted slots
    public function getFormattedSlotsAttribute()
    {
        if ($this->block_all == 1) {
            return 'Full Day Blocked';
        }
        return $this->slots;
    }

    // Scope for full day blocks
    public function scopeFullDay($query)
    {
        return $query->where('block_all', 1);
    }

    // Scope for specific time slots
    public function scopeTimeSlots($query)
    {
        return $query->where('block_all', 0);
    }

    // Get person name from related slot
    public function getPersonNameAttribute()
    {
        if ($this->slotPerPerson) {
            return $this->getPersonNameById($this->slotPerPerson->person_id);
        }
        return 'Unknown';
    }

    // Person name mapping
    public function getPersonNameById($personId)
    {
        $personNames = [
            1 => 'Arun',
            2 => 'Shubam', 
            3 => 'Tourist',
            4 => 'Education',
            5 => 'Adelaide'
        ];
        
        return $personNames[$personId] ?? "User{$personId}";
    }
}

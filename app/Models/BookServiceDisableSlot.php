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

    // Scope for current and future dates only
    public function scopeCurrentAndFuture($query)
    {
        return $query->where('disabledates', '>=', date('Y-m-d'));
    }

    // Scope for past dates only
    public function scopePastDates($query)
    {
        return $query->where('disabledates', '<', date('Y-m-d'));
    }

    // Scope for today's dates
    public function scopeToday($query)
    {
        return $query->where('disabledates', date('Y-m-d'));
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

    /**
     * Clean up past dates - static method for easy access
     * Returns the count of deleted records
     */
    public static function cleanupPastDates()
    {
        try {
            $today = date('Y-m-d');
            
            // Count how many records will be deleted
            $pastSlotsCount = self::pastDates()->count();
            
            if ($pastSlotsCount > 0) {
                // Delete past disabled slots
                $deletedCount = self::pastDates()->delete();
                
                // Log the cleanup activity
                \Log::info("BookServiceDisableSlot Cleanup: Removed {$deletedCount} past date records on " . date('Y-m-d H:i:s'));
                
                return $deletedCount;
            }
            
            return 0;
            
        } catch (\Exception $e) {
            // Log any errors during cleanup
            \Log::error("Error during BookServiceDisableSlot cleanup: " . $e->getMessage());
            return false;
        }
    }
}

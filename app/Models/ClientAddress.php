<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ClientAddress extends Model
{
    protected $table = 'client_addresses';

    /**
     * Matches CRM Address Information list order (latest start_date first; ties by created_at, then id).
     * Use with orderByRaw() on query builder when not using {@see scopeOrderedForDisplay}.
     */
    public const ORDER_BY_DISPLAY_SQL = 'start_date DESC NULLS LAST, created_at DESC';

    protected $fillable = [
        'admin_id',
		'client_id', // New field added
        'address',           // Keep for backward compatibility
        'address_line_1',    // NEW
        'address_line_2',    // NEW
        'suburb',
        'state',
        'country',           // NEW
        'zip',               // Existing
        'regional_code',     // Existing
        'start_date',        // Existing
        'end_date',          // Existing
        'is_current'         // Existing
    ];

    /**
     * Same ordering as the client edit Address section (Option A — first row = canonical "current").
     */
    public function scopeOrderedForDisplay(Builder $query): Builder
    {
        $table = $query->getModel()->getTable();

        return $query->orderByRaw(static::ORDER_BY_DISPLAY_SQL)
            ->orderByDesc($table.'.id');
    }

    /**
     * Set exactly one is_current=1 per client: the first row by {@see scopeOrderedForDisplay}.
     * Does not alter updated_at (avoids mass-touching rows when only the flag changes).
     */
    public static function syncIsCurrentForClient(int $clientId): void
    {
        $winner = static::query()
            ->where('client_id', $clientId)
            ->orderedForDisplay()
            ->first(['id']);

        if ($winner === null) {
            return;
        }

        static::withoutTimestamps(function () use ($clientId, $winner): void {
            static::where('client_id', $clientId)->update(['is_current' => 0]);
            static::where('id', $winner->id)->update(['is_current' => 1]);
        });
    }
}



<?php

namespace App\Support;

use Carbon\Carbon;

class AppointmentSlotOverwrite
{
    /**
     * CRM schedule form sends slot_overwrite_hidden (0/1). Checkbox is a fallback.
     * Only the integer 1 enables overwrite; any other value is 0.
     *
     * @param  array<string, mixed>  $input
     */
    public static function fromRequest(array $input): int
    {
        $hidden = (int) ($input['slot_overwrite_hidden'] ?? 0);
        $checkbox = (int) ($input['slot_overwrite'] ?? 0);

        return ($hidden === 1 || $checkbox === 1) ? 1 : 0;
    }

    /**
     * CRM calendar closed weekdays (JS Date.getDay() / Carbon: Sun=0 … Sat=6).
     * Overwrite off leaves the list unchanged. Overwrite on drops Friday only.
     *
     * @param  list<int|string>  $weeks
     * @return list<int|string>
     */
    public static function closedWeekdaysForCrmCalendar(array $weeks, int $slotOverwrite): array
    {
        if ($slotOverwrite !== 1) {
            return array_values($weeks);
        }

        return array_values(array_filter(
            $weeks,
            static fn (mixed $day): bool => (int) $day !== Carbon::FRIDAY
        ));
    }
}

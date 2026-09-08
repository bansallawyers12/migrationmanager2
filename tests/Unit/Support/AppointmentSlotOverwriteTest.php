<?php

namespace Tests\Unit\Support;

use App\Support\AppointmentSlotOverwrite;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AppointmentSlotOverwriteTest extends TestCase
{
    #[Test]
    public function it_defaults_to_off_when_fields_are_missing(): void
    {
        $this->assertSame(0, AppointmentSlotOverwrite::fromRequest([]));
    }

    #[Test]
    public function it_enables_when_hidden_flag_is_one(): void
    {
        $this->assertSame(1, AppointmentSlotOverwrite::fromRequest([
            'slot_overwrite_hidden' => '1',
            'slot_overwrite' => '0',
        ]));
    }

    #[Test]
    public function it_enables_when_checkbox_is_one_even_if_hidden_is_zero(): void
    {
        $this->assertSame(1, AppointmentSlotOverwrite::fromRequest([
            'slot_overwrite_hidden' => 0,
            'slot_overwrite' => 1,
        ]));
    }

    #[Test]
    public function it_stays_off_for_truthy_non_one_values(): void
    {
        $this->assertSame(0, AppointmentSlotOverwrite::fromRequest([
            'slot_overwrite_hidden' => 'true',
            'slot_overwrite' => 'on',
        ]));
    }

    #[Test]
    public function it_keeps_friday_saturday_sunday_closed_when_overwrite_is_off(): void
    {
        $this->assertSame([0, 5, 6], AppointmentSlotOverwrite::closedWeekdaysForCrmCalendar([0, 5, 6], 0));
    }

    #[Test]
    public function it_does_not_rewrite_weekday_values_when_overwrite_is_off(): void
    {
        $this->assertSame(['0', '5', '6'], AppointmentSlotOverwrite::closedWeekdaysForCrmCalendar(['0', '5', '6'], 0));
    }

    #[Test]
    public function it_opens_friday_only_when_overwrite_is_on(): void
    {
        $this->assertSame([0, 6], AppointmentSlotOverwrite::closedWeekdaysForCrmCalendar([0, 5, 6], 1));
    }

    #[Test]
    public function it_opens_friday_from_string_weekday_values_when_overwrite_is_on(): void
    {
        $this->assertSame(['0', '6'], AppointmentSlotOverwrite::closedWeekdaysForCrmCalendar(['0', '5', '6'], 1));
    }

    #[Test]
    public function it_leaves_weeks_unchanged_when_friday_is_not_listed(): void
    {
        $this->assertSame([0], AppointmentSlotOverwrite::closedWeekdaysForCrmCalendar([0], 1));
    }
}

<?php

namespace Tests\Unit\Mail;

use App\Mail\AppointmentClientConfirmed;
use Illuminate\Mail\Mailables\Attachment;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AppointmentClientConfirmedContentTest extends TestCase
{
    #[Test]
    public function it_shows_start_time_only_after_email_confirm(): void
    {
        $mailable = new AppointmentClientConfirmed($this->details());
        $html = $mailable->render();

        $this->assertStringContainsString('Appointment Confirmed - Bansal Immigration', $html);
        $this->assertStringContainsString('10:20 AM', $html);
        $this->assertStringNotContainsString('10:40 AM', $html);
        $this->assertStringContainsString('Tourist Visa', $html);
        $this->assertStringContainsString('In-Person', $html);
        $this->assertStringContainsString('Bansal Immigration Consultant', $html);
        $this->assertStringNotContainsString('Bansal Immigration Team', $html);
        $this->assertStringNotContainsString('info@bansalimmigration.com.au', $html);
        $this->assertStringContainsString('Registered Migration Agents', $html);
        $this->assertStringContainsString('Appointment Details', $html);
        $this->assertStringContainsString('CONFIRMED', $html);
        $this->assertStringContainsString('width:50%', $html);
        $this->assertStringContainsString('max-width:240px', $html);
        $this->assertStringNotContainsString('>Cancel</a>', $html);
        $this->assertStringNotContainsString('>Confirm</a>', $html);
        $this->assertStringNotContainsString('This appointment stays pending until you confirm it', $html);
        $mailable->assertHasAttachment(
            Attachment::fromPath(public_path('img/logo.png'))
                ->as('Bansal-Immigration-Logo.png')
                ->withMime('image/png')
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function details(): array
    {
        return [
            'client_name' => 'Vipul Kumar',
            'appointment_datetime' => now()->setTime(10, 20),
            'timeslot_full' => '10:20 AM-10:40 AM',
            'location' => 'melbourne',
            'meeting_type' => 'in_person',
            'service_type' => 'Tourist Visa',
        ];
    }
}

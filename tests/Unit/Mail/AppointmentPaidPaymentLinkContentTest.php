<?php

namespace Tests\Unit\Mail;

use App\Mail\AppointmentPaidPaymentLink;
use App\Models\BookingAppointment;
use Illuminate\Mail\Mailables\Attachment;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AppointmentPaidPaymentLinkContentTest extends TestCase
{
    #[Test]
    public function it_uses_the_new_appointment_email_design_and_start_time_only(): void
    {
        $appointment = new BookingAppointment([
            'client_name' => 'Vipul Kumar',
            'appointment_datetime' => now()->addDay(),
            'timeslot_full' => '10:30 AM - 11:00 AM',
            'location' => 'melbourne',
            'meeting_type' => 'phone',
            'service_type' => 'Education/Student Visa',
            'final_amount' => 150,
            'amount' => 150,
            'client_timezone' => 'Australia/Melbourne',
        ]);

        $mailable = new AppointmentPaidPaymentLink($appointment, 'https://example.test/pay');
        $html = $mailable->render();

        $this->assertStringContainsString('Registered Migration Agents', $html);
        $this->assertStringContainsString('width:50%', $html);
        $this->assertStringContainsString('max-width:240px', $html);
        $this->assertStringContainsString('Appointment Details', $html);
        $this->assertStringContainsString('10:30 AM', $html);
        $this->assertStringNotContainsString('11:00 AM', $html);
        $this->assertStringNotContainsString('Appointment Payment Required', $html);
        $this->assertStringContainsString('Pay now securely', $html);
        $this->assertStringContainsString('$150.00 AUD', $html);
        $this->assertStringContainsString('Bansal Immigration Consultant', $html);
        $this->assertStringNotContainsString('Bansal Immigration Team', $html);
        $this->assertStringNotContainsString('info@bansalimmigration.com.au', $html);
        $mailable->assertHasAttachment(
            Attachment::fromPath(public_path('img/logo.png'))
                ->as('Bansal-Immigration-Logo.png')
                ->withMime('image/png')
        );
    }
}

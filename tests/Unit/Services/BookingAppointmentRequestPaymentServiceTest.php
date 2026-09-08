<?php

namespace Tests\Unit\Services;

use App\Models\BookingAppointment;
use App\Services\AppointmentPaymentLinkService;
use App\Services\BansalAppointmentSync\NotificationService;
use App\Services\BookingAppointmentRequestPaymentService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookingAppointmentRequestPaymentServiceTest extends TestCase
{
    #[Test]
    public function it_rejects_already_paid_appointments(): void
    {
        $notifications = Mockery::mock(NotificationService::class);
        $notifications->shouldNotReceive('sendPaidAppointmentPaymentEmail');

        $appointment = new BookingAppointment([
            'is_paid' => true,
            'payment_status' => 'completed',
            'status' => 'paid',
            'client_email' => 'client@example.test',
        ]);

        $result = (new BookingAppointmentRequestPaymentService($notifications))->sendPaymentRequest($appointment);

        $this->assertFalse($result['success']);
        $this->assertSame('This appointment is already marked as Paid.', $result['message']);
    }

    #[Test]
    public function it_rejects_cancelled_appointments(): void
    {
        $notifications = Mockery::mock(NotificationService::class);
        $notifications->shouldNotReceive('sendPaidAppointmentPaymentEmail');

        $appointment = new BookingAppointment([
            'is_paid' => false,
            'status' => 'cancelled',
            'client_email' => 'client@example.test',
        ]);

        $result = (new BookingAppointmentRequestPaymentService($notifications))->sendPaymentRequest($appointment);

        $this->assertFalse($result['success']);
    }

    #[Test]
    public function it_sets_amount_keeps_free_and_sends_payment_email(): void
    {
        $appointment = Mockery::mock(BookingAppointment::class)->makePartial();
        $appointment->forceFill([
            'id' => 20,
            'is_paid' => false,
            'status' => 'confirmed',
            'payment_status' => null,
            'amount' => 0,
            'final_amount' => 0,
            'client_email' => 'client@example.test',
        ]);
        $appointment->shouldReceive('save')->once()->andReturnTrue();
        $appointment->shouldReceive('fresh')->once()->andReturn($appointment);

        $notifications = Mockery::mock(NotificationService::class);
        $notifications->shouldReceive('sendPaidAppointmentPaymentEmail')
            ->once()
            ->with(Mockery::type(BookingAppointment::class))
            ->andReturnTrue();

        $result = (new BookingAppointmentRequestPaymentService($notifications))->sendPaymentRequest($appointment);

        $this->assertTrue($result['success']);
        $this->assertFalse($appointment->is_paid);
        $this->assertSame('confirmed', $appointment->status);
        $this->assertSame('pending', $appointment->payment_status);
        $this->assertEquals(150.00, (float) $appointment->final_amount);
        $this->assertTrue((new AppointmentPaymentLinkService)->requiresOnlinePayment($appointment));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

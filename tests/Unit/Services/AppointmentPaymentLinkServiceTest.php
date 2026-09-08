<?php

namespace Tests\Unit\Services;

use App\Models\BookingAppointment;
use App\Services\AppointmentPaymentLinkService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AppointmentPaymentLinkServiceTest extends TestCase
{
    #[Test]
    public function paid_bookings_still_require_online_payment_until_completed(): void
    {
        $service = new AppointmentPaymentLinkService;
        $appointment = new BookingAppointment([
            'is_paid' => true,
            'payment_status' => 'pending',
            'status' => 'pending',
            'final_amount' => 150,
        ]);

        $this->assertTrue($service->requiresOnlinePayment($appointment));
    }

    #[Test]
    public function free_zero_amount_bookings_do_not_require_online_payment(): void
    {
        $service = new AppointmentPaymentLinkService;
        $appointment = new BookingAppointment([
            'is_paid' => false,
            'payment_status' => null,
            'status' => 'confirmed',
            'final_amount' => 0,
        ]);

        $this->assertFalse($service->requiresOnlinePayment($appointment));
    }

    #[Test]
    public function free_requested_payment_requires_online_payment(): void
    {
        $service = new AppointmentPaymentLinkService;
        $appointment = new BookingAppointment([
            'is_paid' => false,
            'payment_status' => 'pending',
            'status' => 'confirmed',
            'final_amount' => 150,
        ]);

        $this->assertTrue($service->requiresOnlinePayment($appointment));
    }

    #[Test]
    public function completed_or_cancelled_bookings_do_not_require_online_payment(): void
    {
        $service = new AppointmentPaymentLinkService;

        $completed = new BookingAppointment([
            'is_paid' => true,
            'payment_status' => 'completed',
            'status' => 'paid',
            'final_amount' => 150,
        ]);
        $cancelled = new BookingAppointment([
            'is_paid' => true,
            'payment_status' => 'pending',
            'status' => 'cancelled',
            'final_amount' => 150,
        ]);

        $this->assertFalse($service->requiresOnlinePayment($completed));
        $this->assertFalse($service->requiresOnlinePayment($cancelled));
        $this->assertTrue($service->paymentAlreadyCompleted($completed));
    }
}

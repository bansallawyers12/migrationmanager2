<?php

namespace Tests\Unit\Services;

use App\Models\BookingAppointment;
use App\Services\AppointmentPaymentLinkService;
use App\Services\BansalAppointmentSync\BansalAppointmentRecoveryService;
use App\Services\BookingAppointmentManualPaymentService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookingAppointmentManualPaymentServiceTest extends TestCase
{
    #[Test]
    public function it_rejects_appointments_already_marked_paid(): void
    {
        $recovery = Mockery::mock(BansalAppointmentRecoveryService::class);
        $recovery->shouldNotReceive('syncStatus');

        $appointment = new BookingAppointment([
            'is_paid' => true,
            'status' => 'paid',
            'payment_status' => 'completed',
            'final_amount' => 150,
        ]);

        $result = (new BookingAppointmentManualPaymentService($recovery))->markPaidOffline($appointment);

        $this->assertFalse($result['success']);
        $this->assertTrue($result['already_paid']);
        $this->assertSame('This appointment is already marked as Paid.', $result['message']);
    }

    #[Test]
    public function it_completes_manual_paid_booking_that_still_has_zero_amount(): void
    {
        $appointment = $this->partialAppointment([
            'id' => 13,
            'is_paid' => true,
            'status' => 'awaiting_confirmation',
            'payment_status' => 'completed',
            'payment_method' => 'manual',
            'final_amount' => 0,
            'amount' => 0,
            'bansal_appointment_id' => null,
        ]);
        $appointment->shouldReceive('save')->once()->andReturnTrue();

        $recovery = Mockery::mock(BansalAppointmentRecoveryService::class);
        $recovery->shouldNotReceive('syncStatus');

        $result = (new BookingAppointmentManualPaymentService($recovery))->markPaidOffline($appointment);

        $this->assertTrue($result['success']);
        $this->assertSame('paid', $appointment->status);
        $this->assertEquals(150.00, (float) $appointment->final_amount);
    }

    #[Test]
    public function it_marks_free_booking_paid_with_amount_and_syncs_bansal(): void
    {
        $appointment = $this->partialAppointment([
            'id' => 10,
            'is_paid' => false,
            'status' => 'confirmed',
            'payment_status' => null,
            'payment_method' => null,
            'paid_at' => null,
            'bansal_appointment_id' => 555,
            'consultant_id' => 7,
            'meeting_type' => 'phone',
            'amount' => 0,
            'final_amount' => 0,
            'client_id' => null,
        ]);
        $appointment->shouldReceive('save')->twice()->andReturnTrue();

        $recovery = Mockery::mock(BansalAppointmentRecoveryService::class);
        $recovery->shouldReceive('syncStatus')
            ->once()
            ->with(Mockery::type(BookingAppointment::class), 'paid')
            ->andReturn([
                'synced' => true,
                'error' => null,
                'bansal_appointment_id' => 555,
                'created_new' => false,
            ]);

        $result = (new BookingAppointmentManualPaymentService($recovery))->markPaidOffline($appointment);

        $this->assertTrue($result['success']);
        $this->assertNull($result['sync_error']);
        $this->assertTrue($appointment->is_paid);
        $this->assertSame('completed', $appointment->payment_status);
        $this->assertSame('manual', $appointment->payment_method);
        $this->assertSame('paid', $appointment->status);
        $this->assertSame(7, $appointment->consultant_id);
        $this->assertSame('phone', $appointment->meeting_type);
        $this->assertEquals(150.00, (float) $appointment->amount);
        $this->assertEquals(150.00, (float) $appointment->final_amount);
        $this->assertFalse((new AppointmentPaymentLinkService)->requiresOnlinePayment($appointment));
    }

    #[Test]
    public function it_updates_crm_when_bansal_id_is_missing(): void
    {
        $appointment = $this->partialAppointment([
            'id' => 11,
            'is_paid' => false,
            'status' => 'confirmed',
            'bansal_appointment_id' => null,
        ]);
        $appointment->shouldReceive('save')->once()->andReturnTrue();

        $recovery = Mockery::mock(BansalAppointmentRecoveryService::class);
        $recovery->shouldNotReceive('syncStatus');

        $result = (new BookingAppointmentManualPaymentService($recovery))->markPaidOffline($appointment);

        $this->assertTrue($result['success']);
        $this->assertTrue($appointment->is_paid);
        $this->assertSame('Missing Bansal appointment identifier.', $result['sync_error']);
        $this->assertSame('paid', $appointment->status);
        $this->assertEquals(150.00, (float) $appointment->final_amount);
    }

    #[Test]
    public function it_keeps_crm_paid_when_bansal_sync_fails(): void
    {
        $appointment = $this->partialAppointment([
            'id' => 12,
            'is_paid' => false,
            'status' => 'awaiting_confirmation',
            'bansal_appointment_id' => 777,
        ]);
        $appointment->shouldReceive('save')->twice()->andReturnTrue();

        $recovery = Mockery::mock(BansalAppointmentRecoveryService::class);
        $recovery->shouldReceive('syncStatus')
            ->once()
            ->andReturn([
                'synced' => false,
                'error' => 'Website unavailable',
                'bansal_appointment_id' => null,
                'created_new' => false,
            ]);

        $result = (new BookingAppointmentManualPaymentService($recovery))->markPaidOffline($appointment);

        $this->assertTrue($result['success']);
        $this->assertTrue($appointment->is_paid);
        $this->assertSame('Website unavailable', $result['sync_error']);
        $this->assertSame('paid', $appointment->status);
        $this->assertEquals(150.00, (float) $appointment->final_amount);
        $this->assertSame('error', $appointment->sync_status);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function partialAppointment(array $attributes): BookingAppointment
    {
        $appointment = Mockery::mock(BookingAppointment::class)->makePartial();
        $appointment->forceFill($attributes);

        return $appointment;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

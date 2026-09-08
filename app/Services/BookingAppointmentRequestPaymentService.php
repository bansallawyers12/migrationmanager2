<?php

namespace App\Services;

use App\Models\BookingAppointment;
use App\Services\BansalAppointmentSync\NotificationService;

class BookingAppointmentRequestPaymentService
{
    public const REQUEST_PAYMENT_AMOUNT = BookingAppointmentManualPaymentService::MANUAL_PAYMENT_AMOUNT;

    public function __construct(
        protected NotificationService $notificationService,
    ) {}

    /**
     * Prepare a Free booking for a $150 Stripe payment email. Does not mark it Paid.
     *
     * @return array{success: bool, message: string}
     */
    public function sendPaymentRequest(BookingAppointment $appointment): array
    {
        if ($appointment->is_paid && $appointment->payment_status === 'completed') {
            return [
                'success' => false,
                'message' => 'This appointment is already marked as Paid.',
            ];
        }

        if (in_array($appointment->status, ['cancelled', 'completed'], true)) {
            return [
                'success' => false,
                'message' => 'Payment cannot be requested for a cancelled or completed appointment.',
            ];
        }

        if (empty($appointment->client_email)) {
            return [
                'success' => false,
                'message' => 'This appointment has no client email address.',
            ];
        }

        $appointment->forceFill([
            'amount' => self::REQUEST_PAYMENT_AMOUNT,
            'final_amount' => self::REQUEST_PAYMENT_AMOUNT,
            'payment_status' => 'pending',
            'is_paid' => false,
        ]);
        $appointment->save();

        $sent = $this->notificationService->sendPaidAppointmentPaymentEmail($appointment->fresh() ?? $appointment);

        if (! $sent) {
            return [
                'success' => false,
                'message' => 'Could not send the payment email. Please try again.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Payment request email sent to the client.',
        ];
    }
}

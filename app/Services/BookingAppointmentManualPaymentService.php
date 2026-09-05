<?php

namespace App\Services;

use App\Models\BookingAppointment;
use App\Services\BansalAppointmentSync\BansalAppointmentRecoveryService;
use Illuminate\Support\Facades\Log;

class BookingAppointmentManualPaymentService
{
    public const MANUAL_PAYMENT_AMOUNT = 150.00;

    public function __construct(
        protected BansalAppointmentRecoveryService $recoveryService,
    ) {}

    /**
     * Mark a Free booking as Paid offline and set the paid-consultation amount.
     *
     * @return array{success: bool, already_paid: bool, sync_error: ?string, message: string}
     */
    public function markPaidOffline(BookingAppointment $appointment): array
    {
        if ($this->isAlreadyManuallyPaid($appointment)) {
            return [
                'success' => false,
                'already_paid' => true,
                'sync_error' => null,
                'message' => 'This appointment is already marked as Paid.',
            ];
        }

        $appointment->forceFill([
            'status' => 'paid',
            'is_paid' => true,
            'payment_status' => 'completed',
            'payment_method' => 'manual',
            'paid_at' => now(),
            'amount' => self::MANUAL_PAYMENT_AMOUNT,
            'final_amount' => self::MANUAL_PAYMENT_AMOUNT,
        ]);
        $appointment->save();

        $syncError = $this->syncPaidWithBansal($appointment);

        return [
            'success' => true,
            'already_paid' => false,
            'sync_error' => $syncError,
            'message' => $syncError
                ? 'Payment updated locally. Sync with website failed: '.$syncError
                : 'Payment type updated from Free to Paid.',
        ];
    }

    protected function syncPaidWithBansal(BookingAppointment $appointment): ?string
    {
        if (empty($appointment->bansal_appointment_id)) {
            Log::warning('Skipping Bansal pay sync because appointment is missing bansal_appointment_id', [
                'appointment_id' => $appointment->id,
            ]);

            return 'Missing Bansal appointment identifier.';
        }

        $result = $this->recoveryService->syncStatus($appointment, 'paid');

        if ($result['synced']) {
            if ($result['bansal_appointment_id'] !== null) {
                $appointment->bansal_appointment_id = $result['bansal_appointment_id'];
            }

            $appointment->forceFill([
                'last_synced_at' => now(),
                'sync_status' => 'synced',
                'sync_error' => null,
            ])->save();

            return null;
        }

        $syncError = $result['error'];

        Log::error('Failed to sync manual payment with Bansal API', [
            'appointment_id' => $appointment->id,
            'bansal_appointment_id' => $appointment->bansal_appointment_id,
            'error' => $syncError,
        ]);

        $appointment->forceFill([
            'sync_status' => 'error',
            'sync_error' => $syncError,
        ])->save();

        return $syncError;
    }

    protected function isAlreadyManuallyPaid(BookingAppointment $appointment): bool
    {
        return (bool) $appointment->is_paid
            && $appointment->status === 'paid'
            && (float) ($appointment->final_amount ?? 0) >= self::MANUAL_PAYMENT_AMOUNT;
    }
}

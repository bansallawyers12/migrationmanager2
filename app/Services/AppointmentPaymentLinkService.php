<?php

namespace App\Services;

use App\Models\BookingAppointment;
use Illuminate\Support\Str;

class AppointmentPaymentLinkService
{
    /** Days until payment link expires (null token expiry = no expiry check). */
    public const TOKEN_TTL_DAYS = 90;

    public const ALREADY_PAID_MESSAGE = 'Payment is already done. Thank you.';

    public const INVALID_LINK_MESSAGE = 'This payment link is invalid, expired, or payment has already been completed.';

    public function requiresOnlinePayment(BookingAppointment $appointment): bool
    {
        if (in_array($appointment->status, ['cancelled', 'completed'], true)) {
            return false;
        }

        if ($appointment->payment_status === 'completed') {
            return false;
        }

        if ((bool) $appointment->is_paid) {
            return true;
        }

        $amount = (float) ($appointment->final_amount ?? $appointment->amount ?? 0);

        return $amount > 0 && $appointment->payment_status === 'pending';
    }

    public function paymentAlreadyCompleted(BookingAppointment $appointment): bool
    {
        return $appointment->payment_status === 'completed'
            || ((bool) $appointment->is_paid && $appointment->status === 'paid');
    }

    public function findByPaymentToken(string $token): ?BookingAppointment
    {
        $token = trim($token);
        if ($token === '') {
            return null;
        }

        return BookingAppointment::where('payment_token', $token)->first();
    }

    public function ensurePaymentToken(BookingAppointment $appointment): BookingAppointment
    {
        if (! $this->requiresOnlinePayment($appointment)) {
            return $appointment;
        }

        if (! empty($appointment->payment_token) && ! $this->tokenExpired($appointment)) {
            return $appointment;
        }

        $appointment->payment_token = Str::random(48);
        $appointment->payment_token_expires_at = now()->addDays(self::TOKEN_TTL_DAYS);
        $appointment->save();

        return $appointment->fresh();
    }

    public function paymentUrl(BookingAppointment $appointment): ?string
    {
        if (empty($appointment->payment_token)) {
            return null;
        }

        return route('public.appointment.pay', ['token' => $appointment->payment_token]);
    }

    public function findPayableAppointment(string $token): ?BookingAppointment
    {
        $appointment = $this->findByPaymentToken($token);
        if (! $appointment || ! $this->requiresOnlinePayment($appointment)) {
            return null;
        }

        if ($this->tokenExpired($appointment)) {
            return null;
        }

        return $appointment;
    }

    protected function tokenExpired(BookingAppointment $appointment): bool
    {
        if ($appointment->payment_token_expires_at === null) {
            return false;
        }

        return $appointment->payment_token_expires_at->isPast();
    }
}

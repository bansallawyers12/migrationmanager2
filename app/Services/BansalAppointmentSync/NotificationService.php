<?php

namespace App\Services\BansalAppointmentSync;

use App\Models\BookingAppointment;
use App\Services\Sms\UnifiedSmsManager;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected UnifiedSmsManager $smsManager;

    public function __construct(UnifiedSmsManager $smsManager)
    {
        $this->smsManager = $smsManager;
    }

    /**
     * Send detailed follow-up confirmation email
     * (This is sent AFTER customer already got instant confirmation from Bansal website)
     */
    public function sendDetailedConfirmationEmail(BookingAppointment $appointment): bool
    {
        try {
            // Only send if not already sent
            if ($appointment->confirmation_email_sent) {
                return true;
            }

            $details = [
                'client_name' => $appointment->client_name,
                'appointment_datetime' => $appointment->appointment_datetime,
                'timeslot_full' => $appointment->timeslot_full,
                'location' => $appointment->location,
                'consultant' => $appointment->consultant?->name,
                'service_type' => $appointment->service_type,
                'admin_notes' => $appointment->admin_notes,
            ];

            Mail::to($appointment->client_email)->send(
                new \App\Mail\AppointmentDetailedConfirmation($details)
            );

            $appointment->update([
                'confirmation_email_sent' => true,
                'confirmation_email_sent_at' => now()
            ]);

            Log::info('Sent detailed confirmation email', [
                'appointment_id' => $appointment->id,
                'email' => $appointment->client_email
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send confirmation email', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send reminder SMS (24 hours before appointment)
     */
    public function sendReminderSms(BookingAppointment $appointment): bool
    {
        try {
            if ($appointment->reminder_sms_sent) {
                return true;
            }

            $phone = $appointment->client_phone;
            if (empty($phone)) {
                Log::warning('No phone number for reminder SMS', [
                    'appointment_id' => $appointment->id
                ]);
                return false;
            }

            // Get office phone number based on location
            $officePhone = match($appointment->location) {
                'adelaide' => '08 8317 1340',
                'melbourne' => '03 9602 1330',
                default => '1300 859 368' // Fallback to original number
            };

            $message = "BANSAL IMMIGRATION: Reminder - You have an appointment tomorrow at {$appointment->timeslot_full} at our {$appointment->location} office. Please be on time. If you need to reschedule, call us at {$officePhone}.";

            $result = $this->smsManager->sendSms($phone, $message, 'reminder', [
                'appointment_id' => $appointment->id,
                'client_id' => $appointment->client_id,
            ]);

            if ($result['success']) {
                $appointment->update([
                    'reminder_sms_sent' => true,
                    'reminder_sms_sent_at' => now()
                ]);

                Log::info('Sent reminder SMS', [
                    'appointment_id' => $appointment->id,
                    'phone' => $phone
                ]);
            }

            return $result['success'];
        } catch (\Exception $e) {
            Log::error('Failed to send reminder SMS', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send reminders for upcoming appointments (24 hours ahead)
     */
    public function sendUpcomingReminders(): array
    {
        $tomorrow = now()->addDay()->startOfDay();
        $endOfTomorrow = now()->addDay()->endOfDay();

        $appointments = BookingAppointment::where('reminder_sms_sent', false)
            ->where('status', 'confirmed')
            ->whereBetween('appointment_datetime', [$tomorrow, $endOfTomorrow])
            ->get();

        $stats = [
            'total' => $appointments->count(),
            'sent' => 0,
            'failed' => 0
        ];

        foreach ($appointments as $appointment) {
            if ($this->sendReminderSms($appointment)) {
                $stats['sent']++;
            } else {
                $stats['failed']++;
            }
        }

        Log::info('Sent appointment reminders', $stats);

        return $stats;
    }
}


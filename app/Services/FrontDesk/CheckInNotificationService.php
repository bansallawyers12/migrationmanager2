<?php

namespace App\Services\FrontDesk;

use App\Models\BookingAppointment;
use App\Models\FrontDeskCheckIn;
use App\Models\Notification;
use App\Models\Staff;
use App\Events\OfficeVisitNotificationCreated;
use Illuminate\Support\Facades\Log;

class CheckInNotificationService
{
    /**
     * Resolve the assignee for a check-in and send an in-app notification.
     *
     * Priority:
     *  1. Appointment's assigned consultant staff (via assigned_by_admin_id)
     *  2. Appointment's consultant's linked staff (if any)
     *  3. The lead's user_id (assigned staff)
     *
     * Returns the Staff notified, or null if no one could be resolved.
     */
    public function notify(FrontDeskCheckIn $checkIn, Staff $sender): ?Staff
    {
        $recipient = $this->resolveRecipient($checkIn);

        if (!$recipient) {
            Log::info('[FrontDeskCheckIn] No notification recipient resolved', ['check_in_id' => $checkIn->id]);
            return null;
        }

        $clientName = $this->resolveClientName($checkIn);
        $message    = "Front-desk check-in: {$clientName} has arrived" .
                      ($checkIn->claimed_appointment ? ' (has appointment)' : '') .
                      '. Checked in by ' . $sender->full_name . '.';

        $notification = Notification::create([
            'sender_id'         => $sender->id,
            'receiver_id'       => $recipient->id,
            'module_id'         => $checkIn->id,
            'url'               => url('/office-visits/waiting'),
            'notification_type' => 'front_desk_checkin',
            'message'           => $message,
            'seen'              => 0,
            'receiver_status'   => 0,
            'sender_status'     => 1,
        ]);

        // Broadcast via Reverb (reuse the office-visit event shape)
        try {
            broadcast(new OfficeVisitNotificationCreated(
                $notification->id,
                $notification->receiver_id,
                [
                    'id'            => $notification->id,
                    'checkin_id'    => $checkIn->id,
                    'message'       => $message,
                    'sender_name'   => $sender->full_name,
                    'client_name'   => $clientName,
                    'visit_purpose' => $checkIn->visit_reason ?? 'Front-desk check-in',
                    'created_at'    => $notification->created_at?->format('d/m/Y h:i A') ?? now()->format('d/m/Y h:i A'),
                    'url'           => $notification->url,
                ]
            ));
        } catch (\Exception $e) {
            Log::warning('[FrontDeskCheckIn] Broadcast failed (notification still saved)', [
                'notification_id' => $notification->id,
                'error'           => $e->getMessage(),
            ]);
        }

        // Persist who was notified and when
        $checkIn->update([
            'notified_staff_id' => $recipient->id,
            'notified_at'       => now(),
        ]);

        return $recipient;
    }

    private function resolveRecipient(FrontDeskCheckIn $checkIn): ?Staff
    {
        // 1. Via appointment's assigning staff member
        if ($checkIn->appointment_id) {
            $appt = BookingAppointment::find($checkIn->appointment_id);
            if ($appt) {
                if ($appt->assigned_by_admin_id) {
                    $staff = Staff::find($appt->assigned_by_admin_id);
                    if ($staff) {
                        return $staff;
                    }
                }
            }
        }

        // 2. Via lead's assigned-to staff (user_id)
        $adminId = $checkIn->client_id ?? $checkIn->lead_id;
        if ($adminId) {
            $admin = \App\Models\Admin::find($adminId);
            if ($admin && $admin->user_id) {
                $staff = Staff::find($admin->user_id);
                if ($staff) {
                    return $staff;
                }
            }
        }

        return null;
    }

    private function resolveClientName(FrontDeskCheckIn $checkIn): string
    {
        $adminId = $checkIn->client_id ?? $checkIn->lead_id;
        if ($adminId) {
            $admin = \App\Models\Admin::find($adminId);
            if ($admin) {
                return trim($admin->first_name . ' ' . $admin->last_name) ?: 'Walk-in';
            }
        }
        return 'Walk-in';
    }
}

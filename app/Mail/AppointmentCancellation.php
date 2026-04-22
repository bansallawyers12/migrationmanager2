<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentCancellation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public array $details
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Appointment Cancellation - Bansal Immigration',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $clientName = $this->details['client_name'] ?? 'Valued Client';
        $location = $this->details['location'] ?? 'melbourne';
        $locationPhone = $this->getLocationPhone($location);
        $locationPhoneTel = str_replace([' ', '-'], '', $locationPhone);
        $rescheduleBody = "Hi Bansal Immigration Team,\r\n\r\nI would like to reschedule my cancelled appointment. Please let me know your available slots.\r\n\r\nRegards";
        $rescheduleMailtoHref = 'mailto:info@bansalimmigration.com?subject='.rawurlencode(
            'Reschedule Request – '.$clientName
        ).'&body='.rawurlencode($rescheduleBody);

        return new Content(
            view: 'emails.appointment-cancellation',
            with: [
                'clientName' => $clientName,
                'appointmentDate' => $this->details['appointment_datetime']?->format('l, d F Y') ?? 'N/A',
                'appointmentTime' => $this->details['timeslot_full'] ?? 'N/A',
                'location' => ucfirst($location),
                'locationAddress' => $this->getLocationAddress($location),
                'locationPhone' => $locationPhone,
                'locationPhoneTel' => $locationPhoneTel,
                'rescheduleMailtoHref' => $rescheduleMailtoHref,
                'cancellationReason' => $this->details['cancellation_reason'] ?? null,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get full address for location
     */
    protected function getLocationAddress(string $location): string
    {
        return match ($location) {
            'melbourne' => 'Level 8/278 Collins St, Melbourne VIC 3000, Australia',
            'adelaide' => 'Unit 5, 55 Gawler Pl, Adelaide SA 5000, Australia',
            default => 'Bansal Immigration Office',
        };
    }

    /**
     * Get phone number for location (used in appointment emails)
     */
    protected function getLocationPhone(string $location): string
    {
        return match ($location) {
            'adelaide' => '08 8317 1340',
            'melbourne' => '+61 3 9602 1330',
            default => '1300 859 368'
        };
    }
}

<?php

namespace App\Mail;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PublicLeadInquiryAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{name: string, phone: string, email: string, visa_subclass: string|null, address: string|null}  $submitted
     */
    public function __construct(
        public bool $isNewLead,
        public Admin $record,
        public array $submitted
    ) {
    }

    public function build(): self
    {
        $subject = $this->isNewLead
            ? 'Bansal Immigration - Lead is generated'
            : 'Bansal Immigration - User Details updated';

        return $this->subject($subject)->view('emails.public-lead-inquiry-admin');
    }
}

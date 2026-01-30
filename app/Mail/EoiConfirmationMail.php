<?php

namespace App\Mail;

use App\Models\ClientEoiReference;
use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EoiConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $eoiReference;
    public $client;
    public $confirmUrl;
    public $amendUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(ClientEoiReference $eoiReference, Admin $client, string $token)
    {
        $this->eoiReference = $eoiReference;
        $this->client = $client;
        $this->confirmUrl = route('client.eoi.confirm', ['token' => $token]);
        $this->amendUrl = route('client.eoi.amend', ['token' => $token]);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Please Confirm Your EOI Details')
                    ->view('emails.eoi_confirmation');
    }
}

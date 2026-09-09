<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HubdocDibpReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{client_name: string, checklist: string, file_name: string, pdf_path: string}  $receiptData
     */
    public function __construct(public array $receiptData) {}

    public function build(): self
    {
        $mime = $this->receiptData['mime'] ?? 'application/octet-stream';

        return $this->view('emails.hubdoc_dibp_receipt')
            ->subject('Receipt for Hubdoc Processing')
            ->attach($this->receiptData['pdf_path'], [
                'as' => $this->receiptData['file_name'],
                'mime' => $mime,
            ]);
    }
}

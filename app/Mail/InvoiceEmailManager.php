<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceEmailManager extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $array;

    public function __construct($array)
    {
        $this->array = $array;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
     public function build()
     {
         $message = $this->view($this->array['view'])
             ->from($this->array['from'], $this->array['name'])
             ->subject($this->array['subject']);

         $attachOptions = [
             'mime' => 'application/pdf',
         ];

         if (!empty($this->array['file_content'])) {
             $message->attachData(
                 $this->array['file_content'],
                 $this->array['file_name'],
                 $attachOptions
             );
         } elseif (!empty($this->array['file']) && is_file($this->array['file'])) {
             $message->attach($this->array['file'], array_merge($attachOptions, [
                 'as' => $this->array['file_name'],
             ]));
         }

         if (!empty($this->array['email_log_id'])) {
             $emailLogId = (int) $this->array['email_log_id'];
             $message->withSymfonyMessage(function ($symfonyMessage) use ($emailLogId) {
                 app(\App\Services\SystemEmailLogService::class)->attachTrackingHeader($symfonyMessage, $emailLogId);
             });
         }

         return $message;
     }
 }

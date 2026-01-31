<?php

namespace App\Mail;

use App\Models\ClientEoiReference;
use App\Models\Admin;
use App\Services\PointsService;
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
    public $pointsData;

    /** @var array<int, array{data: string, name: string, mime: string}> */
    public $attachments;

    /** @var array<int, string> Category names attached (e.g. EOI Summary, Points Summary, ROI Draft) */
    public $attachmentLabels;

    /**
     * Create a new message instance.
     *
     * @param array<int, array{data: string, name: string, mime: string}> $attachments
     * @param array<int, string> $attachmentLabels
     */
    public function __construct(
        ClientEoiReference $eoiReference,
        Admin $client,
        string $token,
        array $attachments = [],
        array $attachmentLabels = []
    ) {
        $this->eoiReference = $eoiReference;
        $this->client = $client;
        $this->confirmUrl = route('client.eoi.confirm', ['token' => $token]);
        $this->amendUrl = route('client.eoi.amend', ['token' => $token]);
        $this->attachments = $attachments;
        $this->attachmentLabels = $attachmentLabels;

        // Get points calculation with warnings
        $pointsService = app(PointsService::class);
        $subclass = is_array($eoiReference->eoi_subclasses) && count($eoiReference->eoi_subclasses) > 0
            ? $eoiReference->eoi_subclasses[0]
            : null;
        $this->pointsData = $pointsService->compute($client, $subclass);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $mail = $this->subject('Please Confirm Your EOI Details')
            ->view('emails.eoi_confirmation');

        foreach ($this->attachments as $att) {
            $mail->attachData(
                $att['data'],
                $att['name'],
                ['mime' => $att['mime'] ?? 'application/octet-stream']
            );
        }

        return $mail;
    }
}

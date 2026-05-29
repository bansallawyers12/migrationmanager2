<?php

namespace App\Services;

use App\Models\Email;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class EmailService
{
    /**
     * Get all active email configurations.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllActiveEmails()
    {
        return Email::where('status', true)
            ->select('id', 'email', 'display_name')
            ->get();
    }

    /**
     * Send an email using the specified email configuration.
     *
     * @param string $view
     * @param array $data
     * @param string $to
     * @param string $subject
     * @param int $fromEmailId
     * @return bool
     * @throws \Exception
     */
    public function sendEmail($view, $data, $to, $subject, $fromEmailId, $attachments = [], $cc = [], ?int $emailLogId = null)
    {
        try {
            $emailConfig = Email::where('email', $fromEmailId)->first();
            $fromAddress = $emailConfig?->email ?? config('mail.from.address');
            $fromName = $emailConfig?->display_name ?? config('mail.from.name');

            // Send the email
            Mail::mailer('sendgrid')->send($view, $data, function (Message $message) use ($to, $subject, $fromAddress, $fromName, $attachments, $cc, $emailLogId) {
                $message->to($to)
                    ->subject($subject)
                    ->from($fromAddress, $fromName);

                if ($emailLogId !== null) {
                    $message->getSymfonyMessage()->getHeaders()->addTextHeader('X-SMTPAPI', json_encode([
                        'unique_args' => [
                            'email_log_id' => (string) $emailLogId,
                        ],
                        'filters' => [
                            'clicktrack' => ['settings' => ['enable' => 0]],
                            'opentrack'  => ['settings' => ['enable' => 0]],
                        ],
                    ], JSON_UNESCAPED_UNICODE));
                } else {
                    $message->getSymfonyMessage()->getHeaders()->addTextHeader('X-SMTPAPI', json_encode([
                        'filters' => [
                            'clicktrack' => ['settings' => ['enable' => 0]],
                            'opentrack'  => ['settings' => ['enable' => 0]],
                        ],
                    ], JSON_UNESCAPED_UNICODE));
                }

                if (!empty($cc)) {
                    $message->cc($cc);
                }

                if (!empty($attachments)) {
                    foreach ($attachments as $attachment) {
                        if (is_array($attachment) && !empty($attachment['content'])) {
                            $name = $attachment['name'] ?? 'attachment';
                            $message->attachData(
                                $attachment['content'],
                                $name,
                                ['mime' => $attachment['mime'] ?? $this->guessAttachmentMimeType($name)]
                            );
                        } elseif (is_string($attachment) && file_exists($attachment)) {
                            $message->attach($attachment);
                        }
                    }
                }
            });

            return true;
        } catch (\Exception $e) {
            throw new \Exception('Email could not be sent: ' . $e->getMessage());
        }
    }

    protected function guessAttachmentMimeType(string $filename): string
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $map = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return $map[$ext] ?? 'application/octet-stream';
    }
}

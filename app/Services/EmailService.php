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
    public function sendEmail($view, $data, $to, $subject, $fromEmailId, $attachments = [], $cc = [])
    {
        try {
            //dd($view, $data, $to, $subject, $fromEmailId);
            $emailConfig = Email::where('email', $fromEmailId)->firstOrFail();//dd($emailConfig);

            // Configure mail settings for this specific email
            config([
                'mail.mailers.smtp.host' => 'smtp.zoho.com',
                'mail.mailers.smtp.port' => 587,
                'mail.mailers.smtp.encryption' => 'tls',
                'mail.mailers.smtp.username' => $emailConfig->email,
                'mail.mailers.smtp.password' => $emailConfig->password,
                'mail.from.address' => $emailConfig->email,
                'mail.from.name' => $emailConfig->display_name,
            ]);

            // Send the email
            Mail::send($view, $data, function (Message $message) use ($to, $subject, $emailConfig, $attachments, $cc) {
                $message->to($to)
                    ->subject($subject)
                    ->from($emailConfig->email, $emailConfig->display_name);

                if (!empty($cc)) {
                    $message->cc($cc);
                }

                if (!empty($attachments)) {
                    foreach ($attachments as $attachment) {
                        if (file_exists($attachment)) {
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
}

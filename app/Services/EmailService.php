<?php

namespace App\Services;

use App\Models\Email;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Mailer names that may fail over from SES to a mailbox Zoho SMTP.
     *
     * @var list<string>
     */
    private const FAILOVER_DEFAULTS = ['failover', 'sendgrid', 'ses', 'zoho'];

    /**
     * Get all active email configurations.
     *
     * @return Collection
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
     * @param  string  $view
     * @param  array  $data
     * @param  string  $to
     * @param  string  $subject
     * @param  int|string  $fromEmailId
     * @return bool
     *
     * @throws \Exception
     */
    public function sendEmail($view, $data, $to, $subject, $fromEmailId, $attachments = [], $cc = [], ?int $emailLogId = null)
    {
        try {
            $emailConfig = $this->findMailbox((string) $fromEmailId);
            $fromAddress = $this->resolveFromAddress($emailConfig, (string) $fromEmailId);
            $fromName = $emailConfig?->display_name ?: config('mail.from.name');

            Mail::mailer($this->composeMailerName($emailConfig))->send($view, $data, function (Message $message) use ($to, $subject, $fromAddress, $fromName, $attachments, $cc, $emailLogId) {
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
                            'opentrack' => ['settings' => ['enable' => 0]],
                        ],
                    ], JSON_UNESCAPED_UNICODE));
                } else {
                    $message->getSymfonyMessage()->getHeaders()->addTextHeader('X-SMTPAPI', json_encode([
                        'filters' => [
                            'clicktrack' => ['settings' => ['enable' => 0]],
                            'opentrack' => ['settings' => ['enable' => 0]],
                        ],
                    ], JSON_UNESCAPED_UNICODE));
                }

                if (! empty($cc)) {
                    $message->cc($cc);
                }

                if (! empty($attachments)) {
                    foreach ($attachments as $attachment) {
                        if (is_array($attachment) && ! empty($attachment['content'])) {
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
            throw new \Exception('Email could not be sent: '.$e->getMessage());
        }
    }

    /**
     * SES first, then this mailbox's Zoho SMTP when a password is stored.
     * Array/log/smtp test mailers are left unchanged.
     */
    public function composeMailerName(?Email $account): string
    {
        $default = (string) config('mail.default', 'failover');

        if ($account === null || ! filled($account->password)) {
            return $default;
        }

        if (! in_array($default, self::FAILOVER_DEFAULTS, true)) {
            return $default;
        }

        return $this->registerMailboxFailoverMailer($account);
    }

    protected function findMailbox(string $from): ?Email
    {
        $from = strtolower(trim($from));
        if ($from === '') {
            return null;
        }

        return Email::query()->whereRaw('LOWER(email) = ?', [$from])->first();
    }

    protected function resolveFromAddress(?Email $account, string $from): string
    {
        if ($account?->email) {
            return $account->email;
        }

        $from = trim($from);
        if (filter_var($from, FILTER_VALIDATE_EMAIL)) {
            return $from;
        }

        return (string) config('mail.from.address');
    }

    protected function registerMailboxFailoverMailer(Email $account): string
    {
        $suffix = substr(hash('sha256', strtolower((string) $account->email).'|'.(string) $account->password), 0, 12);
        $zohoMailer = 'zoho_mailbox_'.$suffix;
        $failoverMailer = 'failover_mailbox_'.$suffix;

        $zoho = config('mail.mailers.zoho', []);

        config([
            "mail.mailers.{$zohoMailer}" => [
                'transport' => 'smtp',
                'host' => filled($account->smtp_host) ? $account->smtp_host : ($zoho['host'] ?? 'smtp.zoho.com'),
                'port' => (int) ($account->smtp_port ?: ($zoho['port'] ?? 587)),
                'username' => $account->email,
                'password' => $account->password,
                'encryption' => filled($account->smtp_encryption) ? $account->smtp_encryption : ($zoho['encryption'] ?? 'tls'),
                'timeout' => $zoho['timeout'] ?? null,
                'local_domain' => $zoho['local_domain'] ?? null,
            ],
            "mail.mailers.{$failoverMailer}" => [
                'transport' => 'failover',
                'mailers' => ['ses', $zohoMailer],
                'retry_after' => 60,
            ],
        ]);

        return $failoverMailer;
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

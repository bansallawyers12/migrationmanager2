<?php

namespace App\Services;

use App\Models\Email;
use App\Support\SendGridFromAllowedDomains;
use Illuminate\Support\Facades\Log;

class SesSenderService
{
    /**
     * Active From addresses for compose dropdowns.
     *
     * Admin Console emails table first, then SES_SENDERS / MAIL_FROM_* env fallbacks.
     * Does not call the SendGrid API.
     *
     * @return list<array{email: string, name: string, nickname: string}>
     */
    public function getComposeSenders(): array
    {
        $list = [];

        try {
            $rows = Email::query()
                ->where('status', true)
                ->orderBy('id')
                ->get(['email', 'display_name']);
        } catch (\Throwable $e) {
            Log::warning('SES compose senders: emails table read failed', [
                'error' => $e->getMessage(),
            ]);
            $rows = collect();
        }

        foreach ($rows as $row) {
            $email = strtolower(trim((string) $row->email));
            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            if (! SendGridFromAllowedDomains::allowsEmail($email)) {
                continue;
            }
            $displayName = trim((string) ($row->display_name ?? ''));
            $list[$email] = [
                'email' => $email,
                'name' => $displayName !== '' ? $displayName : $email,
                'nickname' => '',
            ];
        }

        foreach ($this->envSenders() as $sender) {
            $email = strtolower($sender['email']);
            if (! isset($list[$email])) {
                $list[$email] = $sender;
            }
        }

        return array_values($list);
    }

    /**
     * Preferred From for the compose dropdown (info@ when present).
     *
     * @param  list<array{email: string, name: string, nickname: string}>  $senders
     */
    public function defaultFrom(array $senders): string
    {
        if ($senders === []) {
            return '';
        }

        $preferred = strtolower(trim((string) config('services.ses_crm.from_email', config('mail.from.address', ''))));
        if ($preferred !== '') {
            foreach ($senders as $row) {
                $addr = isset($row['email']) && is_string($row['email']) ? $row['email'] : '';
                if ($addr !== '' && strtolower($addr) === $preferred) {
                    return $addr;
                }
            }
        }

        return $senders[0]['email'] ?? '';
    }

    /**
     * @return list<array{email: string, name: string, nickname: string}>
     */
    public function envSenders(): array
    {
        $raw = (string) config('services.ses_crm.senders', '');
        $emails = array_filter(array_map('trim', explode(',', $raw)));

        foreach ([
            config('mail.from.address'),
            config('mail.noreply.address'),
            config('mail.info.address'),
        ] as $extra) {
            $extra = trim((string) $extra);
            if ($extra !== '') {
                $emails[] = $extra;
            }
        }

        $list = [];
        foreach ($emails as $email) {
            $email = strtolower($email);
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            if (! SendGridFromAllowedDomains::allowsEmail($email)) {
                continue;
            }
            $list[$email] = [
                'email' => $email,
                'name' => $email,
                'nickname' => '',
            ];
        }

        return array_values($list);
    }
}

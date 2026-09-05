<?php

namespace App\Services;

use App\Models\ClientMatter;
use App\Models\Email;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Service for resolving email sender configuration.
 */
class EmailConfigService
{
    /**
     * Get email configuration for a specific account by email ID
     *
     * @param  int  $emailId  The email record ID
     * @return array Sender configuration array
     *
     * @throws \Exception If email config not found
     */
    public function forAccountById(int $emailId): array
    {
        try {
            $emailConfig = Email::findOrFail($emailId);

            return $this->buildConfig($emailConfig);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve email config by ID', [
                'email_id' => $emailId,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception("Email configuration not found for ID: {$emailId}");
        }
    }

    /**
     * Get email configuration for a specific account by email address
     *
     * @param  string  $email  The email address
     * @return array Sender configuration array
     *
     * @throws \Exception If email config not found
     */
    public function forAccount(string $email): array
    {
        try {
            $emailConfig = Email::where('email', $email)
                ->where('status', true)
                ->firstOrFail();

            return $this->buildConfig($emailConfig);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve email config by email address', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception("Email configuration not found for: {$email}");
        }
    }

    /**
     * Build sender configuration array from Email model
     */
    protected function buildConfig(Email $emailConfig): array
    {
        return [
            'from_address' => $emailConfig->email,
            'from_name' => $emailConfig->display_name ?? 'Bansal Migration',
            'email_signature' => $emailConfig->email_signature ?? '',
        ];
    }

    /**
     * Sender identity only — does not change the failover (SES → Zoho) mailer.
     *
     * @param  array{from_address: string, from_name: string, email_signature?: string}  $config
     */
    public function applyConfig(array $config): void
    {
        config([
            'mail.from.address' => $config['from_address'],
            'mail.from.name' => $config['from_name'],
        ]);

        Log::debug('Applied sender configuration', [
            'from' => $config['from_address'],
        ]);
    }

    /**
     * Get all active email accounts for dropdown selection
     *
     * @return Collection
     */
    public function getActiveAccounts()
    {
        return Email::where('status', true)
            ->select('id', 'email', 'display_name')
            ->orderBy('email')
            ->get();
    }

    /**
     * Get default email account (first active account or system default)
     */
    public function getDefaultAccount(): ?array
    {
        try {
            $emailConfig = Email::where('status', true)
                ->orderBy('id')
                ->first();

            if ($emailConfig) {
                return $this->buildConfig($emailConfig);
            }
        } catch (\Exception $e) {
            Log::error('Failed to get default email account', [
                'error' => $e->getMessage(),
            ]);
        }

        $fromAddress = config('mail.from.address');
        if ($fromAddress) {
            return [
                'from_address' => $fromAddress,
                'from_name' => config('mail.from.name', 'Bansal Migration'),
            ];
        }

        return null;
    }

    /**
     * Get email configuration from .env file only
     * Use this when you want to force .env credentials regardless of database accounts
     */
    public function getEnvAccount(): ?array
    {
        try {
            $fromAddress = config('mail.from.address');
            if ($fromAddress) {
                return [
                    'from_address' => $fromAddress,
                    'from_name' => config('mail.from.name', 'Bansal Migration'),
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get .env email configuration', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get email configuration for EOI verification/confirmation emails.
     * Looks up admin@bansalimmigration from the emails table (or EOI_FROM_EMAIL from .env).
     * Returns sender details for setting the from-address before sending.
     *
     * @return array|null Config array (from_address, from_name, email_signature) or null if not found
     */
    public function getEoiFromAccount(): ?array
    {
        $emailConfig = $this->findDefaultEoiMailbox();

        if ($emailConfig) {
            return $this->buildConfig($emailConfig);
        }

        return null;
    }

    /**
     * Resolve EOI From + mailer. Adelaide uses adelaide@; Melbourne/other offices
     * use admin@. Both try SES first, then that mailbox's Zoho SMTP from emails.
     *
     * @return array{from: array{from_address: string, from_name: string, email_signature?: string}|null, mailer: string|null}
     */
    public function getEoiSendContext(?int $clientId = null): array
    {
        if ($clientId !== null) {
            try {
                if ($this->latestActiveEoiMatterIsAdelaide($clientId)) {
                    return $this->adelaideEoiSendContext();
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to resolve Adelaide EOI send context; using default EOI sender', [
                    'client_id' => $clientId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $this->defaultEoiSendContext();
    }

    /**
     * @return array{from: array{from_address: string, from_name: string, email_signature?: string}, mailer: string|null}
     */
    protected function adelaideEoiSendContext(): array
    {
        $adelaideEmail = (string) config('services.eoi.adelaide_from_email', 'Adelaide@bansalimmigration.com.au');
        $account = Email::query()
            ->where('status', true)
            ->whereRaw('LOWER(email) = ?', [Str::lower($adelaideEmail)])
            ->first();

        if ($account) {
            return [
                'from' => $this->buildConfig($account),
                'mailer' => app(EmailService::class)->composeMailerName($account),
            ];
        }

        return [
            'from' => [
                'from_address' => $adelaideEmail,
                'from_name' => config('mail.from.name', 'Bansal Migration'),
                'email_signature' => '',
            ],
            'mailer' => null,
        ];
    }

    /**
     * @return array{from: array{from_address: string, from_name: string, email_signature?: string}|null, mailer: string|null}
     */
    protected function defaultEoiSendContext(): array
    {
        $account = $this->findDefaultEoiMailbox();

        if ($account) {
            return [
                'from' => $this->buildConfig($account),
                'mailer' => app(EmailService::class)->composeMailerName($account),
            ];
        }

        return [
            'from' => $this->getEoiFromAccount(),
            'mailer' => null,
        ];
    }

    protected function findDefaultEoiMailbox(): ?Email
    {
        $preferredEmail = config('services.eoi.from_email', 'admin@bansalimmigration.com.au');

        $emailConfig = Email::where('status', true)
            ->where('email', $preferredEmail)
            ->first();

        if (! $emailConfig) {
            $emailConfig = Email::where('status', true)
                ->where('email', 'like', 'admin@bansalimmigration%')
                ->first();
        }

        return $emailConfig;
    }

    protected function latestActiveEoiMatterIsAdelaide(int $clientId): bool
    {
        $matter = ClientMatter::query()
            ->with('office:id,office_name')
            ->where('client_id', $clientId)
            ->where('matter_status', 1)
            ->whereHas('matter', static function ($query): void {
                $query->where(function ($q): void {
                    $q->whereRaw("LOWER(COALESCE(nick_name, '')) = 'eoi'")
                        ->orWhereRaw("LOWER(COALESCE(title, '')) LIKE '%eoi%'")
                        ->orWhereRaw("LOWER(COALESCE(title, '')) LIKE '%expression of interest%'");
                });
            })
            ->orderByDesc('id')
            ->first();

        $officeName = $matter?->office?->office_name;

        return is_string($officeName) && Str::contains(Str::lower($officeName), 'adelaide');
    }

    /**
     * Validate email configuration by attempting connection
     */
    public function validateConfig(array $config): bool
    {
        try {
            return ! empty($config['from_address']);
        } catch (\Exception $e) {
            Log::warning('Email config validation failed', [
                'config' => $config['from_address'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}

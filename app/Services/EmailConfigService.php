<?php

namespace App\Services;

use App\Models\Email;
use Illuminate\Support\Facades\Log;

/**
 * Service for managing email SMTP configurations
 * Provides reusable SMTP config retrieval and application
 */
class EmailConfigService
{
    /**
     * Get email configuration for a specific account by email ID
     *
     * @param int $emailId The email record ID
     * @return array SMTP configuration array
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
                'error' => $e->getMessage()
            ]);
            throw new \Exception("Email configuration not found for ID: {$emailId}");
        }
    }

    /**
     * Get email configuration for a specific account by email address
     *
     * @param string $email The email address
     * @return array SMTP configuration array
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
                'error' => $e->getMessage()
            ]);
            throw new \Exception("Email configuration not found for: {$email}");
        }
    }

    /**
     * Build SMTP configuration array from Email model
     *
     * @param Email $emailConfig
     * @return array
     */
    protected function buildConfig(Email $emailConfig): array
    {
        return [
            'host' => $emailConfig->smtp_host ?? 'smtp.zoho.com',
            'port' => $emailConfig->smtp_port ?? 587,
            'encryption' => $emailConfig->smtp_encryption ?? 'tls',
            'username' => $emailConfig->email,
            'password' => $emailConfig->password,
            'from_address' => $emailConfig->email,
            'from_name' => $emailConfig->display_name ?? 'Bansal Migration',
            'timeout' => 30,
        ];
    }

    /**
     * Apply email configuration to Laravel mail config at runtime
     *
     * @param array $config Configuration array from forAccount()
     * @return void
     */
    public function applyConfig(array $config): void
    {
        config([
            'mail.default' => 'smtp',  // Switch to SMTP mailer
            'mail.mailers.smtp.host' => $config['host'],
            'mail.mailers.smtp.port' => $config['port'],
            'mail.mailers.smtp.encryption' => $config['encryption'],
            'mail.mailers.smtp.username' => $config['username'],
            'mail.mailers.smtp.password' => $config['password'],
            'mail.from.address' => $config['from_address'],
            'mail.from.name' => $config['from_name'],
        ]);

        Log::debug('Applied email configuration', [
            'from' => $config['from_address'],
            'host' => $config['host']
        ]);
    }

    /**
     * Get all active email accounts for dropdown selection
     *
     * @return \Illuminate\Database\Eloquent\Collection
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
     *
     * @return array|null
     */
    public function getDefaultAccount(): ?array
    {
        try {
            // Try to get the first active account
            $emailConfig = Email::where('status', true)
                ->orderBy('id')
                ->first();

            if ($emailConfig) {
                return $this->buildConfig($emailConfig);
            }

            // Fallback to environment defaults
            if (env('MAIL_FROM_ADDRESS')) {
                return [
                    'host' => env('MAIL_HOST', 'smtp.zoho.com'),
                    'port' => env('MAIL_PORT', 587),
                    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                    'username' => env('MAIL_USERNAME'),
                    'password' => env('MAIL_PASSWORD'),
                    'from_address' => env('MAIL_FROM_ADDRESS'),
                    'from_name' => env('MAIL_FROM_NAME', 'Bansal Migration'),
                    'timeout' => 30,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get default email account', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get email configuration from .env file only
     * Use this when you want to force .env credentials regardless of database accounts
     *
     * @return array|null
     */
    public function getEnvAccount(): ?array
    {
        try {
            if (env('MAIL_FROM_ADDRESS')) {
                return [
                    'host' => env('MAIL_HOST', 'smtp.zoho.com'),
                    'port' => env('MAIL_PORT', 587),
                    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                    'username' => env('MAIL_USERNAME'),
                    'password' => env('MAIL_PASSWORD'),
                    'from_address' => env('MAIL_FROM_ADDRESS'),
                    'from_name' => env('MAIL_FROM_NAME', 'Bansal Migration'),
                    'timeout' => 30,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get .env email configuration', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Validate email configuration by attempting connection
     *
     * @param array $config
     * @return bool
     */
    public function validateConfig(array $config): bool
    {
        try {
            // Temporarily apply config
            $originalConfig = config('mail.mailers.smtp');
            $this->applyConfig($config);

            // Try to create transport and verify
            $transport = app('mail.manager')->mailer()->getSymfonyTransport();
            
            // Restore original config
            config(['mail.mailers.smtp' => $originalConfig]);

            return true;
        } catch (\Exception $e) {
            Log::warning('Email config validation failed', [
                'config' => $config['from_address'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}


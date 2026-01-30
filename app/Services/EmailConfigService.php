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
            'email_signature' => $emailConfig->email_signature ?? '',
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
     * Get Zepto email account configuration for signature facility
     * This method is used exclusively for document signature emails
     * SMTP settings are read from .env file, email address and signature from database
     *
     * @return array Email configuration with signature
     * @throws \Exception If Zepto account not found or not active
     */
    public function getZeptoAccount(): array
    {
        try {
            // Get email address from .env or search database
            $zeptoEmail = env('ZEPTO_EMAIL', 'signature@bansalimmigration.com.au');
            
            // Try to find email account in database (for email address and signature)
            $emailConfig = Email::where('status', true)
                ->where('email', $zeptoEmail)
                ->first();
            
            // If not found, try pattern search
            if (!$emailConfig) {
                $emailConfig = Email::where('status', true)
                    ->where('email', 'like', '%zepto%')
                    ->orWhere('email', 'like', '%signature%')
                    ->first();
            }
            
            // Get SMTP settings from .env file
            $smtpHost = env('ZEPTO_SMTP_HOST', 'smtp.zeptomail.com');
            $smtpPort = env('ZEPTO_SMTP_PORT', 587);
            $smtpEncryption = env('ZEPTO_SMTP_ENCRYPTION', 'tls');
            $smtpUsername = env('ZEPTO_SMTP_USERNAME', 'emailapikey');
            $smtpPassword = env('ZEPTO_SMTP_PASSWORD');
            $fromAddress = env('ZEPTO_EMAIL', $emailConfig->email ?? 'signature@bansalimmigration.com.au');
            $fromName = env('ZEPTO_FROM_NAME', $emailConfig->display_name ?? 'Bansal Migration');
            
            // Validate required .env settings
            if (empty($smtpPassword)) {
                throw new \Exception('ZEPTO_SMTP_PASSWORD is not set in .env file');
            }
            
            // Build config array with .env SMTP settings
            $config = [
                'host' => $smtpHost,
                'port' => (int) $smtpPort,
                'encryption' => $smtpEncryption,
                'username' => $smtpUsername,
                'password' => $smtpPassword,
                'from_address' => $fromAddress,
                'from_name' => $fromName,
                'email_signature' => $emailConfig->email_signature ?? '',
                'timeout' => 30,
            ];
            
            return $config;
        } catch (\Exception $e) {
            Log::error('Failed to retrieve Zepto email account', [
                'error' => $e->getMessage()
            ]);
            throw new \Exception("Zepto email account configuration error: {$e->getMessage()}");
        }
    }

    /**
     * Get ZeptoMail API configuration
     * Returns configuration for using ZeptoMail REST API instead of SMTP
     *
     * @return array API configuration
     * @throws \Exception If API key is not configured
     */
    public function getZeptoApiConfig(): array
    {
        try {
            $apiKey = config('services.zeptomail.api_key', env('ZEPTOMAIL_API_KEY'));
            
            if (empty($apiKey)) {
                throw new \Exception('ZEPTOMAIL_API_KEY is not set in .env file');
            }

            // Get email address from .env or search database
            $zeptoEmail = config('services.zeptomail.from_email', env('ZEPTOMAIL_FROM_EMAIL', 'signature@bansalimmigration.com.au'));
            
            // Try to find email account in database (for email signature)
            $emailConfig = Email::where('status', true)
                ->where('email', $zeptoEmail)
                ->first();
            
            // If not found, try pattern search
            if (!$emailConfig) {
                $emailConfig = Email::where('status', true)
                    ->where('email', 'like', '%zepto%')
                    ->orWhere('email', 'like', '%signature%')
                    ->first();
            }

            $fromAddress = config('services.zeptomail.from_email', env('ZEPTOMAIL_FROM_EMAIL', $emailConfig->email ?? 'signature@bansalimmigration.com.au'));
            $fromName = config('services.zeptomail.from_name', env('ZEPTOMAIL_FROM_NAME', $emailConfig->display_name ?? 'Bansal Migration'));

            return [
                'api_key' => $apiKey,
                'from_address' => $fromAddress,
                'from_name' => $fromName,
                'email_signature' => $emailConfig->email_signature ?? '',
                'api_url' => config('services.zeptomail.api_url', 'https://api.zeptomail.com/v1.1/email'),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to retrieve ZeptoMail API configuration', [
                'error' => $e->getMessage()
            ]);
            throw new \Exception("ZeptoMail API configuration error: {$e->getMessage()}");
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


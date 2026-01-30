<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Exception;

/**
 * ZeptoMail API Service
 * 
 * This service handles sending emails via ZeptoMail REST API
 * Documentation: https://www.zeptomail.com/docs/api/
 */
class ZeptoMailService
{
    /**
     * ZeptoMail API endpoint
     */
    private const API_URL = 'https://api.zeptomail.com/v1.1/email';

    /**
     * API Key from configuration
     */
    private ?string $apiKey;

    /**
     * Default from email address
     */
    private string $defaultFromEmail;

    /**
     * Default from name
     */
    private string $defaultFromName;

    /**
     * Constructor - Initialize with configuration
     */
    public function __construct()
    {
        $this->apiKey = config('services.zeptomail.api_key') ?: env('ZEPTOMAIL_API_KEY');
        $this->defaultFromEmail = config('services.zeptomail.from_email') ?: env('ZEPTOMAIL_FROM_EMAIL', 'signature@bansalimmigration.com.au');
        $this->defaultFromName = config('services.zeptomail.from_name') ?: env('ZEPTOMAIL_FROM_NAME', 'Bansal Migration');
    }

    /**
     * Send email via ZeptoMail API
     *
     * @param array $to Array of recipients [['address' => 'email@example.com', 'name' => 'Name']]
     * @param string $subject Email subject
     * @param string $htmlBody HTML content of the email
     * @param string|null $textBody Plain text content (optional)
     * @param string|null $fromEmail From email address (optional, uses default if not provided)
     * @param string|null $fromName From name (optional, uses default if not provided)
     * @param array $cc Array of CC recipients (optional)
     * @param array $bcc Array of BCC recipients (optional)
     * @param array $attachments Array of attachments (optional) [['file_name' => 'name', 'file_content' => base64_encoded_content, 'mime_type' => 'type']]
     * @return array Response from API
     * @throws Exception
     */
    public function sendEmail(
        array $to,
        string $subject,
        string $htmlBody,
        ?string $textBody = null,
        ?string $fromEmail = null,
        ?string $fromName = null,
        array $cc = [],
        array $bcc = [],
        array $attachments = []
    ): array {
        try {
            // Validate API key
            if (empty($this->apiKey)) {
                throw new Exception('ZeptoMail API key is not configured. Please set ZEPTOMAIL_API_KEY in .env file.');
            }

            // Prepare from address
            $fromEmail = $fromEmail ?? $this->defaultFromEmail;
            $fromName = $fromName ?? $this->defaultFromName;

            // Build request payload
            $payload = [
                'from' => [
                    'address' => $fromEmail,
                    'name' => $fromName
                ],
                'to' => $this->formatRecipients($to),
                'subject' => $subject,
                'htmlbody' => $htmlBody,
            ];

            // Add text body if provided
            if (!empty($textBody)) {
                $payload['textbody'] = $textBody;
            }

            // Add CC if provided
            if (!empty($cc)) {
                $payload['cc'] = $this->formatRecipients($cc);
            }

            // Add BCC if provided
            if (!empty($bcc)) {
                $payload['bcc'] = $this->formatRecipients($bcc);
            }

            // Add attachments if provided
            if (!empty($attachments)) {
                $payload['attachments'] = $attachments;
            }

            // Make API request
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'authorization' => 'Zoho-enczapikey ' . $this->apiKey,
                'cache-control' => 'no-cache',
                'content-type' => 'application/json',
            ])
            ->timeout(30)
            ->post(self::API_URL, $payload);

            // Check for HTTP errors
            if ($response->failed()) {
                $errorMessage = $response->json()['error'] ?? $response->body() ?? 'Unknown error';
                Log::error('ZeptoMail API request failed', [
                    'status' => $response->status(),
                    'error' => $errorMessage,
                    'payload' => $this->sanitizePayloadForLogging($payload)
                ]);
                throw new Exception("ZeptoMail API error: {$errorMessage}");
            }

            $responseData = $response->json();

            Log::info('ZeptoMail email sent successfully', [
                'to' => array_column($to, 'address'),
                'subject' => $subject,
                'response' => $responseData
            ]);

            return $responseData;

        } catch (Exception $e) {
            Log::error('ZeptoMail service error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Send simple email (quick method)
     *
     * @param string|array $to Recipient email(s) - can be string or array
     * @param string $subject Email subject
     * @param string $htmlBody HTML content
     * @param string|null $fromEmail From email (optional)
     * @param string|null $fromName From name (optional)
     * @return array
     * @throws Exception
     */
    public function sendSimpleEmail(
        $to,
        string $subject,
        string $htmlBody,
        ?string $fromEmail = null,
        ?string $fromName = null
    ): array {
        // Convert string to array format
        if (is_string($to)) {
            $to = [['address' => $to]];
        }

        return $this->sendEmail($to, $subject, $htmlBody, null, $fromEmail, $fromName);
    }

    /**
     * Format recipients array for ZeptoMail API
     *
     * @param array $recipients
     * @return array
     */
    private function formatRecipients(array $recipients): array
    {
        $formatted = [];

        foreach ($recipients as $recipient) {
            if (is_string($recipient)) {
                // Simple string format: 'email@example.com'
                $formatted[] = [
                    'email_address' => [
                        'address' => $recipient
                    ]
                ];
            } elseif (isset($recipient['address'])) {
                // Array format: ['address' => 'email@example.com', 'name' => 'Name']
                $emailAddress = [
                    'address' => $recipient['address']
                ];

                if (isset($recipient['name'])) {
                    $emailAddress['name'] = $recipient['name'];
                }

                $formatted[] = [
                    'email_address' => $emailAddress
                ];
            }
        }

        return $formatted;
    }

    /**
     * Sanitize payload for logging (remove sensitive data)
     *
     * @param array $payload
     * @return array
     */
    private function sanitizePayloadForLogging(array $payload): array
    {
        $sanitized = $payload;
        
        // Remove or mask sensitive content
        if (isset($sanitized['htmlbody'])) {
            $sanitized['htmlbody'] = substr($sanitized['htmlbody'], 0, 100) . '... [truncated]';
        }

        return $sanitized;
    }

    /**
     * Send email from Blade template (replacement for Mail::send)
     *
     * @param string $view Blade view name
     * @param array $data Data to pass to the view
     * @param string|array $to Recipient email(s)
     * @param string $subject Email subject
     * @param string|null $fromEmail From email (optional)
     * @param string|null $fromName From name (optional)
     * @param array $attachments Array of file paths or attachment data
     * @param array $cc CC recipients (optional)
     * @param array $bcc BCC recipients (optional)
     * @return array Response from API
     * @throws Exception
     */
    public function sendFromTemplate(
        string $view,
        array $data,
        $to,
        string $subject,
        ?string $fromEmail = null,
        ?string $fromName = null,
        array $attachments = [],
        array $cc = [],
        array $bcc = []
    ): array {
        try {
            // Render the Blade template to HTML
            $htmlBody = View::make($view, $data)->render();

            // Convert $to to array format
            if (is_string($to)) {
                $toArray = [['address' => $to]];
            } elseif (is_array($to) && isset($to['address'])) {
                $toArray = [$to];
            } elseif (is_array($to) && isset($to[0])) {
                $toArray = $to;
            } else {
                throw new Exception('Invalid recipient format');
            }

            // Process attachments for ZeptoMail API format
            $processedAttachments = [];
            if (!empty($attachments)) {
                foreach ($attachments as $attachment) {
                    if (is_string($attachment)) {
                        // Simple file path
                        if (file_exists($attachment)) {
                            $processedAttachments[] = [
                                'file_name' => basename($attachment),
                                'file_content' => base64_encode(file_get_contents($attachment)),
                                'mime_type' => mime_content_type($attachment) ?: 'application/octet-stream'
                            ];
                        }
                    } elseif (is_array($attachment)) {
                        // Array format with path, name, mime
                        $filePath = $attachment['path'] ?? $attachment['file'] ?? null;
                        if ($filePath && file_exists($filePath)) {
                            $processedAttachments[] = [
                                'file_name' => $attachment['name'] ?? $attachment['as'] ?? basename($filePath),
                                'file_content' => base64_encode(file_get_contents($filePath)),
                                'mime_type' => $attachment['mime'] ?? $attachment['mime_type'] ?? mime_content_type($filePath) ?: 'application/octet-stream'
                            ];
                        }
                    }
                }
            }

            // Send via API
            return $this->sendEmail(
                $toArray,
                $subject,
                $htmlBody,
                null, // text body
                $fromEmail,
                $fromName,
                $cc,
                $bcc,
                $processedAttachments
            );

        } catch (Exception $e) {
            Log::error('ZeptoMail sendFromTemplate error', [
                'view' => $view,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Test API connection
     *
     * @return bool
     */
    public function testConnection(): bool
    {
        try {
            if (empty($this->apiKey)) {
                return false;
            }

            // Try to send a test email (you might want to use a different endpoint for testing)
            // For now, just check if API key is set
            return !empty($this->apiKey);
        } catch (Exception $e) {
            Log::error('ZeptoMail connection test failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}

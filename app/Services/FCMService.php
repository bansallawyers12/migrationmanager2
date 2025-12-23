<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;

class FCMService
{
    private $serviceAccountPath;
    private $serviceAccountData;
    private $projectId;
    private $accessToken;
    private $accessTokenExpiry;

    public function __construct()
    {
        $this->serviceAccountPath = config('services.fcm.service_account_path');
        $this->loadServiceAccount();
    }

    /**
     * Load service account JSON file
     */
    private function loadServiceAccount()
    {
        if (!$this->serviceAccountPath) {
            Log::error('FCM service account path not configured');
            return;
        }

        $fullPath = storage_path('app/' . ltrim($this->serviceAccountPath, '/'));
        
        if (!file_exists($fullPath)) {
            Log::error('FCM service account file not found', ['path' => $fullPath]);
            return;
        }

        $jsonContent = file_get_contents($fullPath);
        $this->serviceAccountData = json_decode($jsonContent, true);

        if (!$this->serviceAccountData) {
            Log::error('Invalid FCM service account JSON');
            return;
        }

        $this->projectId = $this->serviceAccountData['project_id'] ?? null;
    }

    /**
     * Get access token for FCM API
     */
    private function getAccessToken()
    {
        // Return cached token if still valid
        if ($this->accessToken && $this->accessTokenExpiry && time() < $this->accessTokenExpiry) {
            return $this->accessToken;
        }

        if (!$this->serviceAccountData) {
            Log::error('Service account data not loaded');
            return null;
        }

        try {
            // Create JWT for service account
            $now = time();
            $jwt = [
                'iss' => $this->serviceAccountData['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ];

            // Sign JWT with private key
            $privateKey = $this->serviceAccountData['private_key'];
            $token = JWT::encode($jwt, $privateKey, 'RS256');

            // Exchange JWT for access token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $token,
            ]);

            if (!$response->successful()) {
                Log::error('Failed to get FCM access token', [
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return null;
            }

            $tokenData = $response->json();
            $this->accessToken = $tokenData['access_token'] ?? null;
            $this->accessTokenExpiry = $now + ($tokenData['expires_in'] ?? 3600) - 60; // 60 seconds buffer

            return $this->accessToken;
        } catch (\Exception $e) {
            Log::error('Error getting FCM access token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Send notification to a specific user
     */
    public function sendToUser($userId, $title, $body, $data = [])
    {
        $deviceTokens = DeviceToken::active()
            ->forUser($userId)
            ->pluck('device_token')
            ->toArray();

        if (empty($deviceTokens)) {
            Log::warning('No active device tokens found for user', ['user_id' => $userId]);
            return false;
        }

        return $this->sendToMultipleDevices($deviceTokens, $title, $body, $data);
    }

    /**
     * Send notification to multiple devices
     * Note: FCM v1 API requires individual requests per token
     */
    public function sendToMultipleDevices($deviceTokens, $title, $body, $data = [])
    {
        if (empty($deviceTokens)) {
            return false;
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            Log::error('FCM access token not available', [
                'project_id' => $this->projectId,
                'service_account_loaded' => !empty($this->serviceAccountData)
            ]);
            return false;
        }
        
        if (!$this->projectId) {
            Log::error('FCM project ID not available', [
                'service_account_loaded' => !empty($this->serviceAccountData)
            ]);
            return false;
        }

        $successCount = 0;
        $failedTokens = [];

        // FCM v1 API requires individual requests per token
        foreach ($deviceTokens as $token) {
            $result = $this->sendToSingleDevice($token, $title, $body, $data, $accessToken);
            if ($result['success']) {
                $successCount++;
            } else {
                $failedTokens[] = [
                    'token' => $token,
                    'error' => $result['error'] ?? 'Unknown error'
                ];
            }
        }

        // Handle failed tokens
        if (!empty($failedTokens)) {
            $this->handleFailedTokensV1($failedTokens);
        }

        return $successCount > 0;
    }

    /**
     * Send notification to a single device using FCM v1 API
     */
    private function sendToSingleDevice($deviceToken, $title, $body, $data = [], $accessToken = null)
    {
        if (!$accessToken) {
            $accessToken = $this->getAccessToken();
        }

        if (!$accessToken || !$this->projectId) {
            return ['success' => false, 'error' => 'Access token not available'];
        }

        // Prepare data payload - convert all values to strings (FCM requirement)
        $dataPayload = [];
        foreach ($data as $key => $value) {
            $dataPayload[$key] = (string) $value;
        }

        // FCM v1 API payload structure
        $payload = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $dataPayload,
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'default',
                    ],
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                        ],
                    ],
                ],
            ],
        ];

        try {
            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                return ['success' => true];
            }

            $responseData = $response->json();
            $errorMessage = $responseData['error']['message'] ?? $response->body();
            $errorCode = $responseData['error']['code'] ?? null;
            
            Log::error('FCM v1 API request failed', [
                'error' => $errorMessage,
                'error_code' => $errorCode,
                'status_code' => $response->status(),
                'response_body' => $response->body(),
                'token' => substr($deviceToken, 0, 20) . '...'
            ]);
            
            return [
                'success' => false,
                'error' => $errorMessage,
                'code' => $errorCode
            ];
        } catch (\Exception $e) {
            Log::error('FCM v1 API request failed', [
                'error' => $e->getMessage(),
                'token' => substr($deviceToken, 0, 20) . '...'
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Handle failed device tokens for v1 API
     */
    private function handleFailedTokensV1($failedTokens)
    {
        foreach ($failedTokens as $failedToken) {
            $deviceToken = $failedToken['token'];
            $error = $failedToken['error'] ?? '';
            
            // Deactivate invalid tokens based on error codes
            // FCM v1 API error codes: NOT_FOUND, INVALID_ARGUMENT, UNREGISTERED, etc.
            if (
                str_contains($error, 'NOT_FOUND') ||
                str_contains($error, 'UNREGISTERED') ||
                str_contains($error, 'INVALID_ARGUMENT') ||
                str_contains($error, 'registration-token-not-registered')
            ) {
                DeviceToken::where('device_token', $deviceToken)
                    ->update(['is_active' => false]);
                
                Log::info('Deactivated invalid device token (v1 API)', [
                    'device_token' => substr($deviceToken, 0, 20) . '...',
                    'error' => $error
                ]);
            }
        }
    }

    /**
     * Send appointment reminder
     */
    public function sendAppointmentReminder($userId, $appointmentData)
    {
        $title = 'Appointment Reminder';
        $body = "You have an appointment scheduled for {$appointmentData['date']} at {$appointmentData['time']}";
        
        $data = [
            'type' => 'appointment_reminder',
            'appointment_id' => $appointmentData['id'],
            'action' => 'view_appointment'
        ];

        return $this->sendToUser($userId, $title, $body, $data);
    }

    /**
     * Send document approval notification
     */
    public function sendDocumentApproval($userId, $documentData)
    {
        $title = 'Document Approved';
        $body = "Your document '{$documentData['title']}' has been approved";
        
        $data = [
            'type' => 'document_approval',
            'document_id' => $documentData['id'],
            'action' => 'view_document'
        ];

        return $this->sendToUser($userId, $title, $body, $data);
    }

    /**
     * Send case status update
     */
    public function sendCaseStatusUpdate($userId, $caseData)
    {
        $title = 'Case Status Update';
        $body = "Your case '{$caseData['title']}' status has been updated to {$caseData['status']}";
        
        $data = [
            'type' => 'case_status_update',
            'case_id' => $caseData['id'],
            'action' => 'view_case'
        ];

        return $this->sendToUser($userId, $title, $body, $data);
    }
}

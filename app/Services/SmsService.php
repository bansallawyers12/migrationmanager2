<?php

namespace App\Services;

use Twilio\Rest\Client as TwilioClient;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $twilioClient;
    protected $fromNumber;

    public function __construct()
    {
        $accountSid = config('services.twilio.account_sid');
        $authToken = config('services.twilio.auth_token');
        $this->fromNumber = config('services.twilio.from');
        
        $this->twilioClient = new TwilioClient($accountSid, $authToken);
    }

    public function sendSms($to, $message)
    {
        try {
            // Convert single number to array if needed
            $numbers = is_array($to) ? $to : [$to];
            $results = [];

            Log::info('Sending SMS via Twilio', [
                'to' => $numbers,
                'from' => $this->fromNumber,
                'message' => $message
            ]);

            foreach ($numbers as $number) {
                $messageResult = $this->twilioClient->messages->create(
                    $number,
                    [
                        'from' => $this->fromNumber,
                        'body' => $message
                    ]
                );

                $results[] = [
                    'to' => $number,
                    'sid' => $messageResult->sid,
                    'status' => $messageResult->status
                ];
            }

            Log::info('Twilio SMS Response', ['results' => $results]);

            return ['success' => true, 'message' => 'SMS sent successfully!', 'results' => $results];
        } catch (\Twilio\Exceptions\TwilioException $e) {
            Log::error('Twilio SMS Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Failed to send SMS: ' . $e->getMessage()];
        } catch (\Exception $e) {
            Log::error('SMS Service Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get SMS status from Twilio
     */
    public function getSmsStatus($messageSid)
    {
        try {
            $message = $this->twilioClient->messages($messageSid)->fetch();
            
            return [
                'sid' => $message->sid,
                'status' => $message->status,
                'direction' => $message->direction,
                'from' => $message->from,
                'to' => $message->to,
                'body' => $message->body,
                'dateCreated' => $message->dateCreated,
                'dateUpdated' => $message->dateUpdated,
                'errorCode' => $message->errorCode,
                'errorMessage' => $message->errorMessage
            ];
        } catch (\Twilio\Exceptions\TwilioException $e) {
            Log::error('Twilio Status Check Error', ['error' => $e->getMessage()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('SMS Status Check Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get incoming SMS responses from Twilio
     */
    public function getResponses($pageSize = 50)
    {
        try {
            $messages = $this->twilioClient->messages->read([
                'direction' => 'inbound',
                'limit' => $pageSize
            ]);

            $results = [];
            foreach ($messages as $message) {
                $results[] = [
                    'sid' => $message->sid,
                    'from' => $message->from,
                    'to' => $message->to,
                    'body' => $message->body,
                    'status' => $message->status,
                    'dateCreated' => $message->dateCreated,
                    'dateUpdated' => $message->dateUpdated
                ];
            }

            return $results;
        } catch (\Twilio\Exceptions\TwilioException $e) {
            Log::error('Twilio Responses Error', ['error' => $e->getMessage()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('SMS Responses Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Send verification code SMS
     */
    public function sendVerificationCodeSMS($to, $message)
    {
        return $this->sendSms($to, $message);
    }

    /**
     * Send verification code with default message
     */
    public function sendVerificationCode($to, $code)
    {
        $message = "Your verification code is: $code";
        return $this->sendVerificationCodeSMS($to, $message);
    }
}
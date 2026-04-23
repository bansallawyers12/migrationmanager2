<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends BaseController
{
    /**
     * Proxy a user message to Anthropic Messages API (Claude).
     */
    public function chat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => ['required', 'string', 'max:2000'],
            'model' => ['sometimes', 'string', 'max:128'],
            'max_tokens' => ['sometimes', 'integer', 'min:1', 'max:8192'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation failed.', $validator->errors()->toArray(), 422);
        }

        $apiKey = env('ANTHROPIC_API_KEY');
        if (empty($apiKey)) {
            return $this->sendError('Chat service is not configured.', [], 503);
        }

        $data = $validator->validated();
        $model = $data['model'] ?? 'claude-sonnet-4-6';
        $maxTokens = $data['max_tokens'] ?? 1024;

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])
                ->post('https://api.anthropic.com/v1/messages', [
                    'model' => $model,
                    'max_tokens' => $maxTokens,
                    'messages' => [
                        ['role' => 'user', 'content' => $data['message']],
                    ],
                ]);
        } catch (\Throwable $e) {
            Log::error('Anthropic chat request failed', ['message' => $e->getMessage()]);

            return $this->sendError('Unable to reach chat service.', [], 502);
        }

        if ($response->failed()) {
            Log::warning('Anthropic API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->sendError('Claude API failed.', [
                'details' => $response->json() ?? $response->body(),
            ], 500);
        }

        return $this->sendResponse($response->json(), 'OK');
    }
}

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
     * Sends Bansal training prompt as `system` and optional prior turns as `conversation`.
     */
    public function chat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => ['required', 'string', 'max:2000'],
            'conversation' => ['sometimes', 'array', 'max:50'],
            'conversation.*.role' => ['required_with:conversation', 'in:user,assistant'],
            'conversation.*.content' => ['required_with:conversation', 'string', 'max:8000'],
            'model' => ['sometimes', 'string', 'max:128'],
            'max_tokens' => ['sometimes', 'integer', 'min:1', 'max:8192'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation failed.', $validator->errors()->toArray(), 422);
        }

        $data = $validator->validated();
        $priorTurns = $data['conversation'] ?? [];

        $conversationError = $this->validateConversationTurns($priorTurns);
        if ($conversationError !== null) {
            return $this->sendError($conversationError, [], 422);
        }

        $maxPrior = (int) config('chatbot.max_conversation_messages', 24);
        $priorTurns = $this->truncateConversation($priorTurns, $maxPrior);

        $messages = $priorTurns;
        $messages[] = ['role' => 'user', 'content' => $data['message']];

        $systemPrompt = $this->loadSystemPrompt();

        $apiKey = env('ANTHROPIC_API_KEY');
        if (empty($apiKey)) {
            return $this->sendError('Chat service is not configured.', [], 503);
        }

        $model = $data['model'] ?? 'claude-sonnet-4-6';
        $maxTokens = $data['max_tokens'] ?? 1024;

        $payload = [
            'model' => $model,
            'max_tokens' => $maxTokens,
            'messages' => $messages,
        ];

        if ($systemPrompt !== '') {
            $payload['system'] = $systemPrompt;
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])
                ->post('https://api.anthropic.com/v1/messages', $payload);
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

    private function loadSystemPrompt(): string
    {
        $path = config('chatbot.system_prompt_path');
        if (! is_string($path) || $path === '' || ! is_readable($path)) {
            Log::warning('Chatbot system prompt file missing or unreadable.', ['path' => $path]);

            return '';
        }

        $text = file_get_contents($path);

        return is_string($text) ? trim($text) : '';
    }

    /**
     * Keep only the last messages, preserving a valid prefix: even length, starts with user, ends with assistant.
     *
     * @param  array<int, array{role: string, content: string}>  $turns
     * @return array<int, array{role: string, content: string}>
     */
    private function truncateConversation(array $turns, int $maxMessages): array
    {
        if ($turns === []) {
            return [];
        }

        $cap = min(count($turns), max(0, $maxMessages));
        $cap -= $cap % 2;

        if ($cap < 2) {
            return [];
        }

        return array_values(array_slice($turns, -$cap));
    }

    /**
     * Prior turns only; must start with `user` and end with `assistant` when non-empty.
     *
     * @param  array<int, array{role?: string, content?: string}>  $conversation
     */
    private function validateConversationTurns(array $conversation): ?string
    {
        if ($conversation === []) {
            return null;
        }

        $previousRole = null;

        foreach ($conversation as $index => $turn) {
            $role = $turn['role'] ?? '';
            $content = isset($turn['content']) ? trim((string) $turn['content']) : '';

            if ($content === '') {
                return 'conversation turns must have non-empty content.';
            }

            if ($index === 0 && $role !== 'user') {
                return 'conversation must start with a user message.';
            }

            if ($previousRole !== null && $role === $previousRole) {
                return 'conversation roles must alternate between user and assistant.';
            }

            $previousRole = $role;
        }

        if (($conversation[array_key_last($conversation)]['role'] ?? '') !== 'assistant') {
            return 'conversation must end with an assistant message (send prior turns only; put the new user text in message).';
        }

        return null;
    }
}

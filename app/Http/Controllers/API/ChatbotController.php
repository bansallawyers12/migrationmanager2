<?php

namespace App\Http\Controllers\API;

use App\Services\Chatbot\ChatbotKnowledgeMatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends BaseController
{
    /**
     * Proxy a user message to Google Gemini, unless the scripted FAQ library
     * resolves with high confidence — then the exact approved training text returns.
     *
     * Response shape stays Claude-compatible (`content[].text`, `reply`) so the
     * Flutter client portal keeps working without changes.
     */
    public function chat(Request $request, ChatbotKnowledgeMatcher $faqMatcher)
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

        $scriptedMatch = $faqMatcher->resolve((string) $data['message']);
        if ($scriptedMatch !== null) {
            $payload = $this->buildCompatibleScriptedBody(
                $scriptedMatch['answer'],
                [
                    'source' => 'training_script_exact',
                    'matched_faq_id' => $scriptedMatch['faq_id'],
                    'confidence' => $scriptedMatch['confidence'],
                    'category' => $scriptedMatch['category'],
                ]
            );

            return $this->sendResponse($payload, 'OK');
        }

        $apiKey = config('chatbot.api_key');
        if ($apiKey === null || trim((string) $apiKey) === '') {
            return $this->sendError('Chat service is not configured.', [], 503);
        }

        $model = filter_var(config('chatbot.allow_client_model_override', false), FILTER_VALIDATE_BOOLEAN) && isset($data['model'])
            ? $data['model']
            : (string) config('chatbot.default_model', 'gemini-3.6-flash');

        $requestedTokens = isset($data['max_tokens'])
            ? (int) $data['max_tokens']
            : (int) config('chatbot.max_tokens_default', 1024);
        $ceiling = (int) config('chatbot.max_tokens_ceiling', 2048);
        $maxTokens = max(1, min($requestedTokens, $ceiling));

        $messages = $priorTurns;
        $messages[] = ['role' => 'user', 'content' => $data['message']];

        $payload = $this->buildGeminiPayload($messages, $this->loadSystemPrompt(), $maxTokens);
        $endpoint = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent',
            rawurlencode($model)
        );

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-goog-api-key' => trim((string) $apiKey),
                    'content-type' => 'application/json',
                ])
                ->post($endpoint, $payload);
        } catch (\Throwable $e) {
            Log::error('Gemini chat request failed', ['message' => $e->getMessage()]);

            return $this->sendError('Unable to reach chat service.', [], 502);
        }

        if ($response->failed()) {
            Log::warning('Gemini API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'model' => $model,
            ]);

            return $this->sendError('Gemini API failed.', [
                'details' => $response->json() ?? $response->body(),
            ], 500);
        }

        $body = $response->json();

        return $this->sendResponse(
            $this->envelopeGeminiPayload(is_array($body) ? $body : [], $model),
            'OK'
        );
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
     * @param  array<int, array{role: string, content: string}>  $messages
     * @return array<string, mixed>
     */
    private function buildGeminiPayload(array $messages, string $systemPrompt, int $maxTokens): array
    {
        $contents = [];
        foreach ($messages as $turn) {
            $role = ($turn['role'] ?? '') === 'assistant' ? 'model' : 'user';
            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => (string) ($turn['content'] ?? '')],
                ],
            ];
        }

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'maxOutputTokens' => $maxTokens,
                'temperature' => 0.4,
            ],
        ];

        if ($systemPrompt !== '') {
            $payload['system_instruction'] = [
                'parts' => [
                    ['text' => $systemPrompt],
                ],
            ];
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $meta  chatbot_meta (source-specific fields)
     * @return array<string, mixed>
     */
    private function buildCompatibleScriptedBody(string $assistantText, array $meta): array
    {
        return [
            'id' => 'chatbotfaq_'.($meta['matched_faq_id'] ?? uniqid('', true)),
            'type' => 'message',
            'role' => 'assistant',
            'model' => 'bansal-training-script-exact',
            'content' => [
                ['type' => 'text', 'text' => $assistantText],
            ],
            'stop_reason' => 'end_turn',
            'usage' => [
                'input_tokens' => 0,
                'output_tokens' => mb_strlen($assistantText, 'UTF-8'),
            ],
            'reply' => $assistantText,
            'chatbot_meta' => array_merge(
                ['note' => 'Exact text from `chatbot_faqs` (training library). Import: `php artisan chatbot:seed-faq-library`.'],
                $meta
            ),
        ];
    }

    /**
     * Normalize Gemini generateContent JSON into the existing app response shape.
     *
     * @param  array<string, mixed>  $geminiBody
     * @return array<string, mixed>
     */
    private function envelopeGeminiPayload(array $geminiBody, string $resolvedModel): array
    {
        $assistantText = $this->assistantTextFromGeminiBody($geminiBody);
        $usage = is_array($geminiBody['usageMetadata'] ?? null) ? $geminiBody['usageMetadata'] : [];

        return [
            'id' => 'gemini_'.uniqid('', true),
            'type' => 'message',
            'role' => 'assistant',
            'model' => $resolvedModel,
            'content' => [
                ['type' => 'text', 'text' => $assistantText],
            ],
            'stop_reason' => 'end_turn',
            'usage' => [
                'input_tokens' => (int) ($usage['promptTokenCount'] ?? 0),
                'output_tokens' => (int) ($usage['candidatesTokenCount'] ?? 0),
            ],
            'reply' => $assistantText,
            'chatbot_meta' => [
                'source' => 'gemini',
                'model' => $resolvedModel,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $body
     */
    private function assistantTextFromGeminiBody(array $body): string
    {
        $candidates = $body['candidates'] ?? [];
        if (! is_array($candidates) || $candidates === []) {
            return '';
        }

        $parts = $candidates[0]['content']['parts'] ?? [];
        if (! is_array($parts)) {
            return '';
        }

        $texts = [];
        foreach ($parts as $part) {
            if (! is_array($part)) {
                continue;
            }
            if (! isset($part['text'])) {
                continue;
            }
            $texts[] = trim((string) $part['text']);
        }

        return trim(implode("\n", array_filter($texts)));
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

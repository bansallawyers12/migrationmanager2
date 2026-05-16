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
     * Proxy a user message to Anthropic Messages API (Claude), unless the scripted
     * FAQ library resolves with high confidence — then the exact approved training text returns.
     * Sends Bansal training prompt as `system` and optional prior turns as `conversation`.
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
            $payload = $this->buildAnthropicCompatibleScriptedBody(
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

        $messages = $priorTurns;
        $messages[] = ['role' => 'user', 'content' => $data['message']];

        $systemPrompt = $this->loadSystemPrompt();

        $apiKey = env('ANTHROPIC_API_KEY');
        if ($apiKey === null || trim((string) $apiKey) === '') {
            return $this->sendError('Chat service is not configured.', [], 503);
        }

        $model = filter_var(config('chatbot.allow_client_model_override', false), FILTER_VALIDATE_BOOLEAN) && isset($data['model'])
            ? $data['model']
            : (string) config('chatbot.default_model', 'claude-sonnet-4-6');

        $requestedTokens = isset($data['max_tokens'])
            ? (int) $data['max_tokens']
            : (int) config('chatbot.max_tokens_default', 1024);
        $ceiling = (int) config('chatbot.max_tokens_ceiling', 2048);

        $maxTokens = max(1, min($requestedTokens, $ceiling));

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

        $body = $response->json();

        return $this->sendResponse($this->envelopeClaudePayload(is_array($body) ? $body : [], $model), 'OK');
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
     * @param  array<string, mixed>  $meta  chatbot_meta (source-specific fields)
     * @return array<string, mixed>
     */
    private function buildAnthropicCompatibleScriptedBody(string $assistantText, array $meta): array
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
     * @param  array<string, mixed>  $anthropicPayload
     * @return array<string, mixed>
     */
    private function envelopeClaudePayload(array $anthropicPayload, string $resolvedModel): array
    {
        $assistantText = $this->assistantTextFromAnthropicBody($anthropicPayload);

        $anthropicPayload['reply'] = $assistantText;

        $anthropicPayload['chatbot_meta'] = [
            'source' => 'claude',
            'model' => $resolvedModel,
        ];

        return $anthropicPayload;
    }

    /**
     * @param  array<string, mixed>  $body
     */
    private function assistantTextFromAnthropicBody(array $body): string
    {
        $content = $body['content'] ?? [];
        if (! is_array($content)) {
            return '';
        }

        $parts = [];
        foreach ($content as $block) {
            if (! is_array($block)) {
                continue;
            }
            if (($block['type'] ?? '') !== 'text') {
                continue;
            }

            $t = isset($block['text']) ? (string) $block['text'] : '';
            $parts[] = trim($t);
        }

        return trim(implode("\n", array_filter($parts)));
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

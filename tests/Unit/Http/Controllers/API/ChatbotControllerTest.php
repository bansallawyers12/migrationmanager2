<?php

namespace Tests\Unit\Http\Controllers\API;

use App\Http\Controllers\API\ChatbotController;
use App\Services\Chatbot\ChatbotKnowledgeMatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ChatbotControllerTest extends TestCase
{
    #[Test]
    public function scripted_faq_returns_existing_client_shape_without_calling_gemini(): void
    {
        Http::preventStrayRequests();
        Http::fake();

        $matcher = Mockery::mock(ChatbotKnowledgeMatcher::class);
        $matcher->shouldReceive('resolve')
            ->once()
            ->with('What offices do you have?')
            ->andReturn([
                'faq_id' => 501,
                'answer' => 'We have offices in Melbourne and Adelaide.',
                'confidence' => 88.0,
                'category' => '3.1 General Enquiries',
            ]);

        $response = (new ChatbotController)->chat(
            Request::create('/api/chatbot', 'POST', ['message' => 'What offices do you have?']),
            $matcher
        );

        $payload = $response->getData(true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($payload['success']);
        $this->assertSame('We have offices in Melbourne and Adelaide.', $payload['data']['reply']);
        $this->assertSame('text', $payload['data']['content'][0]['type']);
        $this->assertSame('We have offices in Melbourne and Adelaide.', $payload['data']['content'][0]['text']);
        $this->assertSame('training_script_exact', $payload['data']['chatbot_meta']['source']);
        $this->assertSame('bansal-training-script-exact', $payload['data']['model']);
        Http::assertNothingSent();
    }

    #[Test]
    public function gemini_fallback_keeps_content_and_reply_keys_for_the_client_portal(): void
    {
        config([
            'chatbot.api_key' => 'test-gemini-key',
            'chatbot.default_model' => 'gemini-3.6-flash',
            'chatbot.system_prompt_path' => storage_path('framework/testing/chatbot-prompt-missing.txt'),
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'generativelanguage.googleapis.com/v1beta/models/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'You can book an appointment from the portal.'],
                            ],
                        ],
                    ],
                ],
                'usageMetadata' => [
                    'promptTokenCount' => 12,
                    'candidatesTokenCount' => 8,
                ],
            ], 200),
        ]);

        $matcher = Mockery::mock(ChatbotKnowledgeMatcher::class);
        $matcher->shouldReceive('resolve')
            ->once()
            ->with('How do I book?')
            ->andReturn(null);

        $response = (new ChatbotController)->chat(
            Request::create('/api/chatbot', 'POST', ['message' => 'How do I book?']),
            $matcher
        );

        $payload = $response->getData(true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($payload['success']);
        $this->assertSame('You can book an appointment from the portal.', $payload['data']['reply']);
        $this->assertSame('You can book an appointment from the portal.', $payload['data']['content'][0]['text']);
        $this->assertSame('gemini', $payload['data']['chatbot_meta']['source']);
        $this->assertSame('gemini-3.6-flash', $payload['data']['model']);

        Http::assertSent(function ($request): bool {
            return str_contains($request->url(), 'generativelanguage.googleapis.com')
                && ! str_contains($request->url(), 'api.anthropic.com')
                && $request['contents'][0]['role'] === 'user'
                && $request['contents'][0]['parts'][0]['text'] === 'How do I book?';
        });
    }

    #[Test]
    public function missing_api_key_returns_503_without_calling_gemini(): void
    {
        config(['chatbot.api_key' => '']);
        Http::preventStrayRequests();
        Http::fake();

        $matcher = Mockery::mock(ChatbotKnowledgeMatcher::class);
        $matcher->shouldReceive('resolve')->once()->andReturn(null);

        $response = (new ChatbotController)->chat(
            Request::create('/api/chatbot', 'POST', ['message' => 'Hello']),
            $matcher
        );

        $this->assertSame(503, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['success']);
        Http::assertNothingSent();
    }

    #[Test]
    public function validation_failure_does_not_call_gemini(): void
    {
        Http::preventStrayRequests();
        Http::fake();

        $matcher = Mockery::mock(ChatbotKnowledgeMatcher::class);
        $matcher->shouldReceive('resolve')->never();

        $response = (new ChatbotController)->chat(
            Request::create('/api/chatbot', 'POST', []),
            $matcher
        );

        $this->assertSame(422, $response->getStatusCode());
        Http::assertNothingSent();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

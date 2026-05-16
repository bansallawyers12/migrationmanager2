<?php

namespace Tests\Unit\Services;

use App\Models\ChatbotFaq;
use App\Services\Chatbot\ChatbotKnowledgeMatcher;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ChatbotKnowledgeMatcherUnitTest extends TestCase
{
    public function test_office_paraphrase_maps_to_exact_training_answer(): void
    {
        config(['chatbot.faq_match_threshold' => 76]);

        $entries = require database_path('chatbot_training/faq_entries.php');

        /** @var array<string, mixed> $needle */
        $needle = collect($entries)->firstWhere('question', 'Where are your offices?');
        $this->assertIsArray($needle);

        $expectedAnswer = (string) ($needle['answer'] ?? '');
        self::assertNotSame('', $expectedAnswer);

        $faq = new ChatbotFaq([
            'category' => '3.1 General Enquiries',
            'sort_order' => (int) ($needle['sort_order'] ?? 103),
            'question' => 'Where are your offices?',
            'answer' => $expectedAnswer,
            'match_signals' => (string) ($needle['match_signals'] ?? ''),
        ]);
        $faq->id = 501;

        $matcher = new ChatbotKnowledgeMatcher(new Collection([$faq]));
        $match = $matcher->resolve('What offices do you have?');

        self::assertNotNull($match);
        self::assertSame(501, $match['faq_id']);
        self::assertSame(trim($expectedAnswer), $match['answer']);
        self::assertGreaterThanOrEqual(76.0, $match['confidence']);
    }
}

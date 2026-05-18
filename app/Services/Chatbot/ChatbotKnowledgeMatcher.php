<?php

namespace App\Services\Chatbot;

use App\Models\ChatbotFaq;
use Illuminate\Support\Collection;

class ChatbotKnowledgeMatcher
{
    /**
     * @param  Collection<int, ChatbotFaq>|null  $inMemoryFaqs  When set (e.g. in tests), database is bypassed entirely.
     */
    public function __construct(
        private readonly ?Collection $inMemoryFaqs = null,
    ) {}
    /** @var array<int, string> */
    private const STOPWORDS = [
        'what', 'when', 'where', 'which', 'that', 'this', 'with', 'from', 'your',
        'have', 'does', 'will', 'need', 'want', 'tell', 'more', 'some', 'just', 'very',
        'also', 'like', 'then', 'than', 'into', 'been', 'about', 'here', 'there',
        'they', 'them', 'only', 'even', 'other', 'such', 'any', 'get', 'got', 'the',
        'and', 'for', 'are', 'was', 'how', 'can', 'you', 'she', 'her', 'his', 'our',
        'who', 'may', 'its', 'all', 'not', 'but', 'visa', 'australia', 'australian',
    ];

    /**
     * Finds the best scripted training-library reply for the user's latest utterance.
     *
     * @return array{faq_id:int, answer:string, confidence:float, category:string}|null
     */
    public function resolve(string $latestUserMessage): ?array
    {
        $norm = $this->normalize($latestUserMessage);
        if ($norm === '') {
            return null;
        }

        if ($this->inMemoryFaqs === null && ! ChatbotFaq::query()->exists()) {
            return null;
        }

        $threshold = (float) config('chatbot.faq_match_threshold', 76);

        $faqs = $this->inMemoryFaqs ?? $this->faqRecordsFromDatabase();
        $best = null;

        foreach ($faqs as $faq) {
            $signal = $this->signalScore($norm, $faq->match_signals);
            $similar = $this->similarityScore($norm, $faq->question);
            $overlap = $this->tokenOverlapScore($norm, $faq->question);

            $confidence = max($signal, $similar * 0.92, $overlap);

            if ($confidence < $threshold) {
                continue;
            }

            if ($best === null) {
                $best = ['faq' => $faq, 'confidence' => $confidence];
                continue;
            }

            if ($confidence > $best['confidence'] + 0.01) {
                $best = ['faq' => $faq, 'confidence' => $confidence];
                continue;
            }

            /** @var ChatbotFaq $prev */
            $prev = $best['faq'];
            if (
                abs($confidence - $best['confidence']) <= 0.01
                && ($faq->sort_order > $prev->sort_order
                    || ($faq->sort_order === $prev->sort_order && $faq->id > $prev->id))
            ) {
                $best = ['faq' => $faq, 'confidence' => $confidence];
            }
        }

        if ($best === null) {
            return null;
        }

        /** @var ChatbotFaq $chosen */
        $chosen = $best['faq'];

        return [
            'faq_id' => (int) $chosen->id,
            'answer' => $chosen->answer,
            'confidence' => round((float) $best['confidence'], 2),
            'category' => $chosen->category,
        ];
    }



    private function normalize(string $text): string
    {
        $text = mb_strtolower(trim(preg_replace('/\s+/u', ' ', $text) ?? ''), 'UTF-8');

        return $text !== '' ? $text : '';
    }

    /** @return Collection<int, ChatbotFaq> */
    private function faqRecordsFromDatabase(): Collection
    {
        return ChatbotFaq::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    private function signalScore(string $norm, ?string $signals): float
    {
        if ($signals === null || trim($signals) === '') {
            return 0.0;
        }

        $parts = preg_split('/\|/', $signals) ?: [];
        $hits = 0;

        foreach ($parts as $raw) {
            $pat = trim((string) $raw);
            if ($pat === '') {
                continue;
            }
            if ($this->regexMatches($pat, $norm)) {
                $hits++;
            }
        }

        if ($hits === 0) {
            return 0.0;
        }

        return min(100.0, 78.0 + ($hits * 4));
    }

    /**
     * PCRE alternative fragments from `match_signals` (may include \b, quantifiers, etc.).
     */
    private function regexMatches(string $pattern, string $haystack): bool
    {
        $result = @preg_match('~'.$pattern.'~iu', $haystack);

        return $result === 1;
    }

    private function similarityScore(string $normMessage, string $question): float
    {
        $q = $this->normalize($question);
        similar_text($normMessage, $q, $pct);

        return (float) $pct;
    }

    private function tokenOverlapScore(string $normMessage, string $question): float
    {
        $tokens = preg_split('/[^\p{L}\p{N}+\/]+/u', $question, -1, PREG_SPLIT_NO_EMPTY);
        $tokens = is_array($tokens) ? array_map(static fn ($t) => mb_strtolower((string) $t, 'UTF-8'), $tokens) : [];
        $significant = array_values(array_filter($tokens, function (string $t): bool {
            if (strlen($t) < 5) {
                return false;
            }

            return ! in_array($t, self::STOPWORDS, true);
        }));

        if ($significant === []) {
            return 0.0;
        }

        $hits = 0;
        foreach ($significant as $word) {
            if (mb_stripos($normMessage, $word, 0, 'UTF-8') !== false) {
                $hits++;
            }
        }

        return ($hits / count($significant)) * 100 * 0.85;
    }
}

<?php

namespace App\Support\Chatbot;

use App\Models\ChatbotFaq;

/**
 * Imports scripted chatbot FAQs from `database/chatbot_training/faq_entries.php`
 * (exported from `Bansal_Immigration_Chatbot_Training.docx`).
 */
class ChatbotFaqInstaller
{
    public static function installFromPhpArray(): void
    {
        $entries = require database_path('chatbot_training/faq_entries.php');

        if (! is_array($entries)) {
            return;
        }

        ChatbotFaq::query()->delete();

        foreach ($entries as $row) {
            ChatbotFaq::query()->create([
                'category' => (string) $row['category'],
                'sort_order' => (int) ($row['sort_order'] ?? 0),
                'question' => (string) $row['question'],
                'answer' => (string) $row['answer'],
                'match_signals' => isset($row['match_signals']) ? (string) $row['match_signals'] : null,
            ]);
        }
    }
}

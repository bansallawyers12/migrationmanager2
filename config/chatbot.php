<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bansal Immigration — client portal chatbot (Anthropic Claude)
    |--------------------------------------------------------------------------
    |
    | System prompt content should align with internal training docs.
    | Override the file path via CHATBOT_SYSTEM_PROMPT_PATH (.env) if needed.
    |
    */

    'system_prompt_path' => env('CHATBOT_SYSTEM_PROMPT_PATH', resource_path('prompts/bansal_immigration_chatbot_system.txt')),

    /*
    | Maximum prior turns (user + assistant pairs count as 2 messages).
    | The current request "message" is appended after this history.
    */

    'max_conversation_messages' => (int) env('CHATBOT_MAX_CONVERSATION_MESSAGES', 24),

    /*
    | Scripted FAQ matching (rows in `chatbot_faqs`, installed via `php artisan chatbot:seed-faq-library`).
    |
    */

    /** Minimum matcher confidence [0–100] before skipping Claude and returning scripted text */
    'faq_match_threshold' => (float) env('CHATBOT_FAQ_THRESHOLD', 76),

    /*
    | LLM safeguards (prevent client abuse of upstream pricing / output size).
    */

    /** Override request `model` unless allow_client_model_override is true */
    'default_model' => env('CHATBOT_DEFAULT_MODEL', 'claude-sonnet-4-6'),

    'allow_client_model_override' => filter_var(env('CHATBOT_ALLOW_CLIENT_MODEL', false), FILTER_VALIDATE_BOOL),

    'max_tokens_default' => (int) env('CHATBOT_MAX_TOKENS_DEFAULT', 1024),

    'max_tokens_ceiling' => (int) env('CHATBOT_MAX_TOKENS_CEILING', 2048),

];

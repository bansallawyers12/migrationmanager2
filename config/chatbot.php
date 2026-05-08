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

];

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Canonical Q&A from Bansal Immigration chatbot training (Section 3, etc.).
 * Used for scripted exact replies when confidence is high enough.
 */
class ChatbotFaq extends Model
{
    protected $fillable = [
        'category',
        'sort_order',
        'question',
        'answer',
        'match_signals',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}

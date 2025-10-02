<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatMessage extends Model
{
    protected $table = 'ai_chat_messages';

    protected $fillable = ['chat_id', 'sender', 'message'];
}



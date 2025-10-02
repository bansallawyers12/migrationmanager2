<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChat extends Model
{
    protected $table = 'ai_chats';

    protected $fillable = ['client_id', 'title'];
	
	public function messages()
    {
        return $this->hasMany(AiChatMessage::class, 'chat_id');
    }
}



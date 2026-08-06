<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'sender_number',
        'sender_name',
        'region_id',
        'incoming_message',
        'detected_intent',
        'bot_reply',
        'handled_by_ai',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}

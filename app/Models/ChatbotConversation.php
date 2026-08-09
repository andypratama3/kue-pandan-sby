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
        'current_step',
        'context_data',
        'last_interaction_at',
        'bot_reply',
        'handled_by_ai',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'handled_by_ai' => 'boolean',
        'context_data' => 'array',
        'last_interaction_at' => 'datetime',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}

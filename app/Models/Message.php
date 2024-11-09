<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id', // ID of the conversation this message belongs to
        'user_id',         // ID of the user who sent the message
        'content',         // The message text
        'status',          // Message status: 'sent', 'delivered', 'read'
    ];

    /**
     * The conversation this message belongs to.
     */
    public function conversation():BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * The user who sent the message.
     */
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTimeAttribute(): string {
        return date(
            "d M Y, H:i:s",
            strtotime($this->attributes['created_at'])
        );
    }
}

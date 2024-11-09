<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',       // 'private' or 'group'
        'name',       // Only used for group conversations
        'created_by', // ID of the user who created the conversation
    ];

    /**
     * The user who created the conversation.
     */
    public function creator():BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The participants in the conversation.
     */
    public function participants():BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_user')
            ->withPivot('is_admin', 'last_read_at')
            ->withTimestamps();
    }

    /**
     * The messages in the conversation.
     */
    public function messages():HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage():HasOne
    {
        return $this->hasOne(Message::class)->latest();
    }

}

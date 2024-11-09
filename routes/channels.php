<?php

use Illuminate\Support\Facades\Broadcast;

//Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//    return (int) $user->id === (int) $id;
//});

//Broadcast::channel('chat.{id}', function ($user, $id) {
//    return true;
//});

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    // Check if the user is a participant in the conversation
    $conversation = \App\Models\Conversation::find($conversationId);

    if (!$conversation) {
        return false;
    }

    return $conversation->participants()->where('user_id', $user->id)->exists();
});

Broadcast::channel('presence.chat.{conversationId}', function ($user,$conversationId) {
    return ['id' => $user->id, 'name' => $user->name];
});

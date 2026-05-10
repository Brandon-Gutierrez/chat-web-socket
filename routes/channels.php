<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    if ($user->chats->contains($chatId)) {
        return ['id' => $user->id, 'name' => $user->name, 'avatar' => $user->avatar];
    }
});
<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent
{
    use Dispatchable, SerializesModels;

    public $message;
    public $chatId;

    public function __construct(Message $message, $chatId)
    {
        $this->message = $message;
        $this->chatId = $chatId;
    }

    public function broadcastOn()
    {
        // Usamos PresenceChannel para saber quién está en línea
        return new PresenceChannel('chat.' . $this->chatId);
    }
}

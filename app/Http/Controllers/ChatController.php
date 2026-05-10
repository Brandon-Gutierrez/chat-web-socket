<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Events\UserJoinedChat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:255']);
        
        $chat = Chat::create(['name' => $request->name]);
        $chat->users()->attach(auth()->id());

        return redirect()->route('chat.show', $chat->id);
    }

    public function join($id) {
        $chat = Chat::findOrFail($id);
        
        if (!$chat->users()->where('user_id', auth()->id())->exists()) {
            $chat->users()->attach(auth()->id());
            
            // Crear mensaje de sistema
            $message = $chat->messages()->create([
                'user_id' => auth()->id(),
                'content' => auth()->user()->name . ' se unió al chat',
                'is_system_message' => true
            ]);

            broadcast(new UserJoinedChat($chat->id, $message))->toOthers();
        }

        return redirect()->route('chat.show', $chat->id);
    }

    public function show($id) {
        $chat = Chat::with('messages.user', 'users')->findOrFail($id);
        return view('chat', compact('chat'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        /** @var User $user */
        $user = $request->user();

        $chat = Chat::create([
            'id' => Str::uuid(),
            'name' => (string) $request->input('name'),
        ]);

        $chat->users()->attach($user->getKey());

        return redirect()->route('chat.show', $chat->id);
    }

    public function join(Request $request)
    {
        $request->validate(['chat_id' => 'required|uuid']);
        /** @var User $user */
        $user = $request->user();
        $chat = Chat::findOrFail((string) $request->input('chat_id'));

        if (!$chat->users()->where('user_id', $user->getKey())->exists()) {
            $chat->users()->attach($user->getKey());
        }

        return redirect()->route('chat.show', $chat->id);
    }

    public function show(string $id)
    {
        /** @var User $user */
        $user = request()->user();
        $chat = Chat::with(['messages.user', 'users'])->findOrFail($id);

        // Si el usuario accede por link y no está en la sala, lo unimos
        if (!$chat->users()->where('user_id', $user->getKey())->exists()) {
            $chat->users()->attach($user->getKey());
        }

        return view('chat.show', compact('chat'));
    }

    public function sendMessage(Request $request, string $id)
    {
        $request->validate(['content' => 'required|string']);
        /** @var User $user */
        $user = $request->user();
        $chat = Chat::findOrFail($id);

        $message = $chat->messages()->create([
            'user_id' => $user->getKey(),
            'content' => (string) $request->input('content'),
            'is_system_message' => false,
        ]);

        broadcast(new MessageSent($message->load('user'), (string) $chat->id))->toOthers();

        return response()->json($message);
    }
}

<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = ChatMessage::with('user')->latest()->take(50);
        
        if ($user->chat_cleared_at) {
            $query->where('created_at', '>', $user->chat_cleared_at);
        }

        $messages = $query->get()->reverse()->values();

        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = ChatMessage::create([
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        broadcast(new MessageSent($message));

        return response()->json(['status' => 'success', 'message' => $message->load('user')]);
    }

    public function clear()
    {
        $user = Auth::user();
        $user->chat_cleared_at = now();
        $user->save();

        return response()->json(['status' => 'success']);
    }
}

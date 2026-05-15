<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function index()
    {
        $users = $this->chatService->getAllowedUsers();
        $unreadCounts = $this->chatService->getUnreadCounts();

        return view('chat.index', compact('users', 'unreadCounts'));
    }

    public function show(User $user)
    {
        $this->chatService->markAsRead($user);

        $messages = Message::where(function ($query) use ($user) {
            $query->where('sender_id', Auth::id())->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)->where('receiver_id', Auth::id());
        })
        ->orderBy('created_at', 'asc')
        ->get();

        $users = $this->chatService->getAllowedUsers();
        $unreadCounts = $this->chatService->getUnreadCounts();

        return view('chat.show', compact('messages', 'user', 'users', 'unreadCounts'));
    }

    public function store(Request $request, User $user)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $message = $this->chatService->sendMessage($user, $request->only('message'));

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message->load('sender')]);
        }

        return redirect()->route('chat.show', $user->id);
    }
}

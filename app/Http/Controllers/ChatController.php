<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    private function touchOnlineStatus()
    {
        if (Auth::check()) {
            Cache::put('user-is-online-' . Auth::id(), true, now()->addMinutes(5));
        }
    }

    public function index()
    {
        $this->touchOnlineStatus();
        $users = $this->chatService->getAllowedUsers();
        $unreadCounts = $this->chatService->getUnreadCounts();

        return view('chat.index', compact('users', 'unreadCounts'));
    }

    public function show(User $user)
    {
        $this->touchOnlineStatus();
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

    public function status(User $user)
    {
        $this->touchOnlineStatus();
        $this->chatService->markAsRead($user);

        // Fetch IDs of messages sent by Auth::user() to $user that have been read
        $readMessageIds = Message::where('sender_id', Auth::id())
            ->where('receiver_id', $user->id)
            ->where('is_read', true)
            ->pluck('id');

        // Fetch new unread incoming messages from $user (if any)
        $newIncomingMessages = Message::where('sender_id', $user->id)
            ->where('receiver_id', Auth::id())
            ->where('created_at', '>=', now()->subSeconds(10))
            ->get();

        return response()->json([
            'is_online' => $user->is_online,
            'read_message_ids' => $readMessageIds,
            'new_incoming_messages' => $newIncomingMessages,
            'unread_counts' => $this->chatService->getUnreadCounts(),
        ]);
    }

    public function store(Request $request, User $user)
    {
        $this->touchOnlineStatus();

        try {
            $request->validate(['message' => 'required|string|max:1000']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Chat validation failed', [
                'errors' => $e->errors()
            ]);
            throw $e;
        }

        $message = $this->chatService->sendMessage($user, $request->only('message'));

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message->load('sender')]);
        }

        return redirect()->route('chat.show', $user->id);
    }
}

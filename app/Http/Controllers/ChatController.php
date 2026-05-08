<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Get allowed users based on the current user's role.
     */
    private function getAllowedUsers()
    {
        $user = Auth::user();
        $query = User::where('id', '!=', $user->id);

        if ($user->role === 'mahasiswa') {
            $thesis = \App\Models\Thesis::where('student_id', $user->id)->first();
            $dosenIds = $thesis ? array_filter([$thesis->pembimbing1_id, $thesis->pembimbing2_id]) : [];
            $query->where(function($q) use ($dosenIds) {
                $q->whereIn('id', $dosenIds)->orWhere('role', 'admin');
            });
        } elseif ($user->role === 'dosen') {
            $studentIds = \App\Models\Thesis::where('pembimbing1_id', $user->id)
                ->orWhere('pembimbing2_id', $user->id)
                ->pluck('student_id');
            $query->where(function($q) use ($studentIds) {
                $q->whereIn('id', $studentIds)->orWhere('role', 'admin');
            });
        }
        
        return $query->orderBy('name', 'asc')->get();
    }

    /**
     * Display a list of contacts and recent chats.
     */
    public function index()
    {
        $users = $this->getAllowedUsers();
            
        // Get unread counts per user
        $unreadCounts = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->selectRaw('sender_id, count(*) as count')
            ->groupBy('sender_id')
            ->pluck('count', 'sender_id');

        return view('chat.index', compact('users', 'unreadCounts'));
    }

    /**
     * Show the chat room for a specific user.
     */
    public function show(User $user)
    {
        // Mark messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Get all messages between auth user and selected user
        $messages = Message::where(function ($query) use ($user) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', Auth::id());
        })
        ->orderBy('created_at', 'asc')
        ->get();

        // Get allowed contacts for the sidebar
        $users = $this->getAllowedUsers();
        
        $unreadCounts = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->selectRaw('sender_id, count(*) as count')
            ->groupBy('sender_id')
            ->pluck('count', 'sender_id');

        return view('chat.show', compact('messages', 'user', 'users', 'unreadCounts'));
    }

    /**
     * Store a new message.
     */
    public function store(Request $request, User $user)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        \App\Models\ActivityLog::log('Kirim Pesan', "User mengirim pesan chat ke {$user->name}.", 'Chat');

        // Notify Receiver
        $user->notify(new \App\Notifications\GeneralNotification(
            'Pesan Baru',
            Auth::user()->name . ": " . \Illuminate\Support\Str::limit($request->message, 50),
            route('chat.show', Auth::id()),
            'message'
        ));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('sender')
            ]);
        }

        return redirect()->route('chat.show', $user->id);
    }
}

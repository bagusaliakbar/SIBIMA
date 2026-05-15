<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use App\Models\Thesis;
use App\Models\ActivityLog;
use App\Notifications\GeneralNotification;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatService
{
    /**
     * Get allowed users based on the current user's role.
     */
    public function getAllowedUsers()
    {
        $user = Auth::user();
        $query = User::where('id', '!=', $user->id);

        if ($user->role === 'mahasiswa') {
            $thesis = Thesis::where('student_id', $user->id)->first();
            $dosenIds = $thesis ? array_filter([$thesis->pembimbing1_id, $thesis->pembimbing2_id]) : [];
            $query->where(function($q) use ($dosenIds) {
                $q->whereIn('id', $dosenIds)->orWhere('role', 'admin');
            });
        } elseif ($user->role === 'dosen') {
            $studentIds = Thesis::where(function($q) use ($user) {
                    $q->where('pembimbing1_id', $user->id)
                      ->orWhere('pembimbing2_id', $user->id);
                })
                ->where('status', '!=', 'completed')
                ->pluck('student_id');
            $query->where(function($q) use ($studentIds) {
                $q->whereIn('id', $studentIds)->orWhere('role', 'admin');
            });
        }
        
        return $query->orderBy('name', 'asc')->get();
    }

    /**
     * Send a new message.
     */
    public function sendMessage(User $receiver, array $data)
    {
        $sender = Auth::user();
        $message = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $data['message'],
            'is_read' => false,
        ]);

        ActivityLog::log('Kirim Pesan', "User mengirim pesan chat ke {$receiver->name}.", 'Chat');
        
        broadcast(new MessageSent($message))->toOthers();

        $receiver->notify(new GeneralNotification(
            'Pesan Baru',
            $sender->name . ": " . Str::limit($data['message'], 50),
            route('chat.show', $sender->id),
            'message'
        ));

        return $message;
    }

    /**
     * Mark messages as read.
     */
    public function markAsRead(User $sender)
    {
        Message::where('sender_id', $sender->id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Get unread counts.
     */
    public function getUnreadCounts()
    {
        return Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->selectRaw('sender_id, count(*) as count')
            ->groupBy('sender_id')
            ->pluck('count', 'sender_id');
    }
}

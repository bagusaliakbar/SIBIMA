<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BirthdayWish;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BirthdayWishController extends Controller
{
    /**
     * Get birthday wishes received by a specific user for current year.
     */
    public function index(Request $request, User $user)
    {
        $wishes = BirthdayWish::with('fromUser')
            ->where('to_user_id', $user->id)
            ->where('year', now()->year)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($wish) {
                return [
                    'id' => $wish->id,
                    'sender_id' => $wish->fromUser?->id,
                    'sender_name' => $wish->fromUser?->name ?? 'Pengguna',
                    'sender_role' => $wish->fromUser?->role ?? 'mahasiswa',
                    'sender_avatar' => $wish->fromUser?->avatar_url,
                    'message' => $wish->message,
                    'time_ago' => $wish->created_at->diffForHumans(),
                    'created_at' => $wish->created_at->format('d M Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'target_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'avatar' => $user->avatar_url,
            ],
            'wishes' => $wishes,
            'total' => $wishes->count(),
        ]);
    }

    /**
     * Store a new birthday wish.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'to_user_id' => ['required', 'exists:users,id'],
            'message' => ['required', 'string', 'max:300'],
        ], [
            'to_user_id.required' => 'Penerima ucapan harus ditentukan.',
            'to_user_id.exists' => 'Penerima ucapan tidak ditemukan.',
            'message.required' => 'Pesan ucapan tidak boleh kosong.',
            'message.max' => 'Pesan ucapan maksimal 300 karakter.',
        ]);

        $sender = Auth::user();
        $targetUserId = (int) $validated['to_user_id'];

        if ($sender->id === $targetUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat mengirim ucapan ulang tahun ke diri sendiri.',
            ], 422);
        }

        $targetUser = User::findOrFail($targetUserId);

        if (!$targetUser->is_birthday) {
            return response()->json([
                'success' => false,
                'message' => $targetUser->name . ' tidak sedang berulang tahun hari ini.',
            ], 422);
        }

        $currentYear = (int) now()->year;

        $existing = BirthdayWish::where('from_user_id', $sender->id)
            ->where('to_user_id', $targetUserId)
            ->where('year', $currentYear)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mengirimkan ucapan selamat ulang tahun kepada ' . $targetUser->name . ' tahun ini.',
            ], 422);
        }

        $cleanMessage = strip_tags(trim($validated['message']));

        $wish = BirthdayWish::create([
            'from_user_id' => $sender->id,
            'to_user_id' => $targetUserId,
            'year' => $currentYear,
            'message' => $cleanMessage,
        ]);

        ActivityLog::log(
            'Ucapan Ulang Tahun',
            "{$sender->name} ({$sender->role}) mengirimkan doa/ucapan selamat ulang tahun untuk {$targetUser->name} ({$targetUser->role}).",
            'Ulang Tahun',
            $wish,
            [
                'to_user_id' => $targetUser->id,
                'to_user_name' => $targetUser->name,
                'message_preview' => mb_strimwidth($cleanMessage, 0, 80, '...'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Ucapan dan doa selamat ulang tahun berhasil terkirim!',
            'wish' => [
                'id' => $wish->id,
                'to_user_id' => $targetUserId,
                'message' => $cleanMessage,
                'created_at' => $wish->created_at->format('d M Y H:i'),
            ],
        ]);
    }
}


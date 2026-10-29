<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PushSubscriptionController extends Controller
{
    /**
     * Store or update a push notification device subscription.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint'         => 'required|string',
            'keys.p256dh'      => 'nullable|string',
            'keys.auth'        => 'nullable|string',
            'contentEncoding'  => 'nullable|string|max:50',
        ]);

        $user = Auth::user();

        $subscription = PushSubscription::updateOrCreate(
            [
                'user_id'  => $user->id,
                'endpoint' => $validated['endpoint'],
            ],
            [
                'public_key'       => $validated['keys']['p256dh'] ?? null,
                'auth_token'       => $validated['keys']['auth'] ?? null,
                'content_encoding' => $validated['contentEncoding'] ?? 'aes128gcm',
                'user_agent'       => substr((string) $request->userAgent(), 0, 255),
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Perangkat berhasil terhubung dengan notifikasi HP SIBIMA.',
            'id'      => $subscription->id,
        ]);
    }

    /**
     * Remove a push notification device subscription (user uninstalled or disabled).
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        $deleted = PushSubscription::where('user_id', Auth::id())
            ->where('endpoint', $request->input('endpoint'))
            ->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Langganan notifikasi perangkat berhasil dihapus.',
            'deleted' => $deleted > 0,
        ]);
    }

    /**
     * Get the push notification status for current user.
     */
    public function status(): JsonResponse
    {
        $count = Auth::user()->pushSubscriptions()->count();

        return response()->json([
            'subscribed' => $count > 0,
            'device_count' => $count,
        ]);
    }

    /**
     * Send an immediate test notification to verify push delivery on user's device.
     */
    public function test(Request $request): JsonResponse
    {
        $user = Auth::user();

        $payload = [
            'title' => '🔔 Uji Notifikasi SIBIMA Berhasil!',
            'body'  => 'Halo ' . $user->name . ', perangkat Anda kini telah terhubung secara resmi dengan sistem notifikasi SIBIMA FASILKOM UNSUB.',
            'icon'  => asset('icons/icon-192x192.png'),
            'badge' => asset('icons/icon-72x72.png'),
            'url'   => route('dashboard'),
            'tag'   => 'sibima-test-' . time(),
        ];

        return response()->json([
            'status'  => 'success',
            'message' => 'Notifikasi uji coba berhasil disiapkan.',
            'payload' => $payload,
        ]);
    }
}


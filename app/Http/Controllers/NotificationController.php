<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // If AJAX or API call (such as dropdown polling), return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'notifications' => $user->notifications()->latest()->take(20)->get(),
                'unread_count' => $user->unreadNotifications->count()
            ]);
        }

        // Full browser page request: render the dedicated notifications history view
        $search = $request->input('search');
        $activeTab = $request->input('tab', 'all'); // 'all', 'unread', 'read'

        $query = $user->notifications()->latest();

        if ($activeTab === 'unread') {
            $query->whereNull('read_at');
        } elseif ($activeTab === 'read') {
            $query->whereNotNull('read_at');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('data', 'like', '%' . $search . '%');
            });
        }

        $notifications = $query->paginate(15)->appends([
            'search' => $search,
            'tab'    => $activeTab,
        ]);

        $unreadCount = $user->unreadNotifications->count();
        $totalCount = $user->notifications()->count();
        $readCount = max(0, $totalCount - $unreadCount);

        return view('notifications.index', compact('notifications', 'unreadCount', 'totalCount', 'readCount', 'search', 'activeTab'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notifikasi berhasil ditandai telah dibaca.');
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi berhasil ditandai telah dibaca.');
    }
}

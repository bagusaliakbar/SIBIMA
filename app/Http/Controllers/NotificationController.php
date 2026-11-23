<?php

namespace App\Http\Controllers;

use App\Models\MentoringSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // If AJAX or API call (such as dropdown polling), return JSON
        if ($request->wantsJson() || $request->ajax()) {
            $notifications = $user->notifications()->latest()->take(20)->get();

            // Batch-attach mentoring session schedule & attendance metadata for students (1 query total)
            $sessionMetaMap = [];
            if ($user->role === 'mahasiswa') {
                $mentoringIds = $notifications
                    ->map(fn ($n) => $n->data['mentoring_id'] ?? null)
                    ->filter()
                    ->unique()
                    ->values();

                if ($mentoringIds->isNotEmpty()) {
                    $sessions = MentoringSession::query()
                        ->whereIn('id', $mentoringIds)
                        ->get(['id', 'scheduled_at', 'scheduled_end_at', 'student_attendance_status', 'student_arrival_time', 'status']);

                    foreach ($sessions as $session) {
                        $startStr = $session->scheduled_at ? $session->scheduled_at->format('H:i') : null;
                        $endStr = $session->scheduled_end_at ? $session->scheduled_end_at->format('H:i') : null;
                        $hasRange = $startStr && $endStr && $session->scheduled_end_at->gt($session->scheduled_at);

                        $slots = [];
                        if ($hasRange) {
                            $cursor = Carbon::parse($session->scheduled_at);
                            $endCarbon = Carbon::parse($session->scheduled_end_at);
                            while ($cursor->lte($endCarbon) && count($slots) < 24) {
                                $slots[] = $cursor->format('H:i');
                                $cursor->addMinutes(30);
                            }
                            if (end($slots) !== $endStr && count($slots) < 24) {
                                $slots[] = $endStr;
                            }
                        }

                        $sessionMetaMap[$session->id] = [
                            'has_time_range' => $hasRange,
                            'start_time' => $startStr,
                            'end_time' => $endStr,
                            'slots' => $slots,
                            'attendance_status' => $session->student_attendance_status ?? 'pending',
                            'arrival_time' => $session->student_arrival_time,
                            'is_actionable' => in_array($session->status, ['pending', 'approved'], true),
                        ];
                    }
                }
            }

            $serialized = $notifications->map(function ($notif) use ($sessionMetaMap) {
                $arr = $notif->toArray();
                $mid = $notif->data['mentoring_id'] ?? null;
                if ($mid && isset($sessionMetaMap[$mid])) {
                    $arr['session_meta'] = $sessionMetaMap[$mid];
                }
                return $arr;
            })->values();

            return response()->json([
                'notifications' => $serialized,
                'unread_count' => $user->unreadNotifications()->count(),
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

        $unreadCount = $user->unreadNotifications()->count();
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

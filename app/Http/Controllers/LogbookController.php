<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use App\Models\MentoringSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class LogbookController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        if ($user->role === 'mahasiswa') {
            $thesis = Thesis::where('student_id', $user->id)
                ->with(['pembimbing1', 'pembimbing2'])
                ->first();

            $filterDosen = $request->input('dosen', 'all');

            // 1. Ambil seluruh sesi bimbingan selesai urut kronologis (asc) untuk penomoran resmi sesi
            $allCompletedSessions = MentoringSession::whereHas('thesis', function ($q) use ($user) {
                    $q->where('student_id', $user->id);
                })
                ->where('status', 'completed')
                ->where('is_absent', false)
                ->orderBy('scheduled_at', 'asc')
                ->orderBy('id', 'asc')
                ->get(['id', 'dosen_id', 'scheduled_at']);

            $sessionOrderMap = [];
            $sessionDosenOrderMap = [];
            $dosenSessionCounts = [];

            foreach ($allCompletedSessions as $index => $sess) {
                $sessionOrderMap[$sess->id] = $index + 1;
                $dId = $sess->dosen_id;
                $dosenSessionCounts[$dId] = ($dosenSessionCounts[$dId] ?? 0) + 1;
                $sessionDosenOrderMap[$sess->id] = $dosenSessionCounts[$dId];
            }

            $totalCompletedCount = $allCompletedSessions->count();
            $countP1 = $thesis && $thesis->pembimbing1_id 
                ? ($dosenSessionCounts[$thesis->pembimbing1_id] ?? 0) 
                : 0;
            $countP2 = $thesis && $thesis->pembimbing2_id 
                ? ($dosenSessionCounts[$thesis->pembimbing2_id] ?? 0) 
                : 0;

            // 2. Query data dengan pencarian (topik, catatan mhs, dan catatan/revisi dosen) serta filter pembimbing
            $sessionsQuery = MentoringSession::whereHas('thesis', function ($q) use ($user) {
                    $q->where('student_id', $user->id);
                })
                ->where('status', 'completed')
                ->where('is_absent', false)
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('topic', 'like', "%{$search}%")
                          ->orWhere('notes', 'like', "%{$search}%")
                          ->orWhere('feedback', 'like', "%{$search}%");
                    });
                });

            if ($filterDosen === 'p1' && $thesis?->pembimbing1_id) {
                $sessionsQuery->where('dosen_id', $thesis->pembimbing1_id);
            } elseif ($filterDosen === 'p2' && $thesis?->pembimbing2_id) {
                $sessionsQuery->where('dosen_id', $thesis->pembimbing2_id);
            } elseif (is_numeric($filterDosen)) {
                $sessionsQuery->where('dosen_id', $filterDosen);
            }

            $sessions = $sessionsQuery
                ->with('thesis.pembimbing1', 'thesis.pembimbing2', 'dosen')
                ->orderBy('scheduled_at', 'desc')
                ->paginate(10)
                ->appends(array_filter(['search' => $search, 'dosen' => $filterDosen !== 'all' ? $filterDosen : null]));

            return view('logbooks.index', compact(
                'sessions', 
                'search', 
                'thesis', 
                'filterDosen', 
                'sessionOrderMap', 
                'sessionDosenOrderMap', 
                'totalCompletedCount', 
                'countP1', 
                'countP2'
            ));
        }

        if (in_array($user->role, ['dosen', 'admin', 'kaprodi'])) {
            return redirect()->route('theses.index');
        }

        abort(403);
    }

    public function show(Request $request, Thesis $thesis)
    {
        $user = Auth::user();
        $isAuthorized = $user->role === 'admin' 
            || $user->role === 'kaprodi' 
            || ($user->role === 'dosen' && ($thesis->pembimbing1_id === $user->id || $thesis->pembimbing2_id === $user->id))
            || ($user->role === 'mahasiswa' && $thesis->student_id === $user->id);

        if (!$isAuthorized) abort(403);
            
        $thesis->load(['student', 'pembimbing1', 'pembimbing2']);

        $search = $request->input('search');
        $filterDosen = $request->input('dosen', 'all');

        // Query sesi aktif / mendatang
        $activeSessionsQuery = MentoringSession::where('thesis_id', $thesis->id)
            ->whereIn('status', ['pending', 'approved']);

        // Query sesi selesai (riwayat logbook)
        $completedSessionsQuery = MentoringSession::where('thesis_id', $thesis->id)
            ->where('status', 'completed')
            ->where('is_absent', false);

        // Ambil seluruh sesi selesai untuk perhitungan statistik & penomoran resmi (tanpa filter search/dosen)
        $allCompletedSessions = (clone $completedSessionsQuery)
            ->orderBy('scheduled_at', 'asc')
            ->orderBy('id', 'asc')
            ->get(['id', 'dosen_id', 'scheduled_at']);

        $totalCompletedCount = $allCompletedSessions->count();
        $p1SessionCount = $thesis->pembimbing1_id ? $allCompletedSessions->where('dosen_id', $thesis->pembimbing1_id)->count() : 0;
        $p2SessionCount = $thesis->pembimbing2_id ? $allCompletedSessions->where('dosen_id', $thesis->pembimbing2_id)->count() : 0;

        $sessionOrderMap = [];
        $dosenSessionCounts = [];
        $sessionDosenOrderMap = [];
        foreach ($allCompletedSessions as $idx => $sess) {
            $sessionOrderMap[$sess->id] = $idx + 1;
            $dId = $sess->dosen_id;
            $dosenSessionCounts[$dId] = ($dosenSessionCounts[$dId] ?? 0) + 1;
            $sessionDosenOrderMap[$sess->id] = $dosenSessionCounts[$dId];
        }

        // Terapkan filter pencarian jika diisi
        if (!empty($search)) {
            $completedSessionsQuery->where(function ($q) use ($search) {
                $q->where('topic', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('feedback', 'like', "%{$search}%");
            });
        }

        // Terapkan filter pembimbing jika dipilih 'p1', 'p2', atau dosen_id numerik
        if ($filterDosen === 'p1' && $thesis->pembimbing1_id) {
            $activeSessionsQuery->where('dosen_id', $thesis->pembimbing1_id);
            $completedSessionsQuery->where('dosen_id', $thesis->pembimbing1_id);
        } elseif ($filterDosen === 'p2' && $thesis->pembimbing2_id) {
            $activeSessionsQuery->where('dosen_id', $thesis->pembimbing2_id);
            $completedSessionsQuery->where('dosen_id', $thesis->pembimbing2_id);
        } elseif (is_numeric($filterDosen)) {
            $activeSessionsQuery->where('dosen_id', $filterDosen);
            $completedSessionsQuery->where('dosen_id', $filterDosen);
        }

        $activeSessions = $activeSessionsQuery
            ->with('dosen')
            ->orderBy('scheduled_at', 'asc')
            ->get();

        $completedSessions = $completedSessionsQuery
            ->with('dosen')
            ->orderBy('scheduled_at', 'desc')
            ->get();

        return view('logbooks.show', compact(
            'thesis', 
            'activeSessions', 
            'completedSessions', 
            'filterDosen', 
            'search',
            'totalCompletedCount', 
            'p1SessionCount', 
            'p2SessionCount',
            'sessionOrderMap',
            'sessionDosenOrderMap'
        ));
    }

    public function quickPreview(Thesis $thesis)
    {
        $user = Auth::user();
        $isAuthorized = $user->role === 'admin' 
            || $user->role === 'kaprodi' 
            || ($user->role === 'dosen' && ($thesis->pembimbing1_id === $user->id || $thesis->pembimbing2_id === $user->id))
            || ($user->role === 'mahasiswa' && $thesis->student_id === $user->id);

        if (!$isAuthorized) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $thesis->load(['student', 'pembimbing1', 'pembimbing2']);

        // Hilangkan pembatasan where('dosen_id', $user->id) agar Pembimbing 1 dan 2 dapat saling melihat sesi bimbingan
        $sessionsQuery = MentoringSession::where('thesis_id', $thesis->id)
            ->where('status', 'completed')
            ->where('is_absent', false);

        $allSessions = (clone $sessionsQuery)->get(['id', 'dosen_id']);
        $totalCompleted = $allSessions->count();
        $p1Completed = $thesis->pembimbing1_id ? $allSessions->where('dosen_id', $thesis->pembimbing1_id)->count() : 0;
        $p2Completed = $thesis->pembimbing2_id ? $allSessions->where('dosen_id', $thesis->pembimbing2_id)->count() : 0;

        $recentSessions = $sessionsQuery
            ->with('dosen:id,name')
            ->orderBy('scheduled_at', 'desc')
            ->limit(10)
            ->get();

        $formattedSessions = $recentSessions->map(function ($session, $index) use ($totalCompleted, $thesis, $user) {
            $scheduledDate = $session->scheduled_at ? \Carbon\Carbon::parse($session->scheduled_at) : null;
            $timeAgo = '-';
            $scheduledFormatted = '-';

            if ($scheduledDate) {
                if ($scheduledDate->isToday()) {
                    $timeAgo = 'Hari ini';
                } elseif ($scheduledDate->isYesterday()) {
                    $timeAgo = 'Kemarin';
                } else {
                    $days = (int) $scheduledDate->copy()->startOfDay()->diffInDays(now()->startOfDay());
                    $timeAgo = "{$days} hari lalu";
                }
                $scheduledFormatted = $scheduledDate->isoFormat('D MMMM Y • HH:mm') . ' WIB';
            }

            $dosenRole = ($session->dosen_id === $thesis->pembimbing1_id) 
                ? 'Pembimbing 1' 
                : (($session->dosen_id === $thesis->pembimbing2_id) ? 'Pembimbing 2' : 'Dosen');

            return [
                'id' => $session->id,
                'session_number' => $totalCompleted - $index,
                'topic' => $session->topic ?: 'Bimbingan Skripsi',
                'scheduled_at' => $scheduledFormatted,
                'time_ago' => $timeAgo,
                'type' => $session->type,
                'location' => $session->location,
                'feedback' => $session->feedback,
                'notes' => $session->notes,
                'dosen_id' => $session->dosen_id,
                'dosen_name' => $session->dosen ? $session->dosen->name : '-',
                'dosen_role' => $dosenRole,
                'is_my_session' => ($session->dosen_id === $user->id),
                'has_document' => !empty($session->document_path),
                'document_name' => $session->document_original_name,
            ];
        });

        return response()->json([
            'thesis_id' => $thesis->id,
            'student_name' => $thesis->student ? $thesis->student->name : '-',
            'student_identifier' => $thesis->student ? ($thesis->student->identifier ?? '-') : '-',
            'student_avatar' => $thesis->student ? $thesis->student->avatar_url : null,
            'student_phone' => $thesis->student ? $thesis->student->phone_number : null,
            'thesis_title' => $thesis->final_title ?? $thesis->title,
            'thesis_status' => $thesis->status,
            'pembimbing1_name' => $thesis->pembimbing1 ? $thesis->pembimbing1->name : '-',
            'pembimbing2_name' => $thesis->pembimbing2 ? $thesis->pembimbing2->name : '-',
            'p1_completed' => $p1Completed,
            'p2_completed' => $p2Completed,
            'total_completed' => $totalCompleted,
            'full_logbook_url' => route('theses.logbooks', $thesis->id),
            'sessions' => $formattedSessions,
        ]);
    }

    public function exportPdf(Request $request, Thesis $thesis = null)
    {
        $user = Auth::user();
        if (!$thesis) {
            $thesis = Thesis::where('student_id', $user->id)->first();
        }

        if (!$thesis) return back()->with('error', 'Data skripsi tidak ditemukan.');

        $isAuthorized = $user->role === 'admin' 
            || $user->role === 'kaprodi' 
            || ($user->role === 'mahasiswa' && $thesis->student_id === $user->id) 
            || ($user->role === 'dosen' && ($thesis->pembimbing1_id === $user->id || $thesis->pembimbing2_id === $user->id));

        if (!$isAuthorized) abort(403);

        $thesis->load(['student', 'pembimbing1', 'pembimbing2']);

        $filterDosen = $request->input('dosen');
        $filterDosenLabel = 'Semua Pembimbing';
        if ($filterDosen === 'p1' && $thesis->pembimbing1) {
            $filterDosenLabel = 'Pembimbing 1 (' . $thesis->pembimbing1->name . ')';
        } elseif ($filterDosen === 'p2' && $thesis->pembimbing2) {
            $filterDosenLabel = 'Pembimbing 2 (' . $thesis->pembimbing2->name . ')';
        }

        $sessions = MentoringSession::where('thesis_id', $thesis->id)
            ->when($filterDosen === 'p1' && $thesis->pembimbing1_id, function($q) use ($thesis) {
                $q->where('dosen_id', $thesis->pembimbing1_id);
            })
            ->when($filterDosen === 'p2' && $thesis->pembimbing2_id, function($q) use ($thesis) {
                $q->where('dosen_id', $thesis->pembimbing2_id);
            })
            ->when(is_numeric($filterDosen), function($q) use ($filterDosen) {
                $q->where('dosen_id', $filterDosen);
            })
            ->where('status', 'completed')
            ->where('is_absent', false)
            ->orderBy('scheduled_at', 'asc')
            ->get();

        $pdf = Pdf::loadView('logbooks.pdf', compact('thesis', 'sessions', 'filterDosen', 'filterDosenLabel'));
        
        \App\Models\ActivityLog::log('Export Logbook', "User mengekspor logbook mahasiswa {$thesis->student->name} ke PDF.", 'Logbook');

        return $pdf->download('Logbook_' . str_replace(' ', '_', $thesis->student->name) . '_' . date('Ymd') . '.pdf');
    }
}

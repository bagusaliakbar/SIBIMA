<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use App\Models\MentoringSession;
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

        if ($user->role === 'dosen') {
            $theses = Thesis::where(function($q) {
                    $q->where('pembimbing1_id', Auth::id())->orWhere('pembimbing2_id', Auth::id());
                })
                ->with('student')
                ->when($search, function ($query, $search) {
                    $query->whereHas('student', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")->orWhere('identifier', 'like', "%{$search}%");
                    })->orWhere('title', 'like', "%{$search}%");
                })
                ->withCount(['mentoringSessions as completed_sessions_count' => function ($q) {
                    $q->where('dosen_id', Auth::id())->where('status', 'completed')->where('is_absent', false);
                }])
                ->paginate(12)
                ->appends(['search' => $search]);

            return view('logbooks.dosen_index', compact('theses', 'search'));
        }

        abort(403);
    }

    public function show(Thesis $thesis)
    {
        $user = Auth::user();
        $isAuthorized = $user->role === 'admin' 
            || $user->role === 'kaprodi' 
            || ($user->role === 'dosen' && ($thesis->pembimbing1_id === $user->id || $thesis->pembimbing2_id === $user->id))
            || ($user->role === 'mahasiswa' && $thesis->student_id === $user->id);

        if (!$isAuthorized) abort(403);
            
        $thesis->load(['student', 'pembimbing1', 'pembimbing2']);

        $activeSessions = MentoringSession::where('thesis_id', $thesis->id)
            ->when($user->role === 'dosen', function($q) use ($user) {
                $q->where('dosen_id', $user->id);
            })
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        $completedSessions = MentoringSession::where('thesis_id', $thesis->id)
            ->when($user->role === 'dosen', function($q) use ($user) {
                $q->where('dosen_id', $user->id);
            })
            ->where('status', 'completed')
            ->where('is_absent', false)
            ->orderBy('scheduled_at', 'desc')
            ->get();

        return view('logbooks.show', compact('thesis', 'activeSessions', 'completedSessions'));
    }

    public function exportPdf(Request $request, Thesis $thesis = null)
    {
        $user = Auth::user();
        if (!$thesis) {
            $thesis = Thesis::where('student_id', $user->id)->first();
        }

        if (!$thesis) return back()->with('error', 'Data skripsi tidak ditemukan.');

        $isAuthorized = $user->role === 'admin' || $user->role === 'kaprodi' || ($user->role === 'mahasiswa' && $thesis->student_id === $user->id) || ($user->role === 'dosen' && ($thesis->pembimbing1_id === $user->id || $thesis->pembimbing2_id === $user->id));

        if (!$isAuthorized) abort(403);

        $thesis->load(['student', 'pembimbing1', 'pembimbing2']);

        $filterDosen = $request->input('dosen');
        $sessions = MentoringSession::where('thesis_id', $thesis->id)
            ->when($user->role === 'dosen', function($q) use ($user) {
                $q->where('dosen_id', $user->id);
            })
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

        $pdf = Pdf::loadView('logbooks.pdf', compact('thesis', 'sessions'));
        
        \App\Models\ActivityLog::log('Export Logbook', "User mengekspor logbook mahasiswa {$thesis->student->name} ke PDF.", 'Logbook');

        return $pdf->download('Logbook_' . str_replace(' ', '_', $thesis->student->name) . '_' . date('Ymd') . '.pdf');
    }
}

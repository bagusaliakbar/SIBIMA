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
            $dosenId = $user->id;
            $filter = $request->input('filter', 'all');

            // Ambil semua data mahasiswa bimbingan dosen untuk perhitungan metrik statistik
            $allDosenTheses = Thesis::where(function ($q) use ($dosenId) {
                    $q->where('pembimbing1_id', $dosenId)->orWhere('pembimbing2_id', $dosenId);
                })
                ->withCount(['mentoringSessions as completed_sessions_count' => function ($q) use ($dosenId) {
                    $q->where('dosen_id', $dosenId)->where('status', 'completed')->where('is_absent', false);
                }])
                ->with(['mentoringSessions' => function ($q) use ($dosenId) {
                    $q->where('dosen_id', $dosenId)
                      ->where('status', 'completed')
                      ->where('is_absent', false)
                      ->orderBy('scheduled_at', 'desc');
                }])
                ->get();

            $countP1 = $allDosenTheses->where('pembimbing1_id', $dosenId)->count();
            $countP2 = $allDosenTheses->where('pembimbing2_id', $dosenId)->count();

            $readyUpIds = [];
            $readySidangIds = [];
            $stalledIds = [];

            foreach ($allDosenTheses as $t) {
                $completedCount = (int) $t->completed_sessions_count;
                $isP1 = ($t->pembimbing1_id === $dosenId);
                $hasAccUp = $isP1 ? (bool) $t->acc_up_p1 : (bool) $t->acc_up_p2;
                $hasAccSidang = $isP1 ? (bool) $t->acc_sidang_p1 : (bool) $t->acc_sidang_p2;

                // 2. Siap Seminar Proposal (UP): >= 4 sesi bimbingan atau sudah ACC UP
                if ($completedCount >= 4 || $hasAccUp) {
                    $readyUpIds[] = $t->id;
                }

                // 3. Siap Sidang Akhir: >= 8 sesi bimbingan atau sudah ACC Sidang
                if ($completedCount >= 8 || $hasAccSidang) {
                    $readySidangIds[] = $t->id;
                }

                // 4. Perlu Perhatian (Pasif / Macet): 
                // Mahasiswa belum pernah bimbingan (0 sesi) ATAU > 14 hari tidak ada aktivitas bimbingan
                if ($t->status !== 'completed' && !$t->isAccSidangFinal()) {
                    if ($completedCount === 0) {
                        $stalledIds[] = $t->id;
                    } else {
                        $lastSession = $t->mentoringSessions->first();
                        if ($lastSession && $lastSession->scheduled_at) {
                            $daysSince = (int) abs(now()->diffInDays($lastSession->scheduled_at));
                            if ($daysSince > 14) {
                                $stalledIds[] = $t->id;
                            }
                        }
                    }
                }
            }

            $stats = [
                'total' => $allDosenTheses->count(),
                'p1' => $countP1,
                'p2' => $countP2,
                'ready_up' => count($readyUpIds),
                'ready_sidang' => count($readySidangIds),
                'stalled' => count($stalledIds),
            ];

            // Query paginasi daftar mahasiswa bimbingan
            $thesesQuery = Thesis::where(function ($q) use ($dosenId) {
                    $q->where('pembimbing1_id', $dosenId)->orWhere('pembimbing2_id', $dosenId);
                })
                ->with([
                    'student',
                    'mentoringSessions' => function ($q) use ($dosenId) {
                        $q->where('dosen_id', $dosenId)
                          ->where('status', 'completed')
                          ->where('is_absent', false)
                          ->orderBy('scheduled_at', 'desc');
                    }
                ])
                ->when($search, function ($query, $search) {
                    $query->where(function ($sq) use ($search) {
                        $sq->whereHas('student', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")->orWhere('identifier', 'like', "%{$search}%");
                        })->orWhere('title', 'like', "%{$search}%");
                    });
                })
                ->withCount(['mentoringSessions as completed_sessions_count' => function ($q) use ($dosenId) {
                    $q->where('dosen_id', $dosenId)->where('status', 'completed')->where('is_absent', false);
                }]);

            if ($filter === 'ready_up') {
                $thesesQuery->whereIn('id', $readyUpIds);
            } elseif ($filter === 'ready_sidang') {
                $thesesQuery->whereIn('id', $readySidangIds);
            } elseif ($filter === 'stalled') {
                $thesesQuery->whereIn('id', $stalledIds);
            }

            $theses = $thesesQuery
                ->paginate(12)
                ->appends([
                    'search' => $search,
                    'filter' => $filter !== 'all' ? $filter : null,
                ]);

            return view('logbooks.dosen_index', compact('theses', 'search', 'stats', 'filter'));
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

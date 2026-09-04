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

        if ($user->role === 'dosen') {
            $dosenId = $user->id;
            $status = $request->input('status', 'active');
            $filter = $request->input('filter', 'all');
            $roleFilter = $request->input('role_filter', 'all');
            $entryYear = $request->input('entry_year');

            // 1. Ambil data mahasiswa bimbingan AKTIF untuk perhitungan Top KPI Cards & status kategori
            $activeTheses = Thesis::where(function ($q) use ($dosenId) {
                    $q->where('pembimbing1_id', $dosenId)->orWhere('pembimbing2_id', $dosenId);
                })
                ->where('status', '!=', 'completed')
                ->where('status', '!=', 'rejected')
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

            // Total mahasiswa yang sudah lulus (untuk counter tab arsip)
            $completedCountTotal = Thesis::where(function ($q) use ($dosenId) {
                    $q->where('pembimbing1_id', $dosenId)->orWhere('pembimbing2_id', $dosenId);
                })
                ->where('status', 'completed')
                ->count();

            $countP1 = $activeTheses->where('pembimbing1_id', $dosenId)->count();
            $countP2 = $activeTheses->where('pembimbing2_id', $dosenId)->count();

            $proposalIds = [];
            $readyUpIds = [];
            $readySidangIds = [];
            $stalledIds = [];

            foreach ($activeTheses as $t) {
                $completedCount = (int) $t->completed_sessions_count;

                // Tahap Proposal (< 4 sesi)
                if ($completedCount < 4) {
                    $proposalIds[] = $t->id;
                }

                // Siap Seminar Proposal (UP): >= 4 sesi bimbingan
                if ($completedCount >= 4) {
                    $readyUpIds[] = $t->id;
                }

                // Siap Sidang Akhir: >= 8 sesi bimbingan
                if ($completedCount >= 8) {
                    $readySidangIds[] = $t->id;
                }

                // Perlu Perhatian (Pasif / Macet): 
                // Mahasiswa belum pernah bimbingan (0 sesi) ATAU > 14 hari tidak ada aktivitas bimbingan
                if ($completedCount === 0) {
                    $stalledIds[] = $t->id;
                } else {
                    $lastSession = $t->mentoringSessions->first();
                    if ($lastSession && $lastSession->scheduled_at) {
                        $daysSince = (int) \Carbon\Carbon::parse($lastSession->scheduled_at)->startOfDay()->diffInDays(now()->startOfDay());
                        if ($daysSince > 14) {
                            $stalledIds[] = $t->id;
                        }
                    }
                }
            }

            $stats = [
                'total' => $activeTheses->count(),
                'p1' => $countP1,
                'p2' => $countP2,
                'proposal' => count($proposalIds),
                'ready_up' => count($readyUpIds),
                'ready_sidang' => count($readySidangIds),
                'stalled' => count($stalledIds),
                'graduated_total' => $completedCountTotal,
            ];

            // Ambil daftar tahun angkatan mahasiswa bimbingan yang tersedia
            $availableEntryYears = Thesis::where(function ($q) use ($dosenId) {
                    $q->where('pembimbing1_id', $dosenId)->orWhere('pembimbing2_id', $dosenId);
                })
                ->whereHas('student', fn($q) => $q->whereNotNull('entry_year'))
                ->with('student')
                ->get()
                ->pluck('student.entry_year')
                ->filter()
                ->unique()
                ->sortDesc()
                ->values();

            if ($availableEntryYears->isEmpty()) {
                $availableEntryYears = User::where('role', 'mahasiswa')
                    ->whereNotNull('entry_year')
                    ->distinct()
                    ->orderBy('entry_year', 'desc')
                    ->pluck('entry_year');
            }

            // 2. Query paginasi daftar mahasiswa bimbingan yang ditampilkan di tabel
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

            // Filter Peran: Pembimbing 1 vs Pembimbing 2
            if ($roleFilter === 'p1') {
                $thesesQuery->where('pembimbing1_id', $dosenId);
            } elseif ($roleFilter === 'p2') {
                $thesesQuery->where('pembimbing2_id', $dosenId);
            }

            // Filter Angkatan Mahasiswa
            if (!empty($entryYear) && $entryYear !== 'all') {
                $thesesQuery->whereHas('student', fn($q) => $q->where('entry_year', $entryYear));
            }

            // Filter Status: default 'active' (hanya mahasiswa yang sedang aktif bimbingan)
            if ($status === 'completed') {
                $thesesQuery->where('status', 'completed');
            } else {
                $thesesQuery->where('status', '!=', 'completed')->where('status', '!=', 'rejected');

                if ($filter === 'proposal') {
                    $thesesQuery->whereIn('id', $proposalIds);
                } elseif ($filter === 'ready_up') {
                    $thesesQuery->whereIn('id', $readyUpIds);
                } elseif ($filter === 'ready_sidang') {
                    $thesesQuery->whereIn('id', $readySidangIds);
                } elseif ($filter === 'stalled') {
                    $thesesQuery->whereIn('id', $stalledIds);
                }
            }

            $theses = $thesesQuery
                ->paginate(12)
                ->appends(array_filter([
                    'search' => $search,
                    'status' => $status !== 'active' ? $status : null,
                    'role_filter' => $roleFilter !== 'all' ? $roleFilter : null,
                    'filter' => $filter !== 'all' ? $filter : null,
                    'entry_year' => $entryYear !== 'all' ? $entryYear : null,
                ]));

            return view('logbooks.dosen_index', compact(
                'theses', 
                'search', 
                'stats', 
                'filter', 
                'status', 
                'roleFilter', 
                'entryYear', 
                'availableEntryYears'
            ));
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

        $sessionsQuery = MentoringSession::where('thesis_id', $thesis->id)
            ->where('status', 'completed')
            ->where('is_absent', false);

        if ($user->role === 'dosen') {
            $sessionsQuery->where('dosen_id', $user->id);
        }

        $totalCompleted = (clone $sessionsQuery)->count();

        $recentSessions = $sessionsQuery
            ->with('dosen:id,name')
            ->orderBy('scheduled_at', 'desc')
            ->limit(5)
            ->get();

        $formattedSessions = $recentSessions->map(function ($session, $index) use ($totalCompleted) {
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
                'dosen_name' => $session->dosen ? $session->dosen->name : '-',
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

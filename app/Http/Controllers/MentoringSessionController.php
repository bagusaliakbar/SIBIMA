<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMentoringSessionRequest;
use App\Http\Requests\UpdateMentoringSessionRequest;
use App\Http\Requests\UpdateMentoringSessionStatusRequest;
use App\Http\Requests\UploadMentoringDocumentRequest;
use App\Models\MentoringSession;
use App\Models\Thesis;
use App\Services\MentoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentoringSessionController extends Controller
{
    protected $mentoringService;

    public function __construct(MentoringService $mentoringService)
    {
        $this->mentoringService = $mentoringService;
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role === 'mahasiswa') {
            $thesis = Thesis::where('student_id', $user->id)->first();
            
            if (!$thesis || $thesis->status !== 'active') {
                return redirect()->route('dashboard')->with('error', 'Anda harus memiliki skripsi aktif untuk mengajukan bimbingan.');
            }

            return view('mentoring.create', compact('thesis'));
        } elseif ($user->role === 'dosen') {
            $theses = Thesis::with('student')
                ->where(function($q) {
                    $q->where('pembimbing1_id', Auth::id())
                      ->orWhere('pembimbing2_id', Auth::id());
                })
                ->where('status', 'active')
                ->get();
            return view('mentoring.create', compact('theses'));
        } elseif ($user->role === 'admin' || $user->role === 'kaprodi') {
            $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
                ->where('status', 'active')
                ->get();
            $dosens = \App\Models\User::whereIn('role', ['dosen', 'kaprodi'])->orderBy('name')->get();
            return view('mentoring.create', compact('theses', 'dosens'));
        }

        abort(403);
    }

    public function store(StoreMentoringSessionRequest $request)
    {
        try {
            $result = $this->mentoringService->storeSession($request->validated());

            if ($result['type'] === 'mass') {
                return redirect()->route('mentoring-sessions.index')
                    ->with('success', "Jadwal bimbingan massal berhasil dibuat untuk {$result['count']} mahasiswa.");
            } elseif ($result['type'] === 'single') {
                return redirect()->route('mentoring-sessions.index')
                    ->with('success', 'Jadwal bimbingan berhasil dibuat.');
            } else {
                return redirect()->route('dashboard')
                    ->with('success', 'Jadwal bimbingan berhasil diajukan dan menunggu persetujuan dosen.');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit(MentoringSession $mentoringSession)
    {
        $this->authorize('update', $mentoringSession);

        $mentoringSession->load(['thesis.student', 'dosen']);

        // Find sibling sessions of the same lecturer at the exact same scheduled time (not completed)
        $relatedSessions = MentoringSession::where('dosen_id', $mentoringSession->dosen_id)
            ->where('scheduled_at', $mentoringSession->scheduled_at)
            ->where('id', '!=', $mentoringSession->id)
            ->where('status', '!=', 'completed')
            ->with('thesis.student')
            ->get();

        return view('mentoring.edit', compact('mentoringSession', 'relatedSessions'));
    }

    public function update(UpdateMentoringSessionRequest $request, MentoringSession $mentoringSession)
    {
        $this->authorize('update', $mentoringSession);

        try {
            $result = $this->mentoringService->updateSession($mentoringSession, $request->validated());

            if (($result['count'] ?? 1) > 1) {
                return redirect()->route('mentoring-sessions.index')
                    ->with('success', "Jadwal bimbingan bersama berhasil diperbarui untuk {$result['count']} mahasiswa dan notifikasi perubahan jadwal telah dikirimkan.");
            }

            return redirect()->route('mentoring-sessions.index')
                ->with('success', 'Jadwal bimbingan berhasil diperbarui dan notifikasi perubahan jadwal telah dikirim ke mahasiswa.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $activeTab = $request->input('tab', 'active');
        $attendanceFilter = $request->input('attendance');

        if ($user->role === 'dosen') {
            $query = MentoringSession::forUser($user)
                ->search($search)
                ->whereHas('thesis', function($q) use ($activeTab) {
                    if ($activeTab === 'history') {
                        $q->where('status', 'completed');
                    } else {
                        $q->where('status', '!=', 'completed');
                    }
                });

            if ($attendanceFilter && in_array($attendanceFilter, ['attending', 'permission', 'pending'])) {
                $query->where('student_attendance_status', $attendanceFilter);
            }

            $sessions = $query
                ->orderBy('scheduled_at', 'desc')
                ->with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'dosen'])
                ->get();

            $calendarQuery = MentoringSession::forUser($user)
                ->whereHas('thesis', function($q) use ($activeTab) {
                    if ($activeTab === 'history') {
                        $q->where('status', 'completed');
                    } else {
                        $q->where('status', '!=', 'completed');
                    }
                });

            if ($attendanceFilter && in_array($attendanceFilter, ['attending', 'permission', 'pending'])) {
                $calendarQuery->where('student_attendance_status', $attendanceFilter);
            }

            $calendarEvents = $calendarQuery
                ->with(['thesis.student', 'dosen'])
                ->get()
                ->map(fn($s) => $this->formatCalendarEvent($s));

            $kpiStats = $this->calculateKpiStats($user);
            $attendanceStats = $this->calculateAttendanceStats($user);

            return view('mentoring.index', compact('sessions', 'search', 'activeTab', 'attendanceFilter', 'calendarEvents', 'kpiStats', 'attendanceStats'));
        } elseif ($user->role === 'mahasiswa') {
            $sessions = MentoringSession::forUser($user)
                ->search($search)
                ->orderBy('scheduled_at', 'desc')
                ->with('thesis.pembimbing1', 'thesis.pembimbing2')
                ->paginate(10)
                ->appends(['search' => $search]);
            return view('mentoring.student_index', compact('sessions', 'search'));
        } elseif ($user->role === 'admin' || $user->role === 'kaprodi') {
            $dosenId = $request->input('dosen_id');

            $query = MentoringSession::search($search)
                ->whereHas('thesis', function($q) use ($activeTab) {
                    if ($activeTab === 'history') {
                        $q->where('status', 'completed');
                    } else {
                        $q->where('status', '!=', 'completed');
                    }
                });

            if ($dosenId) {
                $query->where(function($q) use ($dosenId) {
                    $q->where('dosen_id', $dosenId)
                      ->orWhereHas('thesis', function($t) use ($dosenId) {
                          $t->where('pembimbing1_id', $dosenId)
                            ->orWhere('pembimbing2_id', $dosenId);
                      });
                });
            }

            if ($attendanceFilter && in_array($attendanceFilter, ['attending', 'permission', 'pending'])) {
                $query->where('student_attendance_status', $attendanceFilter);
            }

            $sessions = $query
                ->with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'dosen'])
                ->orderBy('scheduled_at', 'desc')
                ->get();

            $calendarQuery = MentoringSession::query()
                ->whereHas('thesis', function($q) use ($activeTab) {
                    if ($activeTab === 'history') {
                        $q->where('status', 'completed');
                    } else {
                        $q->where('status', '!=', 'completed');
                    }
                });

            if ($dosenId) {
                $calendarQuery->where(function($q) use ($dosenId) {
                    $q->where('dosen_id', $dosenId)
                      ->orWhereHas('thesis', fn($t) => $t->where('pembimbing1_id', $dosenId)->orWhere('pembimbing2_id', $dosenId));
                });
            }

            if ($attendanceFilter && in_array($attendanceFilter, ['attending', 'permission', 'pending'])) {
                $calendarQuery->where('student_attendance_status', $attendanceFilter);
            }

            $calendarEvents = $calendarQuery->with(['thesis.student', 'dosen'])->get()->map(fn($s) => $this->formatCalendarEvent($s));

            $dosens = \App\Models\User::whereIn('role', ['dosen', 'kaprodi'])->orderBy('name')->get();
            $kpiStats = $this->calculateKpiStats($user, $dosenId);
            $attendanceStats = $this->calculateAttendanceStats($user, $dosenId);

            return view('mentoring.index', compact('sessions', 'search', 'activeTab', 'attendanceFilter', 'dosens', 'dosenId', 'calendarEvents', 'kpiStats', 'attendanceStats'));
        }

        abort(403);
    }

    /**
     * Real-time live attendance polling endpoint for lecturers and admins.
     */
    public function liveAttendance(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['dosen', 'admin', 'kaprodi'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $dosenId = $request->input('dosen_id');

        $query = MentoringSession::query()
            ->whereHas('thesis', function($q) {
                $q->where('status', '!=', 'completed');
            })
            ->whereNotIn('status', ['rejected', 'completed']);

        if ($user->role === 'dosen') {
            $query->forUser($user);
        } elseif ($dosenId) {
            $query->where(function($q) use ($dosenId) {
                $q->where('dosen_id', $dosenId)
                  ->orWhereHas('thesis', fn($t) => $t->where('pembimbing1_id', $dosenId)->orWhere('pembimbing2_id', $dosenId));
            });
        }

        $sessions = $query->with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'dosen'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        $attendingCount = $sessions->where('student_attendance_status', 'attending')->count();
        $permissionCount = $sessions->where('student_attendance_status', 'permission')->count();
        $pendingCount = $sessions->where('student_attendance_status', 'pending')->count();

        $items = $sessions->map(function($s) {
            $student = $s->thesis?->student;
            $dosen = $s->dosen ?? $s->thesis?->pembimbing1;
            return [
                'id' => $s->id,
                'session_group_key' => ($s->dosen_id ?? '0') . '_' . $s->scheduled_at->format('Y-m-d_H:i') . '_' . md5($s->topic ?? ''),
                'student_name' => $student?->name ?? 'Mahasiswa',
                'student_identifier' => $student?->identifier ?? '-',
                'student_phone' => $student?->phone_number ?? null,
                'student_avatar' => $student?->avatar_url,
                'thesis_title' => $s->thesis?->title ?? '-',
                'dosen_name' => $dosen?->name ?? '-',
                'topic' => $s->topic,
                'type' => $s->type,
                'location' => $s->location,
                'scheduled_at' => $s->scheduled_at->format('Y-m-d H:i:s'),
                'scheduled_date_formatted' => $s->scheduled_at->locale('id')->translatedFormat('d M Y'),
                'scheduled_time_formatted' => $s->scheduled_at->format('H:i') . ' WIB',
                'is_today' => $s->scheduled_at->isToday(),
                'status' => $s->status,
                'attendance_status' => $s->student_attendance_status ?? 'pending',
                'attendance_reason' => $s->student_attendance_reason,
                'confirmed_at_formatted' => $s->student_confirmed_at ? $s->student_confirmed_at->locale('id')->translatedFormat('d M H:i') . ' WIB' : null,
            ];
        });

        return response()->json([
            'summary' => [
                'total' => $sessions->count(),
                'attending' => $attendingCount,
                'permission' => $permissionCount,
                'pending' => $pendingCount,
                'last_updated' => now()->locale('id')->translatedFormat('H:i:s') . ' WIB',
            ],
            'sessions' => $items,
        ]);
    }

    private function calculateAttendanceStats($user, $dosenId = null): array
    {
        $query = MentoringSession::query()
            ->whereHas('thesis', function($q) {
                $q->where('status', '!=', 'completed');
            })
            ->whereNotIn('status', ['rejected', 'completed']);

        if ($user->role === 'dosen') {
            $query->forUser($user);
        } elseif ($dosenId) {
            $query->where(function($q) use ($dosenId) {
                $q->where('dosen_id', $dosenId)
                  ->orWhereHas('thesis', fn($t) => $t->where('pembimbing1_id', $dosenId)->orWhere('pembimbing2_id', $dosenId));
            });
        }

        $sessions = $query->get();

        return [
            'total' => $sessions->count(),
            'attending' => $sessions->where('student_attendance_status', 'attending')->count(),
            'permission' => $sessions->where('student_attendance_status', 'permission')->count(),
            'pending' => $sessions->where('student_attendance_status', 'pending')->count(),
        ];
    }

    private function calculateKpiStats($user, $dosenId = null): array
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        // 1. Session base query
        $sessionBaseQuery = ($user->role === 'dosen')
            ? MentoringSession::forUser($user)
            : MentoringSession::query();

        if (in_array($user->role, ['admin', 'kaprodi']) && $dosenId) {
            $sessionBaseQuery->where(function($q) use ($dosenId) {
                $q->where('dosen_id', $dosenId)
                  ->orWhereHas('thesis', fn($t) => $t->where('pembimbing1_id', $dosenId)->orWhere('pembimbing2_id', $dosenId));
            });
        }

        // 1. Jadwal Minggu Ini
        $thisWeekCount = (clone $sessionBaseQuery)
            ->whereBetween('scheduled_at', [$startOfWeek, $endOfWeek])
            ->whereIn('status', ['pending', 'approved'])
            ->count();

        // 2. Menunggu Hasil / Catatan Dosen (sesi yang sudah lewat/berjalan tapi belum diselesaikan/feedback kosong)
        $pendingFeedbackCount = (clone $sessionBaseQuery)
            ->where('scheduled_at', '<=', now())
            ->where('status', 'approved')
            ->where(function($q) {
                $q->whereNull('feedback')->orWhere('feedback', '');
            })
            ->count();

        // 3 & 4. Theses base query
        $thesisQuery = Thesis::with(['mentoringSessions'])
            ->where('status', 'active');

        if ($user->role === 'dosen') {
            $thesisQuery->where(function($q) use ($user) {
                $q->where('pembimbing1_id', $user->id)
                  ->orWhere('pembimbing2_id', $user->id);
            });
        } elseif (in_array($user->role, ['admin', 'kaprodi']) && $dosenId) {
            $thesisQuery->where(function($q) use ($dosenId) {
                $q->where('pembimbing1_id', $dosenId)
                  ->orWhere('pembimbing2_id', $dosenId);
            });
        }

        $theses = $thesisQuery->get();

        // 3. Mahasiswa Siap ACC Seminar (>= 4 bimbingan dan belum ACC UP)
        $readyAccSeminarCount = $theses->filter(function($thesis) use ($user, $dosenId) {
            $count = ($user->role === 'dosen')
                ? $thesis->getCompletedMentoringCountForDosen($user->id)
                : ($dosenId ? $thesis->getCompletedMentoringCountForDosen($dosenId) : $thesis->completed_mentoring_count);

            $isAcc = ($user->role === 'dosen')
                ? ($user->id === $thesis->pembimbing1_id ? $thesis->acc_up_p1 : ($user->id === $thesis->pembimbing2_id ? $thesis->acc_up_p2 : false))
                : ($dosenId ? ($dosenId === $thesis->pembimbing1_id ? $thesis->acc_up_p1 : ($dosenId === $thesis->pembimbing2_id ? $thesis->acc_up_p2 : false)) : ($thesis->acc_up_p1 && $thesis->acc_up_p2));

            return $count >= 4 && !$isAcc;
        })->count();

        // 4. Mahasiswa Siap ACC Sidang (>= 8 bimbingan dan belum ACC Sidang)
        $readyAccSidangCount = $theses->filter(function($thesis) use ($user, $dosenId) {
            $count = ($user->role === 'dosen')
                ? $thesis->getCompletedMentoringCountForDosen($user->id)
                : ($dosenId ? $thesis->getCompletedMentoringCountForDosen($dosenId) : $thesis->completed_mentoring_count);

            $isAcc = ($user->role === 'dosen')
                ? ($user->id === $thesis->pembimbing1_id ? $thesis->acc_sidang_p1 : ($user->id === $thesis->pembimbing2_id ? $thesis->acc_sidang_p2 : false))
                : ($dosenId ? ($dosenId === $thesis->pembimbing1_id ? $thesis->acc_sidang_p1 : ($dosenId === $thesis->pembimbing2_id ? $thesis->acc_sidang_p2 : false)) : ($thesis->acc_sidang_p1 && $thesis->acc_sidang_p2));

            return $count >= 8 && !$isAcc;
        })->count();

        return [
            'this_week' => $thisWeekCount,
            'pending_feedback' => $pendingFeedbackCount,
            'ready_acc_seminar' => $readyAccSeminarCount,
            'ready_acc_sidang' => $readyAccSidangCount,
        ];
    }

    private function formatCalendarEvent(MentoringSession $session): array
    {
        $statusColor = match($session->status) {
            'completed' => '#10b981',
            'approved' => '#ea580c',
            'pending' => '#f59e0b',
            'rejected' => '#ef4444',
            default => '#64748b'
        };
        $statusBg = match($session->status) {
            'completed' => '#ecfdf5',
            'approved' => '#fff7ed',
            'pending' => '#fef3c7',
            'rejected' => '#fef2f2',
            default => '#f8fafc'
        };

        return [
            'id' => $session->id,
            'title' => ($session->thesis->student->name ?? 'Mahasiswa') . ' - ' . $session->topic,
            'start' => $session->scheduled_at->toIso8601String(),
            'backgroundColor' => $statusBg,
            'borderColor' => $statusColor,
            'textColor' => $statusColor,
            'extendedProps' => [
                'id' => $session->id,
                'student_name' => $session->thesis->student->name ?? '-',
                'student_npm' => $session->thesis->student->identifier ?? '-',
                'student_avatar' => $session->thesis->student->avatar_url ?? null,
                'topic' => $session->topic,
                'type' => $session->type,
                'location' => $session->location,
                'status' => $session->status,
                'is_absent' => (bool) $session->is_absent,
                'student_attendance_status' => $session->student_attendance_status ?? 'pending',
                'student_attendance_reason' => $session->student_attendance_reason,
                'student_confirmed_at' => $session->student_confirmed_at ? $session->student_confirmed_at->locale('id')->translatedFormat('d M H:i') . ' WIB' : null,
                'notes' => $session->notes,
                'feedback' => $session->feedback,
                'time' => $session->scheduled_at->format('H:i') . ' WIB',
                'date' => $session->scheduled_at->locale('id')->translatedFormat('l, d F Y'),
            ]
        ];
    }

    public function updateStatus(UpdateMentoringSessionStatusRequest $request, MentoringSession $session)
    {
        // Verify that this dosen owns the thesis
        $this->authorize('updateStatus', $session);

        $message = $this->mentoringService->updateStatus($session, $request->validated());

        return redirect()->back()->with('success', $message);
    }

    public function uploadDocument(UploadMentoringDocumentRequest $request, MentoringSession $session)
    {
        // Only the session's student can upload
        $this->authorize('uploadDocument', $session);

        try {
            $this->mentoringService->uploadDocument($session, $request->input('document'));
            return redirect()->back()->with('success', "Link dokumen berhasil ditambahkan.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Gagal mengunggah dokumen: " . $e->getMessage());
        }
    }

    public function deleteDocument(MentoringSession $session)
    {
        // Only the session's student can delete
        $this->authorize('deleteDocument', $session);

        $this->mentoringService->deleteDocument($session);

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function confirmAttendance(Request $request, MentoringSession $session)
    {
        // Only the session's student can confirm
        $this->authorize('confirmAttendance', $session);

        $validated = $request->validate([
            'status' => 'required|in:attending,permission',
            'reason' => 'nullable|required_if:status,permission|string|max:500',
        ], [
            'status.required' => 'Pilihan kehadiran wajib ditentukan.',
            'reason.required_if' => 'Mohon sertakan alasan / keterangan jika Anda berhalangan hadir (izin).',
            'reason.max' => 'Alasan izin maksimal 500 karakter.',
        ]);

        $message = $this->mentoringService->confirmAttendance($session, $validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'status'  => $validated['status'],
                'session_id' => $session->id,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Cancel / Delete a mentoring session.
     */
    public function destroy(Request $request, MentoringSession $mentoringSession)
    {
        $this->authorize('delete', $mentoringSession);

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
            'apply_to_group' => 'nullable',
        ]);

        $validated['apply_to_group'] = $request->boolean('apply_to_group');

        try {
            $result = $this->mentoringService->cancelSession($mentoringSession, $validated);

            if (($result['count'] ?? 1) > 1) {
                return redirect()->route('mentoring-sessions.index')
                    ->with('success', "Jadwal bimbingan bersama untuk {$result['count']} mahasiswa berhasil dibatalkan dan notifikasi telah dikirimkan.");
            }

            return redirect()->back()
                ->with('success', 'Jadwal bimbingan berhasil dibatalkan dan notifikasi pembatalan telah dikirimkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membatalkan jadwal bimbingan: ' . $e->getMessage());
        }
    }
}

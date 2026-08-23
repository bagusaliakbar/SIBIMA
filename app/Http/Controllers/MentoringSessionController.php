<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMentoringSessionRequest;
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

    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $activeTab = $request->input('tab', 'active');

        if ($user->role === 'dosen') {
            $sessions = MentoringSession::forUser($user)
                ->search($search)
                ->whereHas('thesis', function($q) use ($activeTab) {
                    if ($activeTab === 'history') {
                        $q->where('status', 'completed');
                    } else {
                        $q->where('status', '!=', 'completed');
                    }
                })
                ->orderBy('scheduled_at', 'desc')
                ->with('thesis.student')
                ->paginate(12)
                ->appends(['search' => $search, 'tab' => $activeTab]);

            $calendarEvents = MentoringSession::forUser($user)
                ->whereHas('thesis', function($q) use ($activeTab) {
                    if ($activeTab === 'history') {
                        $q->where('status', 'completed');
                    } else {
                        $q->where('status', '!=', 'completed');
                    }
                })
                ->with(['thesis.student', 'dosen'])
                ->get()
                ->map(fn($s) => $this->formatCalendarEvent($s));

            return view('mentoring.index', compact('sessions', 'search', 'activeTab', 'calendarEvents'));
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

            $sessions = $query
                ->with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'dosen'])
                ->orderBy('scheduled_at', 'desc')
                ->paginate(15)
                ->appends(['search' => $search, 'tab' => $activeTab, 'dosen_id' => $dosenId]);

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
            $calendarEvents = $calendarQuery->with(['thesis.student', 'dosen'])->get()->map(fn($s) => $this->formatCalendarEvent($s));

            $dosens = \App\Models\User::whereIn('role', ['dosen', 'kaprodi'])->orderBy('name')->get();

            return view('mentoring.index', compact('sessions', 'search', 'activeTab', 'dosens', 'dosenId', 'calendarEvents'));
        }

        abort(403);
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

        return redirect()->back()->with('success', $message);
    }
}

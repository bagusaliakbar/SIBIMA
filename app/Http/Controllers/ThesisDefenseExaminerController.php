<?php

namespace App\Http\Controllers;

use App\Models\ThesisDefenseScheduleDetail;
use App\Models\ThesisDefenseRevision;
use App\Models\ThesisDefenseRevisionMessage;
use App\Models\Wave;
use App\Services\ExaminerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ThesisDefenseExaminerController extends Controller implements HasMiddleware
{
    protected $examinerService;

    public function __construct(ExaminerService $examinerService)
    {
        $this->examinerService = $examinerService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (!in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi'])) {
                    abort(403);
                }
                return $next($request);
            }),
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $activeWave = Wave::getCurrentActive();
        $hasWaveFilter = $request->filled('wave_id');
        $selectedWaveId = $request->input('wave_id');

        $query = ThesisDefenseScheduleDetail::has('thesis.student')
            ->with(['thesis.student', 'thesis.defenseApplication', 'thesis.defenseApplications', 'thesis.seminarApplication', 'schedule', 'revisions'])
            ->join('thesis_defense_schedules', 'thesis_defense_schedule_details.thesis_defense_schedule_id', '=', 'thesis_defense_schedules.id');

        if ($user->role === 'dosen') {
            $query->where(function ($q) use ($user) {
                $q->where('examiner1_id', $user->id)
                  ->orWhere('examiner2_id', $user->id)
                  ->orWhereHas('thesis', function($t) use ($user) {
                      $t->where('pembimbing1_id', $user->id);
                  });
            });

            if ($hasWaveFilter) {
                // Dosen memilih gelombang secara eksplisit untuk melihat riwayat/arsip gelombang tersebut
                $query->where('thesis_defense_schedules.wave_id', $selectedWaveId);
            } else {
                // Dosen belum memilih gelombang:
                // Jangan tampilkan jadwal yang tanggal pelaksanaannya sudah lewat (date < today).
                // Hanya tampilkan jadwal aktif / mendatang (date >= today).
                $query->where('thesis_defense_schedules.date', '>=', now()->toDateString())
                      ->when($activeWave, function($q) use ($activeWave) {
                          $q->where('thesis_defense_schedules.wave_id', $activeWave->id);
                      });
            }
        } else {
            // Admin & Kaprodi: default to activeWave if no wave is selected
            $selectedWaveId = $request->input('wave_id', $activeWave?->id);
            $query->when($selectedWaveId, function($q) use ($selectedWaveId) {
                $q->where('thesis_defense_schedules.wave_id', $selectedWaveId);
            });
        }

        $search = trim($request->input('search', ''));

        if (!empty($search)) {
            $searchTerm = '%' . $search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('thesis.student', function ($sq) use ($searchTerm) {
                    $sq->where('name', 'like', $searchTerm)
                       ->orWhere('identifier', 'like', $searchTerm);
                })->orWhereHas('thesis', function ($tq) use ($searchTerm) {
                    $tq->where('title', 'like', $searchTerm);
                })->orWhere('thesis_defense_schedules.location', 'like', $searchTerm);
            });
        }

        $dateDirection = $hasWaveFilter ? 'desc' : 'asc';

        $examinations = $query
            ->orderBy('thesis_defense_schedules.date', $dateDirection)
            ->orderBy('thesis_defense_schedule_details.start_time', 'asc')
            ->orderBy('thesis_defense_schedule_details.order', 'asc')
            ->select('thesis_defense_schedule_details.*')
            ->paginate(15)
            ->withQueryString();

        $waves = Wave::orderBy('created_at', 'desc')->get();

        return view('defense-examiner.index', compact('examinations', 'waves', 'selectedWaveId', 'activeWave', 'hasWaveFilter', 'search'));
    }

    public function show(ThesisDefenseScheduleDetail $detail)
    {
        $user = Auth::user();
        $detail->load(['thesis.student', 'thesis.pembimbing1', 'thesis.defenseApplication', 'thesis.defenseApplications', 'thesis.seminarApplication', 'examiner1', 'examiner2', 'schedule', 'revisions.messages.sender']);

        $isAuthorized = in_array($user->role, ['admin', 'kaprodi'])
            || $detail->examiner1_id === $user->id
            || $detail->examiner2_id === $user->id
            || ($detail->thesis && $detail->thesis->pembimbing1_id === $user->id);

        if (!$isAuthorized) {
            abort(403);
        }
        
        $actingId = $user->id;
        if (in_array($user->role, ['admin', 'kaprodi'])) {
            if (request()->has('target_examiner_id')) {
                $actingId = request()->input('target_examiner_id');
            } else {
                $actingId = $detail->examiner1_id ?? $detail->thesis?->pembimbing1_id ?? $user->id;
            }
        }

        $myRevision = $detail->revisions->where('examiner_id', $actingId)->first();
        $targetUser = \App\Models\User::find($actingId);

        $examiners = collect([
            [
                'role_label' => 'Penguji 1',
                'user' => $detail->examiner1,
                'revision' => $detail->revisions->where('examiner_id', $detail->examiner1_id)->first(),
                'color' => 'rose',
            ],
            [
                'role_label' => 'Penguji 2',
                'user' => $detail->examiner2,
                'revision' => $detail->revisions->where('examiner_id', $detail->examiner2_id)->first(),
                'color' => 'rose',
            ],
            [
                'role_label' => 'Pembimbing 1',
                'user' => $detail->thesis?->pembimbing1,
                'revision' => $detail->revisions->where('examiner_id', $detail->thesis?->pembimbing1_id)->first(),
                'color' => 'indigo',
            ],
        ])->filter(fn($item) => $item['user'] !== null);

        return view('defense-examiner.show', compact('detail', 'myRevision', 'actingId', 'targetUser', 'examiners'));
    }

    public function grading(ThesisDefenseScheduleDetail $detail)
    {
        $user = Auth::user();
        $detail->load(['thesis.student', 'thesis.pembimbing1', 'thesis.defenseApplication', 'thesis.defenseApplications', 'thesis.seminarApplication', 'examiner1', 'examiner2', 'schedule', 'revisions']);

        $isAuthorized = in_array($user->role, ['admin', 'kaprodi'])
            || $detail->examiner1_id === $user->id
            || $detail->examiner2_id === $user->id
            || ($detail->thesis && $detail->thesis->pembimbing1_id === $user->id);

        if (!$isAuthorized) {
            abort(403);
        }
        
        $actingId = $user->id;
        if (in_array($user->role, ['admin', 'kaprodi'])) {
            if (request()->has('target_examiner_id')) {
                $actingId = request()->input('target_examiner_id');
            } else {
                $actingId = $detail->examiner1_id ?? $detail->thesis?->pembimbing1_id ?? $user->id;
            }
        }

        $myRevision = ThesisDefenseRevision::where('thesis_defense_schedule_detail_id', $detail->id)
            ->where('examiner_id', $actingId)
            ->first();

        $targetUser = \App\Models\User::find($actingId);

        $examiners = collect([
            [
                'role_label' => 'Pembimbing 1',
                'user' => $detail->thesis?->pembimbing1,
                'revision' => $detail->revisions->where('examiner_id', $detail->thesis?->pembimbing1_id)->first(),
                'color' => 'indigo',
            ],
            [
                'role_label' => 'Penguji 1',
                'user' => $detail->examiner1,
                'revision' => $detail->revisions->where('examiner_id', $detail->examiner1_id)->first(),
                'color' => 'rose',
            ],
            [
                'role_label' => 'Penguji 2',
                'user' => $detail->examiner2,
                'revision' => $detail->revisions->where('examiner_id', $detail->examiner2_id)->first(),
                'color' => 'rose',
            ],
        ])->filter(fn($item) => $item['user'] !== null);

        return view('defense-examiner.grade', compact('detail', 'myRevision', 'actingId', 'targetUser', 'examiners'));
    }

    public function storeGrading(Request $request, ThesisDefenseScheduleDetail $detail)
    {
        $user = Auth::user();
        $detail->load(['thesis.pembimbing1', 'examiner1', 'examiner2']);

        $isAuthorized = in_array($user->role, ['admin', 'kaprodi'])
            || $detail->examiner1_id === $user->id
            || $detail->examiner2_id === $user->id
            || ($detail->thesis && $detail->thesis->pembimbing1_id === $user->id);

        if (!$isAuthorized) {
            abort(403);
        }

        $request->validate([
            'score_presentation' => 'required|integer|min:0|max:100',
            'score_explanation' => 'required|integer|min:0|max:100',
            'score_writing' => 'required|integer|min:0|max:100',
            'target_examiner_id' => 'nullable|exists:users,id',
        ]);

        $actingUser = $user;
        if (in_array($user->role, ['admin', 'kaprodi']) && $request->filled('target_examiner_id')) {
            $target = \App\Models\User::find($request->input('target_examiner_id'));
            if ($target) $actingUser = $target;
        } elseif (in_array($user->role, ['admin', 'kaprodi'])) {
            $actingUser = $detail->examiner1 ?? $detail->thesis?->pembimbing1 ?? $user;
        }

        $this->examinerService->storeGrading(ThesisDefenseRevision::class, $detail, $request->only('score_presentation', 'score_explanation', 'score_writing'), $actingUser);

        if (in_array($user->role, ['admin', 'kaprodi'])) {
            return redirect()->route('defense-examiner.grading', [
                'detail' => $detail->id,
                'target_examiner_id' => $actingUser->id,
                'redirect_to' => $request->input('redirect_to')
            ])->with('success', "Nilai sidang untuk {$actingUser->name} berhasil disimpan.");
        }

        if ($request->input('redirect_to') === 'monitoring') {
            return redirect()->route('monitoring.defense-scores')->with('success', "Nilai sidang untuk {$actingUser->name} berhasil disimpan.");
        }

        return redirect()->route('defense-examiner.index')->with('success', "Nilai sidang untuk {$actingUser->name} berhasil disimpan.");
    }

    public function storeRevision(Request $request, ThesisDefenseScheduleDetail $detail)
    {
        $user = Auth::user();
        $detail->load('thesis');

        $isAuthorized = in_array($user->role, ['admin', 'kaprodi'])
            || $detail->examiner1_id === $user->id
            || $detail->examiner2_id === $user->id
            || ($detail->thesis && $detail->thesis->pembimbing1_id === $user->id);

        if (!$isAuthorized) {
            abort(403);
        }

        if ($request->has('revision_points') && is_array($request->input('revision_points'))) {
            $filteredPoints = array_values(array_filter(array_map('trim', $request->input('revision_points')), fn($p) => $p !== ''));
            if (!empty($filteredPoints)) {
                $compiled = [];
                foreach ($filteredPoints as $idx => $point) {
                    $compiled[] = ($idx + 1) . '. ' . $point;
                }
                if ($request->filled('additional_notes')) {
                    $compiled[] = '';
                    $compiled[] = 'Catatan Tambahan: ' . trim($request->input('additional_notes'));
                }
                $request->merge(['revision_notes' => implode("\n", $compiled)]);
            }
        }

        $request->validate([
            'revision_notes' => 'required|string',
            'revision_link' => 'nullable|url',
        ]);

        $actingUser = $user;
        if (in_array($user->role, ['admin', 'kaprodi']) && $request->filled('target_examiner_id')) {
            $target = \App\Models\User::find($request->input('target_examiner_id'));
            if ($target) $actingUser = $target;
        } elseif (in_array($user->role, ['admin', 'kaprodi'])) {
            $actingUser = $detail->examiner1 ?? $detail->thesis?->pembimbing1 ?? $user;
        }

        try {
            $this->examinerService->storeRevision(
                ThesisDefenseRevision::class, ThesisDefenseRevisionMessage::class, $detail, 
                $request->only('revision_notes'), $request->input('revision_link'),
                $actingUser
            );
            return redirect()->back()->with('success', "Catatan revisi baru atas nama {$actingUser->name} berhasil dikirim.");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengirim catatan revisi: ' . $e->getMessage())->withInput();
        }
    }

    public function approveRevision(ThesisDefenseRevision $revision)
    {
        try {
            $this->examinerService->approveRevision($revision);
            return redirect()->back()->with('success', 'Revisi mahasiswa telah disetujui (FINAL).');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function unapproveRevision(ThesisDefenseRevision $revision)
    {
        try {
            $this->examinerService->unapproveRevision($revision);
            return redirect()->back()->with('success', 'Status selesai berhasil dibatalkan. Revisi kini dibuka kembali.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function approveRevisionDirect(Request $request, ThesisDefenseScheduleDetail $detail)
    {
        $user = Auth::user();
        $detail->load('thesis');

        $isAuthorized = in_array($user->role, ['admin', 'kaprodi'])
            || $detail->examiner1_id === $user->id
            || $detail->examiner2_id === $user->id
            || ($detail->thesis && $detail->thesis->pembimbing1_id === $user->id);

        if (!$isAuthorized) {
            abort(403);
        }

        $actingId = $user->id;
        if (in_array($user->role, ['admin', 'kaprodi'])) {
            $actingId = $request->input('target_examiner_id', $detail->examiner1_id ?? $detail->thesis?->pembimbing1_id ?? $user->id);
        }

        ThesisDefenseRevision::updateOrCreate(
            ['thesis_defense_schedule_detail_id' => $detail->id, 'examiner_id' => $actingId],
            ['status' => 'approved', 'revision_notes' => 'Disetujui tanpa catatan revisi.']
        );

        $this->examinerService->checkGraduation($detail);

        return redirect()->back()->with('success', 'Revisi mahasiswa telah disetujui tanpa catatan.');
    }

    public function exportBeritaAcara(ThesisDefenseScheduleDetail $detail)
    {
        $user = Auth::user();
        $isAuthorized = in_array($user->role, ['admin', 'kaprodi'])
            || $detail->examiner1_id === $user->id
            || $detail->examiner2_id === $user->id
            || ($detail->thesis && $detail->thesis->pembimbing1_id === $user->id);

        if (!$isAuthorized) {
            abort(403);
        }

        $monitoringService = app(\App\Services\MonitoringService::class);
        $scoresData = $monitoringService->calculateDefenseScores($detail);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('monitoring.berita_acara_pdf', array_merge(['detail' => $detail], $scoresData));

        $fileName = 'Berita_Acara_Sidang_' . str_replace(' ', '_', $detail->thesis->student->name) . '.pdf';
        return $pdf->download($fileName);
    }

    public function printRevision(\App\Models\ThesisDefenseRevision $revision)
    {
        $user = Auth::user();
        $revision->load(['examiner', 'detail.thesis.student', 'detail.schedule', 'messages']);

        $isExaminer = $revision->examiner_id === $user->id;
        $isStaff = in_array($user->role, ['admin', 'kaprodi']);
        $isAdvisor = $revision->detail->thesis && ($revision->detail->thesis->pembimbing1_id === $user->id || $revision->detail->thesis->pembimbing2_id === $user->id);
        $isStudent = $revision->detail->thesis && $revision->detail->thesis->student_id === $user->id;

        if (!$isExaminer && !$isStaff && !$isAdvisor && !$isStudent) {
            abort(403);
        }

        $firstMessage = $revision->messages->where('sender_id', $revision->examiner_id)->first()
            ?? $revision->messages->where('sender_id', '!=', $revision->detail->thesis?->student_id)->first()
            ?? $revision->messages->first();

        if (!$firstMessage) {
            return back()->with('error', 'Belum ada catatan atau poin revisi dari penguji.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student-defense-revisions.print', compact('revision', 'firstMessage'));
        return $pdf->stream('Pernyataan_Revisi_Sidang_' . ($revision->detail->thesis->student->identifier ?? 'Mahasiswa') . '.pdf');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SeminarScheduleDetail;
use App\Models\SeminarRevision;
use App\Models\SeminarRevisionMessage;
use App\Models\Wave;
use App\Services\ExaminerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SeminarExaminerController extends Controller implements HasMiddleware
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

        $query = SeminarScheduleDetail::has('thesis.student')
            ->with(['thesis.student', 'schedule', 'revisions'])
            ->join('seminar_schedules', 'seminar_schedule_details.seminar_schedule_id', '=', 'seminar_schedules.id');

        if ($user->role === 'dosen') {
            $query->where(function ($q) use ($user) {
                $q->where('examiner1_id', $user->id)->orWhere('examiner2_id', $user->id);
            });

            if ($hasWaveFilter) {
                // Dosen memilih gelombang secara eksplisit untuk melihat riwayat/arsip gelombang tersebut
                $query->where('seminar_schedules.wave_id', $selectedWaveId);
            } else {
                // Dosen belum memilih gelombang:
                // Jangan tampilkan jadwal yang tanggal pelaksanaannya sudah lewat (date < today).
                // Hanya tampilkan jadwal aktif / mendatang (date >= today).
                $query->where('seminar_schedules.date', '>=', now()->toDateString())
                      ->when($activeWave, function($q) use ($activeWave) {
                          $q->where('seminar_schedules.wave_id', $activeWave->id);
                      });
            }
        } else {
            // Admin & Kaprodi: default to activeWave if no wave is selected
            $selectedWaveId = $request->input('wave_id', $activeWave?->id);
            $query->when($selectedWaveId, function($q) use ($selectedWaveId) {
                $q->where('seminar_schedules.wave_id', $selectedWaveId);
            });
        }

        $examinations = $query
            ->orderBy('seminar_schedules.date', 'desc')
            ->select('seminar_schedule_details.*')
            ->get();

        $waves = Wave::orderBy('created_at', 'desc')->get();

        return view('seminar-examiner.index', compact('examinations', 'waves', 'selectedWaveId', 'activeWave', 'hasWaveFilter'));
    }

    public function show(SeminarScheduleDetail $detail)
    {
        $user = Auth::user();
        $isAuthorized = in_array($user->role, ['admin', 'kaprodi'])
            || $detail->examiner1_id === $user->id
            || $detail->examiner2_id === $user->id;

        if (!$isAuthorized) {
            abort(403);
        }

        $detail->load(['thesis.student', 'schedule', 'revisions.messages.sender']);
        
        $actingId = $user->id;
        if (in_array($user->role, ['admin', 'kaprodi']) && request()->has('target_examiner_id')) {
            $actingId = request()->input('target_examiner_id');
        } elseif (in_array($user->role, ['admin', 'kaprodi'])) {
            $actingId = $detail->examiner1_id ?? $user->id;
        }

        $myRevision = $detail->revisions->where('examiner_id', $actingId)->first();

        return view('seminar-examiner.show', compact('detail', 'myRevision'));
    }

    public function storeRevision(Request $request, SeminarScheduleDetail $detail)
    {
        $user = Auth::user();
        $isAuthorized = in_array($user->role, ['admin', 'kaprodi'])
            || $detail->examiner1_id === $user->id
            || $detail->examiner2_id === $user->id;

        if (!$isAuthorized) {
            abort(403);
        }

        $request->validate([
            'revision_notes' => 'required|string',
            'revision_link' => 'nullable|url',
        ]);

        $actingUser = $user;
        if (in_array($user->role, ['admin', 'kaprodi']) && $request->has('target_examiner_id')) {
            $target = \App\Models\User::find($request->input('target_examiner_id'));
            if ($target) $actingUser = $target;
        } elseif (in_array($user->role, ['admin', 'kaprodi'])) {
            $actingUser = $detail->examiner1 ?? $user;
        }

        try {
            $this->examinerService->storeRevision(
                SeminarRevision::class, SeminarRevisionMessage::class, $detail, 
                $request->only('revision_notes'), $request->input('revision_link'),
                $actingUser
            );
            return redirect()->back()->with('success', 'Catatan revisi baru berhasil dikirim.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengirim catatan revisi: ' . $e->getMessage())->withInput();
        }
    }

    public function approveRevision(SeminarRevision $revision)
    {
        try {
            $this->examinerService->approveRevision($revision);
            return redirect()->back()->with('success', 'Revisi mahasiswa telah disetujui (FINAL).');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function grading(SeminarScheduleDetail $detail)
    {
        $user = Auth::user();
        $isAuthorized = in_array($user->role, ['admin', 'kaprodi'])
            || $detail->examiner1_id === $user->id
            || $detail->examiner2_id === $user->id;

        if (!$isAuthorized) {
            abort(403);
        }

        $detail->load(['thesis.student', 'schedule']);
        
        $actingId = $user->id;
        if (in_array($user->role, ['admin', 'kaprodi']) && request()->has('target_examiner_id')) {
            $actingId = request()->input('target_examiner_id');
        } elseif (in_array($user->role, ['admin', 'kaprodi'])) {
            $actingId = $detail->examiner1_id ?? $user->id;
        }

        $myRevision = SeminarRevision::where('seminar_schedule_detail_id', $detail->id)
            ->where('examiner_id', $actingId)
            ->first();

        return view('seminar-examiner.grade', compact('detail', 'myRevision'));
    }

    public function storeGrading(Request $request, SeminarScheduleDetail $detail)
    {
        $user = Auth::user();
        $isAuthorized = in_array($user->role, ['admin', 'kaprodi'])
            || $detail->examiner1_id === $user->id
            || $detail->examiner2_id === $user->id;

        if (!$isAuthorized) {
            abort(403);
        }

        $request->validate([
            'score_presentation' => 'required|integer|min:0|max:100',
            'score_explanation' => 'required|integer|min:0|max:100',
            'score_writing' => 'required|integer|min:0|max:100',
        ]);

        $actingUser = $user;
        if (in_array($user->role, ['admin', 'kaprodi']) && $request->has('target_examiner_id')) {
            $target = \App\Models\User::find($request->input('target_examiner_id'));
            if ($target) $actingUser = $target;
        } elseif (in_array($user->role, ['admin', 'kaprodi'])) {
            $actingUser = $detail->examiner1 ?? $user;
        }

        $this->examinerService->storeGrading(SeminarRevision::class, $detail, $request->only('score_presentation', 'score_explanation', 'score_writing'), $actingUser);

        return redirect()->route('seminar-examiner.index')->with('success', 'Nilai seminar berhasil disimpan.');
    }

    public function exportBeritaAcara(SeminarScheduleDetail $detail)
    {
        $user = Auth::user();
        $isAuthorized = in_array($user->role, ['admin', 'kaprodi'])
            || $detail->examiner1_id === $user->id
            || $detail->examiner2_id === $user->id;

        if (!$isAuthorized) {
            abort(403);
        }

        $monitoringService = app(\App\Services\MonitoringService::class);
        $scoresData = $monitoringService->calculateSeminarScores($detail);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('monitoring.berita_acara_seminar_pdf', array_merge(['detail' => $detail], $scoresData));

        $fileName = 'Berita_Acara_Seminar_' . str_replace(' ', '_', $detail->thesis->student->name) . '.pdf';
        return $pdf->download($fileName);
    }
}


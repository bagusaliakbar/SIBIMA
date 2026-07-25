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
                if (Auth::user()->role !== 'dosen') {
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
        $selectedWaveId = $request->input('wave_id', $activeWave?->id);

        $examinations = ThesisDefenseScheduleDetail::with(['thesis.student', 'schedule', 'revisions' => function($q) use ($user) {
                $q->where('examiner_id', $user->id);
            }])
            ->where(function ($q) use ($user) {
                $q->where('examiner1_id', $user->id)
                  ->orWhere('examiner2_id', $user->id)
                  ->orWhereHas('thesis', function($t) use ($user) {
                      $t->where('pembimbing1_id', $user->id);
                  });
            })
            ->when($selectedWaveId, function($q) use ($selectedWaveId) {
                $q->whereHas('schedule', function($query) use ($selectedWaveId) {
                    $query->where('wave_id', $selectedWaveId);
                });
            })
            ->join('thesis_defense_schedules', 'thesis_defense_schedule_details.thesis_defense_schedule_id', '=', 'thesis_defense_schedules.id')
            ->orderBy('thesis_defense_schedules.date', 'desc')
            ->select('thesis_defense_schedule_details.*')
            ->get();

        $waves = Wave::orderBy('created_at', 'desc')->get();

        return view('defense-examiner.index', compact('examinations', 'waves', 'selectedWaveId', 'activeWave'));
    }

    public function show(ThesisDefenseScheduleDetail $detail)
    {
        $user = Auth::user();
        $detail->load(['thesis.student', 'schedule', 'revisions.messages.sender']);

        if ($detail->examiner1_id !== $user->id && $detail->examiner2_id !== $user->id && $detail->thesis->pembimbing1_id !== $user->id) {
            abort(403);
        }
        
        $myRevision = $detail->revisions->where('examiner_id', $user->id)->first();

        return view('defense-examiner.show', compact('detail', 'myRevision'));
    }

    public function grading(ThesisDefenseScheduleDetail $detail)
    {
        $user = Auth::user();
        $detail->load(['thesis.student', 'schedule']);

        if ($detail->examiner1_id !== $user->id && $detail->examiner2_id !== $user->id && $detail->thesis->pembimbing1_id !== $user->id) {
            abort(403);
        }
        
        $myRevision = ThesisDefenseRevision::where('thesis_defense_schedule_detail_id', $detail->id)
            ->where('examiner_id', $user->id)
            ->first();

        return view('defense-examiner.grade', compact('detail', 'myRevision'));
    }

    public function storeGrading(Request $request, ThesisDefenseScheduleDetail $detail)
    {
        $user = Auth::user();
        $detail->load('thesis');

        if ($detail->examiner1_id !== $user->id && $detail->examiner2_id !== $user->id && $detail->thesis->pembimbing1_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'score_presentation' => 'required|integer|min:0|max:100',
            'score_explanation' => 'required|integer|min:0|max:100',
            'score_writing' => 'required|integer|min:0|max:100',
        ]);

        $this->examinerService->storeGrading(ThesisDefenseRevision::class, $detail, $request->only('score_presentation', 'score_explanation', 'score_writing'), $user);

        return redirect()->route('defense-examiner.index')->with('success', 'Nilai sidang berhasil disimpan.');
    }

    public function storeRevision(Request $request, ThesisDefenseScheduleDetail $detail)
    {
        $user = Auth::user();
        $detail->load('thesis');

        if ($detail->examiner1_id !== $user->id && $detail->examiner2_id !== $user->id && $detail->thesis->pembimbing1_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'revision_notes' => 'required|string',
            'revision_link' => 'nullable|url',
        ]);

        try {
            $this->examinerService->storeRevision(
                ThesisDefenseRevision::class, ThesisDefenseRevisionMessage::class, $detail, 
                $request->only('revision_notes'), $request->input('revision_link')
            );
            return redirect()->back()->with('success', 'Catatan revisi baru berhasil dikirim.');
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

    public function approveRevisionDirect(Request $request, ThesisDefenseScheduleDetail $detail)
    {
        $user = Auth::user();
        $detail->load('thesis');

        if ($detail->examiner1_id !== $user->id && $detail->examiner2_id !== $user->id && $detail->thesis->pembimbing1_id !== $user->id) {
            abort(403);
        }

        ThesisDefenseRevision::updateOrCreate(
            ['thesis_defense_schedule_detail_id' => $detail->id, 'examiner_id' => $user->id],
            ['status' => 'approved', 'revision_notes' => 'Disetujui tanpa catatan revisi.']
        );

        $this->examinerService->checkGraduation($detail);

        return redirect()->back()->with('success', 'Revisi mahasiswa telah disetujui tanpa catatan.');
    }

    public function exportBeritaAcara(ThesisDefenseScheduleDetail $detail)
    {
        $user = Auth::user();
        if ($detail->examiner1_id !== $user->id && $detail->examiner2_id !== $user->id && $detail->thesis->pembimbing1_id !== $user->id) {
            abort(403);
        }

        $monitoringService = app(\App\Services\MonitoringService::class);
        $scoresData = $monitoringService->calculateDefenseScores($detail);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('monitoring.berita_acara_pdf', array_merge(['detail' => $detail], $scoresData));

        $fileName = 'Berita_Acara_Sidang_' . str_replace(' ', '_', $detail->thesis->student->name) . '.pdf';
        return $pdf->download($fileName);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ThesisDefenseScheduleDetail;
use App\Models\ThesisDefenseRevision;
use App\Models\ThesisDefenseRevisionMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\Wave;

class ThesisDefenseExaminerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'dosen') {
            abort(403);
        }

        $activeWave = Wave::active() ?: Wave::where('is_active', true)->latest()->first() ?: Wave::latest()->first();
        $selectedWaveId = $request->get('wave_id', $activeWave?->id);

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

        if ($detail->examiner1_id !== $user->id && 
            $detail->examiner2_id !== $user->id && 
            $detail->thesis->pembimbing1_id !== $user->id) {
            abort(403);
        }
        
        // Get existing revision by this user (could be examiner or supervisor)
        $myRevision = $detail->revisions->where('examiner_id', $user->id)->first();

        return view('defense-examiner.show', compact('detail', 'myRevision'));
    }

    public function grading(ThesisDefenseScheduleDetail $detail)
    {
        $user = Auth::user();
        $detail->load(['thesis.student', 'schedule']);

        if ($detail->examiner1_id !== $user->id && 
            $detail->examiner2_id !== $user->id && 
            $detail->thesis->pembimbing1_id !== $user->id) {
            abort(403);
        }
        
        // Get existing revision (where scores are stored)
        $myRevision = ThesisDefenseRevision::where('thesis_defense_schedule_detail_id', $detail->id)
            ->where('examiner_id', $user->id)
            ->first();

        return view('defense-examiner.grade', compact('detail', 'myRevision'));
    }

    public function storeGrading(Request $request, ThesisDefenseScheduleDetail $detail)
    {
        $user = Auth::user();
        $detail->load('thesis');

        if ($detail->examiner1_id !== $user->id && 
            $detail->examiner2_id !== $user->id && 
            $detail->thesis->pembimbing1_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'score_presentation' => 'required|integer|min:0|max:100',
            'score_explanation' => 'required|integer|min:0|max:100',
            'score_writing' => 'required|integer|min:0|max:100',
        ]);

        $isSupervisorOnly = ($detail->thesis->pembimbing1_id === $user->id && 
                             $detail->examiner1_id !== $user->id && 
                             $detail->examiner2_id !== $user->id);

        $revision = ThesisDefenseRevision::updateOrCreate(
            [
                'thesis_defense_schedule_detail_id' => $detail->id,
                'examiner_id' => $user->id,
            ],
            [
                'score_presentation' => $request->score_presentation,
                'score_explanation' => $request->score_explanation,
                'score_writing' => $request->score_writing,
                'status' => ($detail->thesis->pembimbing1_id === $user->id) ? 'approved' : 'completed'
            ]
        );

        // Check if all revisions are now approved (e.g. if this was the last approval needed)
        if ($detail->isRevisionAllApproved()) {
            $thesis = $detail->thesis;
            $thesis->update(['status' => 'completed']);
            
            // Notify Student
            $thesis->student->notify(new \App\Notifications\GeneralNotification(
                'Selamat! Anda Lulus',
                "Seluruh revisi sidang Anda telah disetujui. Anda dinyatakan LULUS.",
                route('dashboard'),
                'success'
            ));
        }

        return redirect()->route('defense-examiner.index')->with('success', 'Nilai sidang berhasil disimpan.');
    }

    public function storeRevision(Request $request, ThesisDefenseScheduleDetail $detail)
    {
        $user = Auth::user();
        $detail->load('thesis');

        if ($detail->examiner1_id !== $user->id && 
            $detail->examiner2_id !== $user->id && 
            $detail->thesis->pembimbing1_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'revision_notes' => 'required|string',
            'revision_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $revision = ThesisDefenseRevision::firstOrCreate(
            [
                'thesis_defense_schedule_detail_id' => $detail->id,
                'examiner_id' => $user->id,
            ],
            ['status' => 'completed']
        );

        $revision->update([
            'status' => 'completed',
        ]);

        $message = ThesisDefenseRevisionMessage::create([
            'thesis_defense_revision_id' => $revision->id,
            'sender_id' => $user->id,
            'message' => $request->revision_notes,
        ]);

        if ($request->hasFile('revision_file')) {
            $path = $request->file('revision_file')->store('defense-revisions', 'public');
            $message->update(['file_path' => $path]);
        }

        return redirect()->back()->with('success', 'Catatan revisi baru berhasil dikirim.');
    }

    public function approveRevision(ThesisDefenseRevision $revision)
    {
        $user = Auth::user();
        if ($revision->examiner_id !== $user->id) {
            abort(403);
        }

        $revision->update(['status' => 'approved']);

        // Check if all revisions are now approved
        $detail = $revision->detail;
        if ($detail->isRevisionAllApproved()) {
            $thesis = $detail->thesis;
            $thesis->update(['status' => 'completed']);
            
            // Notify Student
            $thesis->student->notify(new \App\Notifications\GeneralNotification(
                'Selamat! Anda Lulus',
                "Seluruh revisi sidang Anda telah disetujui. Anda dinyatakan LULUS.",
                route('dashboard'),
                'success'
            ));
        }

        return redirect()->back()->with('success', 'Revisi mahasiswa telah disetujui (FINAL).');
    }

    public function approveRevisionDirect(Request $request, ThesisDefenseScheduleDetail $detail)
    {
        $user = Auth::user();
        $detail->load('thesis');

        if ($detail->examiner1_id !== $user->id && 
            $detail->examiner2_id !== $user->id && 
            $detail->thesis->pembimbing1_id !== $user->id) {
            abort(403);
        }

        $revision = ThesisDefenseRevision::updateOrCreate(
            [
                'thesis_defense_schedule_detail_id' => $detail->id,
                'examiner_id' => $user->id,
            ],
            [
                'status' => 'approved',
                'revision_notes' => 'Disetujui tanpa catatan revisi.'
            ]
        );

        // Check if all revisions are now approved
        if ($detail->isRevisionAllApproved()) {
            $thesis = $detail->thesis;
            $thesis->update(['status' => 'completed']);
            
            // Notify Student
            $thesis->student->notify(new \App\Notifications\GeneralNotification(
                'Selamat! Anda Lulus',
                "Seluruh revisi sidang Anda telah disetujui. Anda dinyatakan LULUS.",
                route('dashboard'),
                'success'
            ));
        }

        return redirect()->back()->with('success', 'Revisi mahasiswa telah disetujui tanpa catatan.');
    }
}

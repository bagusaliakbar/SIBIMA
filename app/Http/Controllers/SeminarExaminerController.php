<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SeminarScheduleDetail;
use App\Models\SeminarRevision;
use App\Models\SeminarRevisionMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\Wave;

class SeminarExaminerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'dosen') {
            abort(403);
        }

        $activeWave = Wave::active() ?: Wave::where('is_active', true)->latest()->first() ?: Wave::latest()->first();
        $selectedWaveId = $request->input('wave_id', $activeWave?->id);

        $examinations = SeminarScheduleDetail::with(['thesis.student', 'schedule', 'revisions' => function($q) use ($user) {
                $q->where('examiner_id', $user->id);
            }])
            ->where(function ($q) use ($user) {
                $q->where('examiner1_id', $user->id)
                  ->orWhere('examiner2_id', $user->id);
            })
            ->when($selectedWaveId, function($q) use ($selectedWaveId) {
                $q->whereHas('schedule', function($query) use ($selectedWaveId) {
                    $query->where('wave_id', $selectedWaveId);
                });
            })
            ->join('seminar_schedules', 'seminar_schedule_details.seminar_schedule_id', '=', 'seminar_schedules.id')
            ->orderBy('seminar_schedules.date', 'desc')
            ->select('seminar_schedule_details.*')
            ->get();

        $waves = Wave::orderBy('created_at', 'desc')->get();

        return view('seminar-examiner.index', compact('examinations', 'waves', 'selectedWaveId', 'activeWave'));
    }

    public function show(SeminarScheduleDetail $detail)
    {
        $user = Auth::user();
        if ($detail->examiner1_id !== $user->id && $detail->examiner2_id !== $user->id) {
            abort(403);
        }

        $detail->load(['thesis.student', 'schedule', 'revisions.messages.sender']);
        
        // Get existing revision by this examiner if any
        $myRevision = $detail->revisions->where('examiner_id', $user->id)->first();

        return view('seminar-examiner.show', compact('detail', 'myRevision'));
    }

    public function storeRevision(Request $request, SeminarScheduleDetail $detail)
    {
        $user = Auth::user();
        if ($detail->examiner1_id !== $user->id && $detail->examiner2_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'revision_notes' => 'required|string',
            'revision_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $revision = SeminarRevision::firstOrCreate(
            [
                'seminar_schedule_detail_id' => $detail->id,
                'examiner_id' => $user->id,
            ],
            ['status' => 'completed']
        );

        $revision->update(['status' => 'completed']);

        $message = SeminarRevisionMessage::create([
            'seminar_revision_id' => $revision->id,
            'sender_id' => $user->id,
            'message' => $request->revision_notes,
        ]);

        if ($request->hasFile('revision_file')) {
            $path = $request->file('revision_file')->store('seminar-revisions', 'public');
            $message->update(['file_path' => $path]);
        }

        return redirect()->back()->with('success', 'Catatan revisi baru berhasil dikirim.');
    }

    public function approveRevision(SeminarRevision $revision)
    {
        $user = Auth::user();
        if ($revision->examiner_id !== $user->id) {
            abort(403);
        }

        $revision->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Revisi mahasiswa telah disetujui (FINAL).');
    }
}

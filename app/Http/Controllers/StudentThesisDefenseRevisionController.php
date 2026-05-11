<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ThesisDefenseRevision;
use App\Models\ThesisDefenseRevisionMessage;
use App\Models\Thesis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentThesisDefenseRevisionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role !== 'mahasiswa') {
            abort(403);
        }

        $thesis = Thesis::where('student_id', $user->id)->first();
        if (!$thesis) {
            return view('student-defense-revisions.index', ['revisions' => collect()]);
        }

        $revisions = ThesisDefenseRevision::whereHas('detail', function ($q) use ($thesis) {
            $q->where('thesis_id', $thesis->id);
        })->with(['examiner', 'detail.schedule', 'messages' => function($q) {
            $q->latest()->limit(1);
        }])->get();

        return view('student-defense-revisions.index', compact('revisions'));
    }

    public function show(ThesisDefenseRevision $revision)
    {
        $user = Auth::user();
        $revision->load(['examiner', 'detail.thesis', 'detail.schedule', 'messages.sender']);

        if ($revision->detail->thesis->student_id !== $user->id) {
            abort(403);
        }

        return view('student-defense-revisions.show', compact('revision'));
    }

    public function storeReply(Request $request, ThesisDefenseRevision $revision)
    {
        $user = Auth::user();
        if ($revision->detail->thesis->student_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'student_notes' => 'required|string',
            'student_file' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:10240',
        ]);

        $revision->update([
            'resubmitted_at' => now(),
            'status' => 'resubmitted',
        ]);

        $message = ThesisDefenseRevisionMessage::create([
            'thesis_defense_revision_id' => $revision->id,
            'sender_id' => $user->id,
            'message' => $request->student_notes,
        ]);

        if ($request->hasFile('student_file')) {
            $path = $request->file('student_file')->store('defense-revisions/replies', 'public');
            $message->update(['file_path' => $path]);
        }

        return redirect()->back()->with('success', 'Follow-up revisi berhasil dikirim.');
    }

    public function printPdf(ThesisDefenseRevision $revision)
    {
        $user = Auth::user();
        $revision->load(['examiner', 'detail.thesis.student', 'detail.schedule', 'messages']);

        // Only student or examiner can print
        if ($revision->detail->thesis->student_id !== $user->id && $revision->examiner_id !== $user->id) {
            abort(403);
        }

        $firstMessage = $revision->messages->where('sender_id', $revision->examiner_id)->first();
        
        if (!$firstMessage) {
            return redirect()->back()->with('error', 'Belum ada catatan revisi dari penguji.');
        }

        $pdf = Pdf::loadView('student-defense-revisions.print', compact('revision', 'firstMessage'));
        
        return $pdf->stream('Pernyataan_Revisi_Sidang_' . $revision->detail->thesis->student->identifier . '.pdf');
    }
}

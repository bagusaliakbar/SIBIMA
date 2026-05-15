<?php

namespace App\Http\Controllers;

use App\Models\SeminarRevision;
use App\Models\SeminarRevisionMessage;
use App\Models\Thesis;
use App\Services\RevisionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class StudentSeminarRevisionController extends Controller implements HasMiddleware
{
    protected $revisionService;

    public function __construct(RevisionService $revisionService)
    {
        $this->revisionService = $revisionService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (Auth::user()->role !== 'mahasiswa' && !in_array($request->route()->getName(), ['student-seminar-revisions.print-pdf'])) {
                    abort(403);
                }
                return $next($request);
            }),
        ];
    }

    public function index()
    {
        $user = Auth::user();
        $thesis = Thesis::where('student_id', $user->id)->first();
        if (!$thesis) {
            return view('student-seminar-revisions.index', ['revisions' => collect()]);
        }

        $revisions = SeminarRevision::whereHas('detail', function ($q) use ($thesis) {
            $q->where('thesis_id', $thesis->id);
        })->with(['examiner', 'detail.schedule', 'messages' => function($q) {
            $q->latest()->limit(1);
        }])->get();

        return view('student-seminar-revisions.index', compact('revisions'));
    }

    public function show(SeminarRevision $revision)
    {
        $user = Auth::user();
        $revision->load(['examiner', 'detail.thesis', 'detail.schedule', 'messages.sender']);

        if ($revision->detail->thesis->student_id !== $user->id) abort(403);

        return view('student-seminar-revisions.show', compact('revision'));
    }

    public function storeReply(Request $request, SeminarRevision $revision)
    {
        $user = Auth::user();
        if ($revision->detail->thesis->student_id !== $user->id) abort(403);

        $request->validate([
            'student_notes' => 'required|string',
            'student_file' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:10240',
        ]);

        $this->revisionService->storeReply($revision, SeminarRevisionMessage::class, $request->only('student_notes'), $request->file('student_file'));

        return redirect()->back()->with('success', 'Follow-up revisi berhasil dikirim.');
    }

    public function printPdf(SeminarRevision $revision)
    {
        $user = Auth::user();
        $revision->load(['examiner', 'detail.thesis.student', 'detail.schedule', 'messages']);

        if ($revision->detail->thesis->student_id !== $user->id && $revision->examiner_id !== $user->id) abort(403);

        $firstMessage = $revision->messages->where('sender_id', $revision->examiner_id)->first();
        if (!$firstMessage) return back()->with('error', 'Belum ada catatan revisi dari penguji.');

        $pdf = Pdf::loadView('student-seminar-revisions.print', compact('revision', 'firstMessage'));
        return $pdf->stream('Pernyataan_Revisi_' . $revision->detail->thesis->student->identifier . '.pdf');
    }
}

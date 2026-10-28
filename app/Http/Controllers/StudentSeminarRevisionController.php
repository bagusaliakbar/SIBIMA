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
                    if (Auth::user()->role === 'dosen') {
                        return redirect()->route('seminar-examiner.index')->with('info', 'Anda dialihkan ke halaman penilaian & revisi seminar penguji.');
                    }
                    abort(403);
                }
                return $next($request);
            }),
        ];
    }

    public function index()
    {
        $user = Auth::user();
        $thesis = Thesis::where('student_id', $user->id)
            ->with(['pembimbing1', 'pembimbing2'])
            ->first();

        if (!$thesis) {
            return view('student-seminar-revisions.index', [
                'revisions' => collect(),
                'thesis' => null,
                'scheduleDetail' => null,
                'examiners' => collect(),
            ]);
        }

        $scheduleDetail = \App\Models\SeminarScheduleDetail::where('thesis_id', $thesis->id)
            ->with(['schedule', 'examiner1', 'examiner2'])
            ->latest()
            ->first();

        $revisions = SeminarRevision::whereHas('detail', function ($q) use ($thesis) {
            $q->where('thesis_id', $thesis->id);
        })->with(['examiner', 'detail.schedule', 'detail.thesis', 'messages' => function($q) {
            $q->latest()->limit(1);
        }])->get();

        // Build complete list of examiners (including those who have not yet submitted revisions)
        $examiners = collect();
        if ($scheduleDetail) {
            if ($scheduleDetail->examiner1) {
                $examiners->push([
                    'role_label' => 'Penguji 1',
                    'user' => $scheduleDetail->examiner1,
                    'revision' => $revisions->where('examiner_id', $scheduleDetail->examiner1_id)->first(),
                ]);
            }
            if ($scheduleDetail->examiner2) {
                $examiners->push([
                    'role_label' => 'Penguji 2',
                    'user' => $scheduleDetail->examiner2,
                    'revision' => $revisions->where('examiner_id', $scheduleDetail->examiner2_id)->first(),
                ]);
            }
        }

        // Include any additional examiners with existing revision records
        foreach ($revisions as $rev) {
            if ($rev->examiner && !$examiners->contains('user.id', $rev->examiner_id)) {
                $examiners->push([
                    'role_label' => 'Dosen Penguji',
                    'user' => $rev->examiner,
                    'revision' => $rev,
                ]);
            }
        }

        return view('student-seminar-revisions.index', compact('revisions', 'thesis', 'scheduleDetail', 'examiners'));
    }

    public function show(SeminarRevision $revision)
    {
        $user = Auth::user();
        $revision->load([
            'examiner',
            'detail.thesis.student',
            'detail.thesis.pembimbing1',
            'detail.thesis.pembimbing2',
            'detail.thesis.seminarApplication',
            'detail.schedule',
            'messages.sender'
        ]);

        if ($revision->detail->thesis->student_id !== $user->id) abort(403);

        // Mark unread examiner messages as read by student
        $revision->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Load all examiner revisions for this seminar schedule detail so student can switch channel tabs seamlessly
        $allRevisions = SeminarRevision::where('seminar_schedule_detail_id', $revision->seminar_schedule_detail_id)
            ->with(['examiner', 'messages' => fn($q) => $q->latest()])
            ->get();

        // Determine default PDF document URL for left column preview
        $latestMessageFile = $revision->messages->whereNotNull('file_path')->last()?->file_path;
        $defaultPdfUrl = $latestMessageFile
            ?: ($revision->revision_file
            ?: ($revision->student_file
            ?: ($revision->detail->thesis?->seminarApplication?->file_skripsi ?? null)));

        return view('student-seminar-revisions.show', compact('revision', 'allRevisions', 'defaultPdfUrl'));
    }

    public function storeReply(Request $request, SeminarRevision $revision)
    {
        $user = Auth::user();
        if ($revision->detail->thesis->student_id !== $user->id) abort(403);

        $request->validate([
            'student_notes' => 'required|string',
            'student_link' => 'nullable|url',
        ]);

        $this->revisionService->storeReply($revision, SeminarRevisionMessage::class, $request->only('student_notes'), $request->input('student_link'));

        return redirect()->back()->with('success', 'Follow-up revisi berhasil dikirim.');
    }

    public function printPdf(SeminarRevision $revision)
    {
        $user = Auth::user();
        $revision->load(['examiner', 'detail.thesis.student', 'detail.schedule', 'messages']);

        $isStudent = $revision->detail->thesis->student_id === $user->id;
        $isExaminer = $revision->examiner_id === $user->id;
        $isStaff = in_array($user->role, ['admin', 'kaprodi']);
        $isAdvisor = $revision->detail->thesis && ($revision->detail->thesis->pembimbing1_id === $user->id || $revision->detail->thesis->pembimbing2_id === $user->id);

        if (!$isStudent && !$isExaminer && !$isStaff && !$isAdvisor) abort(403);

        $firstMessage = $revision->messages->where('sender_id', $revision->examiner_id)->first();
        if (!$firstMessage) return back()->with('error', 'Belum ada catatan revisi dari penguji.');

        $pdf = Pdf::loadView('student-seminar-revisions.print', compact('revision', 'firstMessage'));
        return $pdf->stream('Pernyataan_Revisi_' . $revision->detail->thesis->student->identifier . '.pdf');
    }
}

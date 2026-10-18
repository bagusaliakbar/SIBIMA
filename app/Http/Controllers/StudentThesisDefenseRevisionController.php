<?php

namespace App\Http\Controllers;

use App\Models\ThesisDefenseRevision;
use App\Models\ThesisDefenseRevisionMessage;
use App\Models\Thesis;
use App\Services\RevisionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class StudentThesisDefenseRevisionController extends Controller implements HasMiddleware
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
                if (Auth::user()->role !== 'mahasiswa' && !in_array($request->route()->getName(), ['student-defense-revisions.print-pdf'])) {
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
            return view('student-defense-revisions.index', [
                'revisions' => collect(),
                'thesis' => null,
                'scheduleDetail' => null,
                'examiners' => collect(),
            ]);
        }

        $scheduleDetail = \App\Models\ThesisDefenseScheduleDetail::where('thesis_id', $thesis->id)
            ->with(['schedule', 'examiner1', 'examiner2'])
            ->latest()
            ->first();

        $revisions = ThesisDefenseRevision::whereHas('detail', function ($q) use ($thesis) {
            $q->where('thesis_id', $thesis->id);
        })->with(['examiner', 'detail.schedule', 'detail.thesis', 'messages' => function($q) {
            $q->latest()->limit(1);
        }])->get();

        // Build complete list of examiners/reviewers for thesis defense
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
            if ($thesis && $thesis->pembimbing1 && $thesis->pembimbing1_id !== $scheduleDetail->examiner1_id && $thesis->pembimbing1_id !== $scheduleDetail->examiner2_id) {
                $examiners->push([
                    'role_label' => 'Pembimbing 1',
                    'user' => $thesis->pembimbing1,
                    'revision' => $revisions->where('examiner_id', $thesis->pembimbing1_id)->first(),
                ]);
            }
        }

        // Include any additional examiners with existing revision records
        foreach ($revisions as $rev) {
            if ($rev->examiner && !$examiners->contains('user.id', $rev->examiner_id)) {
                $roleLabel = 'Dosen Penguji';
                if ($thesis && $rev->examiner_id === $thesis->pembimbing2_id) {
                    $roleLabel = 'Pembimbing 2';
                }
                $examiners->push([
                    'role_label' => $roleLabel,
                    'user' => $rev->examiner,
                    'revision' => $rev,
                ]);
            }
        }

        return view('student-defense-revisions.index', compact('revisions', 'thesis', 'scheduleDetail', 'examiners'));
    }

    public function show(ThesisDefenseRevision $revision)
    {
        $user = Auth::user();
        $revision->load([
            'examiner',
            'detail.thesis.student',
            'detail.thesis.pembimbing1',
            'detail.thesis.pembimbing2',
            'detail.thesis.defenseApplication',
            'detail.schedule',
            'messages.sender'
        ]);

        if ($revision->detail->thesis->student_id !== $user->id) abort(403);

        // Mark unread examiner messages as read by student
        $revision->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Load all examiner revisions for this schedule detail so student can switch channel tabs seamlessly
        $allRevisions = ThesisDefenseRevision::where('thesis_defense_schedule_detail_id', $revision->thesis_defense_schedule_detail_id)
            ->with(['examiner', 'messages' => fn($q) => $q->latest()])
            ->get();

        // Determine default PDF document URL for left column preview
        $latestMessageFile = $revision->messages->whereNotNull('file_path')->last()?->file_path;
        $defaultPdfUrl = $latestMessageFile
            ?: ($revision->revision_file
            ?: ($revision->student_file
            ?: ($revision->detail->thesis?->defenseApplication?->file_skripsi ?? null)));

        return view('student-defense-revisions.show', compact('revision', 'allRevisions', 'defaultPdfUrl'));
    }

    public function storeReply(Request $request, ThesisDefenseRevision $revision)
    {
        $user = Auth::user();
        if ($revision->detail->thesis->student_id !== $user->id) abort(403);

        $request->validate([
            'student_notes' => 'required|string',
            'student_link' => 'nullable|url',
        ]);

        $this->revisionService->storeReply($revision, ThesisDefenseRevisionMessage::class, $request->only('student_notes'), $request->input('student_link'));

        return redirect()->back()->with('success', 'Follow-up revisi berhasil dikirim.');
    }

    public function printPdf(ThesisDefenseRevision $revision)
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

        $pdf = Pdf::loadView('student-defense-revisions.print', compact('revision', 'firstMessage'));
        return $pdf->stream('Pernyataan_Revisi_' . $revision->detail->thesis->student->identifier . '.pdf');
    }
}

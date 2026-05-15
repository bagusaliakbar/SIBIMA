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
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $query = MentoringSession::forUser($user)
            ->search($search)
            ->orderBy('scheduled_at', 'desc');

        if ($user->role === 'dosen') {
            $sessions = $query->with('thesis.student')->paginate(12);
            return view('mentoring.index', compact('sessions', 'search'));
        } elseif ($user->role === 'mahasiswa') {
            $sessions = $query->with('thesis.pembimbing1', 'thesis.pembimbing2')->paginate(10);
            return view('mentoring.student_index', compact('sessions', 'search'));
        } elseif ($user->role === 'admin') {
            $sessions = MentoringSession::search($search)
                ->with(['thesis.student', 'dosen'])
                ->orderBy('scheduled_at', 'desc')
                ->paginate(15);
            return view('mentoring.index', compact('sessions', 'search'));
        }

        abort(403);
    }

    public function updateStatus(UpdateMentoringSessionStatusRequest $request, MentoringSession $session)
    {
        // Verify that this dosen owns the thesis
        if ($session->thesis->pembimbing1_id !== Auth::id() && $session->thesis->pembimbing2_id !== Auth::id()) {
            abort(403);
        }

        $message = $this->mentoringService->updateStatus($session, $request->validated());

        return redirect()->back()->with('success', $message);
    }

    public function uploadDocument(UploadMentoringDocumentRequest $request, MentoringSession $session)
    {
        // Only the session's student can upload
        if ($session->thesis->student_id !== Auth::id()) {
            abort(403);
        }

        $originalName = $this->mentoringService->uploadDocument($session, $request->file('document'));

        return redirect()->back()->with('success', "Dokumen \"{$originalName}\" berhasil diunggah.");
    }

    public function deleteDocument(MentoringSession $session)
    {
        // Only the session's student can delete
        if (Auth::user()->role !== 'mahasiswa' || $session->thesis->student_id !== Auth::id()) {
            abort(403);
        }

        $this->mentoringService->deleteDocument($session);

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }
}

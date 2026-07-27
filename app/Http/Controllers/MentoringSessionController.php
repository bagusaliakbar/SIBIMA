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
        } elseif ($user->role === 'admin' || $user->role === 'kaprodi') {
            $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
                ->where('status', 'active')
                ->get();
            $dosens = \App\Models\User::whereIn('role', ['dosen', 'kaprodi'])->orderBy('name')->get();
            return view('mentoring.create', compact('theses', 'dosens'));
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $activeTab = $request->input('tab', 'active');

        if ($user->role === 'dosen') {
            $sessions = MentoringSession::forUser($user)
                ->search($search)
                ->whereHas('thesis', function($q) use ($activeTab) {
                    if ($activeTab === 'history') {
                        $q->where('status', 'completed');
                    } else {
                        $q->where('status', '!=', 'completed');
                    }
                })
                ->orderBy('scheduled_at', 'desc')
                ->with('thesis.student')
                ->paginate(12)
                ->appends(['search' => $search, 'tab' => $activeTab]);
            return view('mentoring.index', compact('sessions', 'search', 'activeTab'));
        } elseif ($user->role === 'mahasiswa') {
            $sessions = MentoringSession::forUser($user)
                ->search($search)
                ->orderBy('scheduled_at', 'desc')
                ->with('thesis.pembimbing1', 'thesis.pembimbing2')
                ->paginate(10)
                ->appends(['search' => $search]);
            return view('mentoring.student_index', compact('sessions', 'search'));
        } elseif ($user->role === 'admin' || $user->role === 'kaprodi') {
            $sessions = MentoringSession::search($search)
                ->whereHas('thesis', function($q) use ($activeTab) {
                    if ($activeTab === 'history') {
                        $q->where('status', 'completed');
                    } else {
                        $q->where('status', '!=', 'completed');
                    }
                })
                ->with(['thesis.student', 'dosen'])
                ->orderBy('scheduled_at', 'desc')
                ->paginate(15)
                ->appends(['search' => $search, 'tab' => $activeTab]);
            return view('mentoring.index', compact('sessions', 'search', 'activeTab'));
        }

        abort(403);
    }

    public function updateStatus(UpdateMentoringSessionStatusRequest $request, MentoringSession $session)
    {
        // Verify that this dosen owns the thesis
        $this->authorize('updateStatus', $session);

        $message = $this->mentoringService->updateStatus($session, $request->validated());

        return redirect()->back()->with('success', $message);
    }

    public function uploadDocument(UploadMentoringDocumentRequest $request, MentoringSession $session)
    {
        // Only the session's student can upload
        $this->authorize('uploadDocument', $session);

        try {
            $this->mentoringService->uploadDocument($session, $request->input('document'));
            return redirect()->back()->with('success', "Link dokumen berhasil ditambahkan.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Gagal mengunggah dokumen: " . $e->getMessage());
        }
    }

    public function deleteDocument(MentoringSession $session)
    {
        // Only the session's student can delete
        $this->authorize('deleteDocument', $session);

        $this->mentoringService->deleteDocument($session);

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }
}

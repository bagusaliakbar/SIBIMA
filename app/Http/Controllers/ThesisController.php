<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThesisRequest;
use App\Http\Requests\AssignPembimbingRequest;
use App\Http\Requests\UpdateThesisRequest;
use App\Models\Thesis;
use App\Models\User;
use App\Services\ThesisService;
use App\Exports\ThesesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThesisController extends Controller
{
    protected $thesisService;

    public function __construct(ThesisService $thesisService)
    {
        $this->thesisService = $thesisService;
    }

    public function exportExcel(Request $request)
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'kaprodi') {
            abort(403);
        }
        $search = $request->input('search');
        $status = $request->input('status', 'all');
        return Excel::download(new ThesesExport($search, $status), 'data-skripsi-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->role !== 'kaprodi' && $user->role !== 'dosen') {
            abort(403);
        }

        $search = $request->input('search');
        $defaultStatus = $user->role === 'dosen' ? 'active' : 'all';
        $status = $request->input('status', $defaultStatus);

        $thesesQuery = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->forUser($user)
            ->search($search);

        if ($status !== 'all') {
            $thesesQuery->where('status', $status);
        }

        $theses = $thesesQuery->get();

        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();
        
        $pdf = Pdf::loadView('theses.pdf', compact('theses', 'kaprodi'));
        return $pdf->download('data-skripsi-' . now()->format('Y-m-d') . '.pdf');
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role === 'mahasiswa') {
            // Check if student already has a thesis
            $existingThesis = Thesis::where('student_id', $user->id)->first();
            if ($existingThesis) {
                return redirect()->route('dashboard')->with('error', 'Anda sudah mengajukan skripsi.');
            }
            $students = collect();
        } elseif ($user->role === 'admin' || $user->role === 'kaprodi') {
            $students = User::where('role', 'mahasiswa')
                ->whereDoesntHave('thesis')
                ->orderBy('name')
                ->get();
        } else {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak akses untuk mengajukan skripsi.');
        }

        $dosens = User::where('role', 'dosen')->orderBy('name')->get();

        return view('theses.create', compact('dosens', 'students'));
    }

    public function store(StoreThesisRequest $request)
    {
        $this->thesisService->createThesis($request->validated());

        return redirect()->route('theses.index')->with('success', 'Pengajuan skripsi berhasil dikirim.');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        if ($user->role === 'mahasiswa') {
            return redirect()->route('dashboard');
        }

        $defaultStatus = $user->role === 'dosen' ? 'active' : 'all';
        $status = $request->input('status', $defaultStatus);

        $thesesQuery = Thesis::with(['student', 'pembimbing1', 'pembimbing2', 'requestedPembimbing1', 'requestedPembimbing2'])
            ->forUser($user)
            ->search($search);

        if ($status !== 'all') {
            $thesesQuery->where('status', $status);
        }

        $theses = $thesesQuery->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $search, 'status' => $status]);

        if ($user->role === 'admin' || $user->role === 'kaprodi') {
            $dosens = User::where('role', 'dosen')
                ->withCount(['thesesAsP1 as p1_count' => function($query) {
                    $query->where('status', 'active');
                }])
                ->withCount(['thesesAsP2 as p2_count' => function($query) {
                    $query->where('status', 'active');
                }])
                ->get()
                ->map(function($dosen) {
                    $dosen->total_workload = $dosen->p1_count + $dosen->p2_count;
                    return $dosen;
                });
            return view('theses.index', compact('theses', 'dosens', 'search', 'status'));
        }

        return view('theses.index', compact('theses', 'search', 'status'));
    }

    public function assignPembimbing(AssignPembimbingRequest $request, Thesis $thesis)
    {
        $this->thesisService->assignPembimbing($thesis, $request->validated());

        return redirect()->back()->with('success', 'Dosen pembimbing berhasil ditugaskan.');
    }

    public function update(UpdateThesisRequest $request, Thesis $thesis)
    {
        $this->authorize('update', $thesis);
        
        $this->thesisService->updateThesis($thesis, $request->validated());

        return redirect()->back()->with('success', 'Data skripsi berhasil diperbarui.');
    }

    public function toggleAcc(Request $request, Thesis $thesis, $type)
    {
        $this->authorize('toggleAcc', $thesis);

        try {
            $slot = $request->input('slot');
            $statusText = $this->thesisService->toggleAcc($thesis, $type, $slot);
            $typeName = $type === 'up' ? 'Seminar UP' : 'Sidang Akhir';

            return redirect()->back()->with('success', "ACC {$typeName} berhasil {$statusText}.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}

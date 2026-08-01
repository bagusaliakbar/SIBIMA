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
use App\Imports\ThesesImport;

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

    public function checkTitle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:10'
        ]);

        $inputTitle = strtolower(trim($request->title));
        
        $theses = Thesis::with('student')->get();
        $repositories = \App\Models\ThesisRepository::all();
        
        $similarTitles = [];
        
        foreach($theses as $thesis) {
            $existingTitle = strtolower(trim($thesis->title));
            // Calculate similarity percentage using Levenshtein distance implicitly in similar_text
            similar_text($inputTitle, $existingTitle, $percent);
            
            if ($percent >= 60) {
                $similarTitles[] = [
                    'title' => $thesis->title,
                    'student_name' => $thesis->student->name ?? 'Unknown',
                    'year' => $thesis->created_at ? $thesis->created_at->format('Y') : date('Y'),
                    'percentage' => round($percent, 1),
                    'source' => 'Skripsi Aktif'
                ];
            }
        }

        foreach($repositories as $repo) {
            $existingTitle = strtolower(trim($repo->title));
            similar_text($inputTitle, $existingTitle, $percent);
            
            if ($percent >= 60) {
                $similarTitles[] = [
                    'title' => $repo->title,
                    'student_name' => $repo->name,
                    'year' => $repo->year,
                    'percentage' => round($percent, 1),
                    'source' => 'Arsip Alumni'
                ];
            }
        }
        
        // Sort by highest percentage
        usort($similarTitles, function($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });
        
        return response()->json([
            'similar' => array_slice($similarTitles, 0, 3)
        ]);
    }

    public function kanban()
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->role !== 'kaprodi') {
            abort(403);
        }

        // Fetch all theses with relationships to group them
        $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2', 'seminarApplication', 'defenseApplication'])
            ->orderBy('created_at', 'desc')
            ->get();

        $pengajuanBaru = collect();
        $bimbinganUp = collect();
        $prosesSeminar = collect();
        $siapSidang = collect();
        $lulus = collect();

        foreach ($theses as $thesis) {
            if ($thesis->status === 'completed') {
                $lulus->push($thesis);
            } elseif ($thesis->status === 'pending') {
                $pengajuanBaru->push($thesis);
            } elseif ($thesis->status === 'active') {
                if ($thesis->isAccSidangFinal() || $thesis->defenseApplication) {
                    $siapSidang->push($thesis);
                } elseif ($thesis->isAccUpFinal() || $thesis->seminarApplication) {
                    $prosesSeminar->push($thesis);
                } else {
                    $bimbinganUp->push($thesis);
                }
            }
        }

        return view('theses.kanban', compact(
            'pengajuanBaru', 'bimbinganUp', 'prosesSeminar', 'siapSidang', 'lulus'
        ));
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

    public function createMigration()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $students = User::where('role', 'mahasiswa')
            ->whereDoesntHave('thesis')
            ->orderBy('name')
            ->get();
        $dosens = User::where('role', 'dosen')->orderBy('name')->get();

        return view('theses.create-migration', compact('dosens', 'students'));
    }

    public function storeMigration(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'abstract' => 'nullable|string',
            'pembimbing1_id' => 'required|exists:users,id',
            'pembimbing2_id' => 'required|exists:users,id|different:pembimbing1_id',
            'current_stage' => 'required|string|in:Bimbingan Skripsi,Selesai Seminar UP,Siap Sidang',
        ]);

        $this->thesisService->createMigrationThesis($validated);

        return redirect()->route('theses.index')->with('success', 'Data migrasi skripsi berhasil ditambahkan.');
    }

    public function importExcel(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new ThesesImport($this->thesisService), $request->file('file'));
            return redirect()->route('theses.index')->with('success', 'Data migrasi skripsi berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }
        
        return Excel::download(new \App\Exports\MigrationTemplateExport, 'Template_Migrasi_Skripsi.xlsx');
    }
}

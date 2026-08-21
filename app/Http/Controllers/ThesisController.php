<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThesisRequest;
use App\Http\Requests\AssignPembimbingRequest;
use App\Http\Requests\UpdateThesisRequest;
use App\Models\Thesis;
use App\Models\User;
use App\Services\ThesisService;
use App\Exports\ThesesExport;
use App\Exports\UnsubmittedStudentsExport;
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

        $inputTitle = $request->title;
        
        $thesesQuery = Thesis::with('student');
        if (Auth::check() && Auth::user()->role === 'mahasiswa') {
            $thesesQuery->where('student_id', '!=', Auth::id());
        }
        $theses = $thesesQuery->get();
        $repositories = \App\Models\ThesisRepository::all();
        
        $similarTitles = [];
        
        foreach($theses as $thesis) {
            $res = $this->calculateSimilarityDetails($inputTitle, $thesis->title);
            
            if ($res['percentage'] >= 45) {
                $similarTitles[] = [
                    'title' => $thesis->title,
                    'student_name' => $thesis->student->name ?? 'Unknown',
                    'year' => $thesis->student->entry_year ?? ($thesis->created_at ? $thesis->created_at->format('Y') : date('Y')),
                    'percentage' => $res['percentage'],
                    'matched_words' => $res['matched_words'],
                    'source' => 'Skripsi Aktif'
                ];
            }
        }

        foreach($repositories as $repo) {
            $res = $this->calculateSimilarityDetails($inputTitle, $repo->title);
            
            if ($res['percentage'] >= 45) {
                $similarTitles[] = [
                    'title' => $repo->title,
                    'student_name' => $repo->name,
                    'year' => $repo->year,
                    'percentage' => $res['percentage'],
                    'matched_words' => $res['matched_words'],
                    'source' => 'Arsip Alumni'
                ];
            }
        }
        
        // Sort by highest percentage
        usort($similarTitles, function($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });
        
        return response()->json([
            'similar' => array_slice($similarTitles, 0, 4)
        ]);
    }

    private function calculateSimilarityDetails($input, $existing)
    {
        $stopwords = ['sistem', 'informasi', 'aplikasi', 'perancangan', 'rancang', 'bangun', 'pembuatan', 'pengembangan', 'berbasis', 'web', 'android', 'website', 'mobile', 'dengan', 'metode', 'menggunakan', 'pada', 'untuk', 'studi', 'kasus', 'penerapan', 'implementasi', 'pengaruh', 'analisis', 'evaluasi', 'pengujian', 'desa', 'kabupaten', 'kota', 'kecamatan', 'pt', 'cv'];
        
        $cleanInput = preg_replace('/[^a-z0-9\s]/', '', strtolower($input));
        $cleanExisting = preg_replace('/[^a-z0-9\s]/', '', strtolower($existing));
        
        if (trim($cleanInput) === trim($cleanExisting)) {
            return [
                'percentage' => 100.0,
                'matched_words' => array_values(array_unique(array_filter(explode(' ', $cleanInput))))
            ];
        }

        $inputTokens = array_values(array_filter(explode(' ', $cleanInput)));
        $existingTokens = array_values(array_filter(explode(' ', $cleanExisting)));
        
        $inputTokensFiltered = array_values(array_diff($inputTokens, $stopwords));
        $existingTokensFiltered = array_values(array_diff($existingTokens, $stopwords));
        
        if (empty($inputTokensFiltered)) $inputTokensFiltered = $inputTokens;
        if (empty($existingTokensFiltered)) $existingTokensFiltered = $existingTokens;
        
        // Matched words
        $intersection = array_values(array_unique(array_intersect($inputTokensFiltered, $existingTokensFiltered)));
        $union = array_unique(array_merge($inputTokensFiltered, $existingTokensFiltered));
        
        // Jaccard similarity on filtered tokens
        $jaccardScore = count($union) > 0 ? (count($intersection) / count($union)) * 100 : 0;
        
        // Dice similarity
        $diceScore = (count($inputTokensFiltered) + count($existingTokensFiltered)) > 0 
            ? (2 * count($intersection) / (count($inputTokensFiltered) + count($existingTokensFiltered))) * 100 
            : 0;

        // Overall full text similarity
        $similarTextPercent = 0;
        similar_text($cleanInput, $cleanExisting, $similarTextPercent);

        // Combined score: 60% Token similarity + 40% Full text similarity
        $tokenScore = max($jaccardScore, $diceScore);
        $finalScore = ($tokenScore * 0.60) + ($similarTextPercent * 0.40);
        
        return [
            'percentage' => min(100, round($finalScore, 1)),
            'matched_words' => $intersection
        ];
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
            /** @var Thesis $thesis */
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

            // Pending count for badge on the clean audit button
            $pendingCount = Thesis::where('status', 'pending')->count();
            $pendingSummary = ['total' => $pendingCount];

            return view('theses.index', compact('theses', 'dosens', 'search', 'status', 'pendingSummary'));
        }

        return view('theses.index', compact('theses', 'search', 'status'));
    }

    public function cleanAudit(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $dosens = User::where('role', 'dosen')
            ->withCount(['thesesAsP1 as p1_count' => function ($query) {
                $query->whereIn('status', ['active', 'seminar_proposal', 'seminar_hasil', 'sidang']);
            }])
            ->withCount(['thesesAsP2 as p2_count' => function ($query) {
                $query->whereIn('status', ['active', 'seminar_proposal', 'seminar_hasil', 'sidang']);
            }])
            ->get()
            ->map(function($dosen) {
                $dosen->total_workload = $dosen->p1_count + $dosen->p2_count;
                return $dosen;
            });

        $search = $request->query('search', '');
        $filterCategory = $request->query('category', 'all');

        $pendingTheses = Thesis::with(['student', 'requestedPembimbing1', 'requestedPembimbing2'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $cleanDataCollection = $pendingTheses->map(function($thesis) use ($dosens) {
            return $thesis->getAuditCleanData($dosens);
        });

        $pendingSummary = [
            'total' => $cleanDataCollection->count(),
            'clean_count' => $cleanDataCollection->where('category', 'clean')->count(),
            'warning_count' => $cleanDataCollection->where('category', 'warning')->count(),
            'critical_count' => $cleanDataCollection->where('category', 'critical')->count(),
        ];

        return view('theses.clean-audit', compact('cleanDataCollection', 'pendingSummary', 'dosens', 'search', 'filterCategory'));
    }

    public function assignPembimbing(AssignPembimbingRequest $request, Thesis $thesis)
    {
        $this->thesisService->assignPembimbing($thesis, $request->validated());

        return redirect()->back()->with('success', 'Dosen pembimbing berhasil ditugaskan.');
    }

    public function unassignPembimbing(Thesis $thesis)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $this->thesisService->unassignPembimbing($thesis);

        return redirect()->back()->with('success', 'Penugasan pembimbing berhasil dibatalkan (di-rollback) dan status dikembalikan ke Menunggu.');
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

    public function unsubmittedStudents(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $search = $request->input('search');
        $entryYear = $request->input('entry_year');
        $semesterFilter = $request->input('semester_filter'); // 'all', 'critical', 'warning', 'normal'

        $allUnsubmitted = User::where('role', 'mahasiswa')
            ->whereDoesntHave('thesis')
            ->get();

        $totalUnsubmitted = $allUnsubmitted->count();
        $criticalCount = $allUnsubmitted->filter(fn($u) => $u->is_critical_semester)->count();
        $warningCount = $allUnsubmitted->filter(fn($u) => ($u->current_semester ?? 0) >= 7 && !$u->is_critical_semester)->count();
        $yearDistribution = $allUnsubmitted->whereNotNull('entry_year')->groupBy('entry_year')->map->count()->sortKeysDesc();

        $entryYears = User::where('role', 'mahasiswa')
            ->whereDoesntHave('thesis')
            ->whereNotNull('entry_year')
            ->distinct()
            ->orderBy('entry_year', 'desc')
            ->pluck('entry_year');

        $query = User::where('role', 'mahasiswa')
            ->whereDoesntHave('thesis')
            ->when($search, function ($q, $search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                       ->orWhere('identifier', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($entryYear, function ($q, $entryYear) {
                $q->where('entry_year', $entryYear);
            });

        if ($semesterFilter === 'critical') {
            $filteredIds = $allUnsubmitted->filter(fn($u) => $u->is_critical_semester)->pluck('id');
            $query->whereIn('id', $filteredIds);
        } elseif ($semesterFilter === 'warning') {
            $filteredIds = $allUnsubmitted->filter(fn($u) => ($u->current_semester ?? 0) >= 7 && !$u->is_critical_semester)->pluck('id');
            $query->whereIn('id', $filteredIds);
        } elseif ($semesterFilter === 'normal') {
            $filteredIds = $allUnsubmitted->filter(fn($u) => ($u->current_semester ?? 0) < 7)->pluck('id');
            $query->whereIn('id', $filteredIds);
        }

        $students = $query->orderBy('entry_year', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('theses.unsubmitted_students', compact(
            'students',
            'search',
            'entryYear',
            'semesterFilter',
            'entryYears',
            'totalUnsubmitted',
            'criticalCount',
            'warningCount',
            'yearDistribution'
        ));
    }

    public function exportUnsubmittedExcel(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $search = $request->input('search');
        $entryYear = $request->input('entry_year');
        $semesterFilter = $request->input('semester_filter');

        $fileName = 'Data_Mahasiswa_Belum_Mengajukan_' . ($entryYear ? 'Angkatan_' . $entryYear . '_' : '') . now()->format('Ymd') . '.xlsx';

        return Excel::download(new UnsubmittedStudentsExport($search, $entryYear, $semesterFilter), $fileName);
    }

    public function exportUnsubmittedPdf(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $search = $request->input('search');
        $entryYear = $request->input('entry_year');
        $semesterFilter = $request->input('semester_filter');

        $query = User::where('role', 'mahasiswa')
            ->whereDoesntHave('thesis')
            ->when($search, function ($q, $search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                       ->orWhere('identifier', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($entryYear, function ($q, $entryYear) {
                $q->where('entry_year', $entryYear);
            });

        $students = $query->orderBy('entry_year', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($semesterFilter === 'critical') {
            $students = $students->filter(fn($u) => $u->is_critical_semester);
        } elseif ($semesterFilter === 'warning') {
            $students = $students->filter(fn($u) => ($u->current_semester ?? 0) >= 7 && !$u->is_critical_semester);
        } elseif ($semesterFilter === 'normal') {
            $students = $students->filter(fn($u) => ($u->current_semester ?? 0) < 7);
        }

        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();

        $pdf = Pdf::loadView('theses.unsubmitted_students_pdf', compact('students', 'kaprodi', 'entryYear', 'semesterFilter'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Data_Mahasiswa_Belum_Mengajukan_' . now()->format('Ymd') . '.pdf');
    }
}

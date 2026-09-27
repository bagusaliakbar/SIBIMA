<?php

namespace App\Http\Controllers;

use App\Models\AdvisorDecree;
use App\Models\Thesis;
use App\Models\User;
use App\Models\Wave;
use App\Models\LetterSetting;
use App\Models\ActivityLog;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AdvisorDecreeController extends Controller
{
    /**
     * Display a listing of advisor decrees.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isStaff = in_array($user->role, ['admin', 'kaprodi']);

        $query = AdvisorDecree::with(['creator', 'dosen', 'wave']);

        if (!$isStaff && $user->role === 'dosen') {
            // Lecturers can see collective decrees OR decrees specifically issued for them
            $query->where(function ($q) use ($user) {
                $q->where('target_type', 'collective')
                  ->orWhere('dosen_id', $user->id)
                  ->orWhere('theses_data', 'LIKE', '%' . $user->name . '%')
                  ->orWhere('theses_data', 'LIKE', '%' . $user->identifier . '%');
            });
        }

        $targetType = $request->get('target_type', 'all');

        $baseStatsQuery = AdvisorDecree::query();
        if (!$isStaff && $user->role === 'dosen') {
            $baseStatsQuery->where(function ($q) use ($user) {
                $q->where('target_type', 'collective')
                  ->orWhere('dosen_id', $user->id)
                  ->orWhere('theses_data', 'LIKE', '%' . $user->name . '%')
                  ->orWhere('theses_data', 'LIKE', '%' . $user->identifier . '%');
            });
        }

        $stats = [
            'total' => (clone $baseStatsQuery)->count(),
            'collective' => (clone $baseStatsQuery)->where('target_type', 'collective')->count(),
            'individual' => (clone $baseStatsQuery)->where('target_type', 'individual_dosen')->count(),
            'total_students' => (int) ((clone $baseStatsQuery)->sum('total_students') ?: 0),
        ];

        if ($targetType !== 'all') {
            $query->where('target_type', $targetType);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('decree_number', 'LIKE', "%{$search}%")
                  ->orWhere('title', 'LIKE', "%{$search}%")
                  ->orWhere('signatory_name', 'LIKE', "%{$search}%")
                  ->orWhere('academic_year', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('academic_year') && $request->academic_year !== 'all') {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->filled('semester') && $request->semester !== 'all') {
            $query->where('semester', $request->semester);
        }

        $decrees = $query->orderBy('decree_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $academicYears = AdvisorDecree::select('academic_year')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year');

        return view('documents.advisor_decrees.index', compact(
            'decrees',
            'academicYears',
            'isStaff',
            'stats',
            'targetType'
        ));
    }

    /**
     * Show the form for creating a new advisor decree.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi', 'dosen'])) {
            abort(403, 'Akses terbatas untuk Administrator, Kaprodi, dan Dosen.');
        }

        // Determine current academic year and semester
        $currentMonth = (int) now()->format('n');
        $currentYear = (int) now()->format('Y');

        if ($currentMonth >= 9) {
            $defaultAcademicYear = $currentYear . '/' . ($currentYear + 1);
            $defaultSemester = 'Ganjil';
        } elseif ($currentMonth <= 2) {
            $defaultAcademicYear = ($currentYear - 1) . '/' . $currentYear;
            $defaultSemester = 'Ganjil';
        } else {
            $defaultAcademicYear = ($currentYear - 1) . '/' . $currentYear;
            $defaultSemester = 'Genap';
        }

        // Get preview of next letter number
        $letterSetting = LetterSetting::firstOrCreate(
            ['type' => 'sk_pembimbing'],
            [
                'title' => 'SK Dosen Pembimbing Skripsi',
                'format' => '[NUMBER]/SK-PEMBIMBING/UNSUB/FIK/[ROMAN_MONTH]/[YEAR]',
                'last_number' => 0,
            ]
        );

        $nextNumber = str_pad($letterSetting->last_number + 1, 3, '0', STR_PAD_LEFT);
        $month = now()->format('m');
        $year = now()->format('Y');
        $romans = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $romanMonth = $romans[(int)$month] ?? 'I';

        $previewLetterNumber = str_replace(
            ['[NUMBER]', '[MONTH]', '[ROMAN_MONTH]', '[YEAR]'],
            [$nextNumber, $month, $romanMonth, $year],
            $letterSetting->format
        );

        // Available Cohorts / Angkatan
        $cohorts = User::where('role', 'mahasiswa')
            ->whereNotNull('entry_year')
            ->distinct()
            ->orderBy('entry_year', 'desc')
            ->pluck('entry_year');

        // Waves & Lecturers
        $waves = Wave::orderBy('id', 'desc')->get();
        $dosens = User::whereIn('role', ['dosen', 'kaprodi'])->orderBy('name')->get();

        // Signatory defaults (Kaprodi / Dekan)
        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();
        $defaultSignatoryTitle = 'Dekan Fakultas Ilmu Komputer';
        $defaultSignatoryName = $kaprodi ? $kaprodi->name : 'Dekan Fasilkom';
        $defaultSignatoryIdentifier = $kaprodi ? $kaprodi->identifier : null;

        return view('documents.advisor_decrees.create', compact(
            'defaultAcademicYear',
            'defaultSemester',
            'previewLetterNumber',
            'cohorts',
            'waves',
            'dosens',
            'kaprodi',
            'defaultSignatoryTitle',
            'defaultSignatoryName',
            'defaultSignatoryIdentifier'
        ));
    }

    /**
     * Helper to compute Academic Year and Semester from a Carbon date.
     */
    public static function getAcademicPeriod(Carbon $date): array
    {
        $month = (int) $date->format('n');
        $year = (int) $date->format('Y');

        if ($month >= 9) {
            $ay = $year . '/' . ($year + 1);
            $sem = 'Ganjil';
        } elseif ($month <= 2) {
            $ay = ($year - 1) . '/' . $year;
            $sem = 'Ganjil';
        } else {
            $ay = ($year - 1) . '/' . $year;
            $sem = 'Genap';
        }

        return [
            'academic_year' => $ay,
            'semester' => $sem,
            'label' => "{$ay} ({$sem})",
        ];
    }

    /**
     * Helper to compute start and end Carbon dates for a given academic year & semester.
     */
    public static function getDateRangeForPeriod(string $academicYear, string $semester): array
    {
        $parts = explode('/', trim($academicYear));
        $startYear = (int) ($parts[0] ?? now()->year);
        $endYear = (int) ($parts[1] ?? ($startYear + 1));

        if (strtolower(trim($semester)) === 'ganjil') {
            $start = Carbon::create($startYear, 9, 1, 0, 0, 0);
            $end = Carbon::create($endYear, 2, 28, 23, 59, 59)->endOfMonth();
        } else {
            $start = Carbon::create($endYear, 3, 1, 0, 0, 0);
            $end = Carbon::create($endYear, 8, 31, 23, 59, 59);
        }

        return [$start, $end];
    }

    /**
     * AJAX endpoint to query candidate theses based on filters.
     */
    public function candidates(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi', 'dosen'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Map all existing decrees to see which theses have already been decreed
        $existingDecrees = AdvisorDecree::all(['id', 'decree_number', 'academic_year', 'semester', 'theses_data']);
        $decreedMap = [];
        foreach ($existingDecrees as $dec) {
            if (is_array($dec->theses_data)) {
                foreach ($dec->theses_data as $item) {
                    if (!empty($item['thesis_id'])) {
                        $decreedMap[(int)$item['thesis_id']] = [
                            'decree_id' => $dec->id,
                            'decree_number' => $dec->decree_number,
                            'period' => "{$dec->academic_year} ({$dec->semester})",
                        ];
                    }
                }
            }
        }

        $query = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->whereNotNull('pembimbing1_id'); // Must have at least Pembimbing 1 assigned

        // Target type filter
        if ($user->role === 'dosen') {
            $query->where(function ($q) use ($user) {
                $q->where('pembimbing1_id', $user->id)
                  ->orWhere('pembimbing2_id', $user->id);
            });
        } elseif ($request->target_type === 'individual_dosen' && $request->filled('dosen_id')) {
            $dosenId = $request->dosen_id;
            $query->where(function ($q) use ($dosenId) {
                $q->where('pembimbing1_id', $dosenId)
                  ->orWhere('pembimbing2_id', $dosenId);
            });
        }

        // Decree status filter: unassigned (default) vs assigned vs all
        $decreeStatus = $request->input('decree_status', 'unassigned');
        if ($decreeStatus === 'unassigned') {
            if (!empty($decreedMap)) {
                $query->whereNotIn('id', array_keys($decreedMap));
            }
        } elseif ($decreeStatus === 'assigned') {
            $query->whereIn('id', array_keys($decreedMap));
        }

        // Submission period filter (Periode Pengajuan Judul Mahasiswa)
        $submissionPeriod = $request->input('submission_period', 'same_as_decree');
        if ($submissionPeriod === 'same_as_decree' && $request->filled('academic_year') && $request->filled('semester')) {
            try {
                [$startRange, $endRange] = self::getDateRangeForPeriod($request->academic_year, $request->semester);
                $query->whereBetween('created_at', [$startRange, $endRange]);
            } catch (\Exception $e) {
                // Ignore if date format error
            }
        } elseif ($submissionPeriod === 'custom' && $request->filled('filter_academic_year') && $request->filled('filter_semester')) {
            try {
                [$startRange, $endRange] = self::getDateRangeForPeriod($request->filter_academic_year, $request->filter_semester);
                $query->whereBetween('created_at', [$startRange, $endRange]);
            } catch (\Exception $e) {
                // Ignore
            }
        }
        // If 'all', do not constrain created_at

        // Thesis status filter: active (default: belum lulus) vs completed (sudah lulus) vs all
        $status = $request->input('status', 'active');
        if ($status === 'active') {
            $query->activeMentoring();
        } elseif ($status === 'completed') {
            $query->graduated();
        } else {
            $query->whereIn('status', ['active', 'completed']);
        }

        // Cohort filter
        if ($request->filled('cohort') && $request->cohort !== 'all') {
            $cohort = $request->cohort;
            $query->whereHas('student', function ($q) use ($cohort) {
                $q->where('entry_year', $cohort);
            });
        }

        // Wave filter
        if ($request->filled('wave_id') && $request->wave_id !== 'all' && $request->wave_id !== '') {
            $waveId = $request->wave_id;
            $query->where(function ($q) use ($waveId) {
                $q->whereHas('seminarApplications', function ($sq) use ($waveId) {
                    $sq->where('wave_id', $waveId);
                })->orWhereHas('defenseApplications', function ($dq) use ($waveId) {
                    $dq->where('wave_id', $waveId);
                });
            });
        }

        // Search filter
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('final_title', 'LIKE', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('identifier', 'LIKE', "%{$search}%");
                  });
            });
        }

        $theses = $query->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();

        $candidates = $theses->map(function ($t) use ($decreedMap) {
            $period = $t->created_at ? self::getAcademicPeriod($t->created_at) : null;
            $isDecreed = isset($decreedMap[$t->id]);

            return [
                'id' => $t->id,
                'student_name' => $t->student ? $t->student->name : '-',
                'student_npm' => $t->student ? ($t->student->identifier ?? '-') : '-',
                'student_cohort' => $t->student ? ($t->student->entry_year ?? '-') : '-',
                'title' => $t->display_title,
                'topic' => $t->topic ?: '-',
                'status' => $t->status,
                'submission_date' => $t->created_at ? $t->created_at->translatedFormat('d M Y') : '-',
                'submission_period' => $period ? $period['label'] : '-',
                'has_decree' => $isDecreed,
                'decree_info' => $decreedMap[$t->id] ?? null,
                'pembimbing1_id' => $t->pembimbing1_id,
                'pembimbing1_name' => $t->pembimbing1 ? $t->pembimbing1->name : '-',
                'pembimbing1_nidn' => $t->pembimbing1 ? ($t->pembimbing1->identifier ?? '-') : '-',
                'pembimbing2_id' => $t->pembimbing2_id,
                'pembimbing2_name' => $t->pembimbing2 ? $t->pembimbing2->name : '-',
                'pembimbing2_nidn' => $t->pembimbing2 ? ($t->pembimbing2->identifier ?? '-') : '-',
            ];
        });

        return response()->json([
            'count' => $candidates->count(),
            'candidates' => $candidates
        ]);
    }

    /**
     * Store a newly created advisor decree.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi', 'dosen'])) {
            abort(403);
        }

        if ($user->role === 'dosen') {
            $request->merge([
                'target_type' => 'individual_dosen',
                'dosen_id' => $user->id,
            ]);
        }

        if ($request->wave_id === 'all' || empty($request->wave_id)) {
            $request->merge(['wave_id' => null]);
        }

        $selectedThesesIds = $request->input('selected_theses', []);
        $manualTheses = $request->input('manual_theses', []);

        if (empty($selectedThesesIds) && empty($manualTheses)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['selected_theses' => 'Pilih minimal satu mahasiswa dari daftar atau tambahkan mahasiswa secara manual.']);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'academic_year' => 'required|string|max:50',
            'semester' => 'required|in:Ganjil,Genap',
            'target_type' => 'required|in:collective,individual_dosen',
            'dosen_id' => 'nullable|required_if:target_type,individual_dosen|exists:users,id',
            'wave_id' => 'nullable|exists:waves,id',
            'decree_date' => 'required|date',
            'signatory_title' => 'required|string|max:255',
            'signatory_name' => 'required|string|max:255',
            'signatory_identifier' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'selected_theses' => 'nullable|array',
            'selected_theses.*' => 'exists:theses,id',
            'manual_theses' => 'nullable|array',
        ], [
            'wave_id.exists' => 'Gelombang pelaksanaan yang dipilih tidak valid.',
            'dosen_id.required_if' => 'Dosen pembimbing wajib dipilih untuk surat tugas BKD dosen.',
            'title.required' => 'Perihal / Judul SK wajib diisi.',
            'academic_year.required' => 'Tahun akademik wajib diisi.',
            'decree_date.required' => 'Tanggal penetapan SK wajib diisi.',
            'signatory_title.required' => 'Jabatan penandatangan wajib diisi.',
            'signatory_name.required' => 'Nama pejabat penandatangan wajib diisi.',
        ]);

        // Generate decree number atomically via LetterSetting
        $decreeDate = Carbon::parse($request->decree_date);
        $decreeNumber = DB::transaction(function () use ($decreeDate, $request) {
            if ($request->filled('custom_decree_number')) {
                return trim($request->custom_decree_number);
            }

            $setting = LetterSetting::firstOrCreate(
                ['type' => 'sk_pembimbing'],
                [
                    'title' => 'SK Dosen Pembimbing Skripsi',
                    'format' => '[NUMBER]/SK-PEMBIMBING/UNSUB/FIK/[ROMAN_MONTH]/[YEAR]',
                    'last_number' => 0,
                ]
            );

            $setting->increment('last_number');
            $number = str_pad($setting->last_number, 3, '0', STR_PAD_LEFT);
            $month = $decreeDate->format('m');
            $year = $decreeDate->format('Y');
            $romans = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $romanMonth = $romans[(int)$month] ?? 'I';

            return str_replace(
                ['[NUMBER]', '[MONTH]', '[ROMAN_MONTH]', '[YEAR]'],
                [$number, $month, $romanMonth, $year],
                $setting->format
            );
        });

        // Retrieve selected theses and build frozen snapshot
        $thesesData = [];
        $lecturersToNotify = collect();

        if (!empty($selectedThesesIds)) {
            $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
                ->whereIn('id', $selectedThesesIds)
                ->get();

            foreach ($theses as $thesis) {
                $thesesData[] = [
                    'thesis_id' => $thesis->id,
                    'student_id' => $thesis->student_id,
                    'student_name' => $thesis->student ? $thesis->student->name : '-',
                    'student_npm' => $thesis->student ? ($thesis->student->identifier ?? '-') : '-',
                    'student_cohort' => $thesis->student ? ($thesis->student->entry_year ?? '-') : '-',
                    'title' => $thesis->display_title,
                    'topic' => $thesis->topic ?: '-',
                    'pembimbing1_id' => $thesis->pembimbing1_id,
                    'pembimbing1_name' => $thesis->pembimbing1 ? $thesis->pembimbing1->name : '-',
                    'pembimbing1_nidn' => $thesis->pembimbing1 ? ($thesis->pembimbing1->identifier ?? '-') : '-',
                    'pembimbing2_id' => $thesis->pembimbing2_id,
                    'pembimbing2_name' => $thesis->pembimbing2 ? $thesis->pembimbing2->name : '-',
                    'pembimbing2_nidn' => $thesis->pembimbing2 ? ($thesis->pembimbing2->identifier ?? '-') : '-',
                ];

                if ($thesis->pembimbing1) {
                    $lecturersToNotify->push($thesis->pembimbing1);
                }
                if ($thesis->pembimbing2) {
                    $lecturersToNotify->push($thesis->pembimbing2);
                }
            }
        }

        if (!empty($manualTheses)) {
            foreach ($manualTheses as $manual) {
                if (!empty($manual['student_name']) || !empty($manual['title'])) {
                    $thesesData[] = [
                        'thesis_id' => null,
                        'student_id' => null,
                        'student_name' => $manual['student_name'] ?? '-',
                        'student_npm' => $manual['student_npm'] ?? '-',
                        'student_cohort' => $manual['student_cohort'] ?? '-',
                        'title' => $manual['title'] ?? '-',
                        'topic' => $manual['topic'] ?? '-',
                        'pembimbing1_id' => null,
                        'pembimbing1_name' => $manual['pembimbing1_name'] ?? '-',
                        'pembimbing1_nidn' => $manual['pembimbing1_nidn'] ?? '-',
                        'pembimbing2_id' => null,
                        'pembimbing2_name' => $manual['pembimbing2_name'] ?? '-',
                        'pembimbing2_nidn' => $manual['pembimbing2_nidn'] ?? '-',
                    ];
                }
            }
        }


        // Unique verification token
        $verificationToken = Str::random(32) . time();

        // Signer user
        $signerUser = User::where('name', $request->signatory_name)->first()
            ?? User::where('role', 'kaprodi')->first()
            ?? $user;

        $decree = AdvisorDecree::create([
            'decree_number' => $decreeNumber,
            'title' => $request->title,
            'academic_year' => $request->academic_year,
            'semester' => $request->semester,
            'target_type' => $request->target_type,
            'dosen_id' => $request->target_type === 'individual_dosen' ? $request->dosen_id : null,
            'wave_id' => $request->wave_id ?: null,
            'decree_date' => $decreeDate,
            'signatory_title' => $request->signatory_title,
            'signatory_name' => $request->signatory_name,
            'signatory_identifier' => $request->signatory_identifier,
            'signer_user_id' => $signerUser ? $signerUser->id : null,
            'theses_data' => $thesesData,
            'total_students' => count($thesesData),
            'verification_token' => $verificationToken,
            'created_by' => $user->id,
            'notes' => $request->notes,
        ]);

        // Activity Log
        ActivityLog::log(
            'Penerbitan SK Pembimbing',
            "{$user->name} menerbitkan SK Pembimbing No. {$decreeNumber} untuk {$decree->total_students} mahasiswa.",
            'Dokumen & SK',
            $decree
        );

        // Notify unique lecturers in background
        $lecturersToNotify->unique('id')->each(function ($lecturer) use ($decreeNumber, $decree) {
            $lecturer->notify(new GeneralNotification(
                'SK Pembimbing Skripsi Baru Diterbitkan',
                "Surat Keputusan Pembimbing Skripsi No. {$decreeNumber} telah diterbitkan dan mencantumkan nama Anda sebagai pembimbing.",
                route('advisor-decrees.show', $decree),
                'success'
            ));
        });

        return redirect()->route('advisor-decrees.show', $decree)
            ->with('success', "Surat Keputusan Dosen Pembimbing No. {$decreeNumber} berhasil diterbitkan ({$decree->total_students} mahasiswa terlampir).");
    }

    /**
     * Display the specified advisor decree.
     */
    public function show(AdvisorDecree $advisorDecree)
    {
        $user = Auth::user();
        $isStaff = in_array($user->role, ['admin', 'kaprodi']);

        if (!$isStaff && $user->role === 'dosen') {
            // Ensure lecturer is part of this decree if not staff
            $isIncluded = $advisorDecree->target_type === 'collective'
                || $advisorDecree->dosen_id === $user->id
                || str_contains(json_encode($advisorDecree->theses_data), $user->name);

            if (!$isIncluded) {
                abort(403, 'Anda tidak memiliki hak akses ke dokumen ini.');
            }
        }

        $advisorDecree->load(['creator', 'signer', 'dosen', 'wave']);

        return view('documents.advisor_decrees.show', compact('advisorDecree', 'isStaff'));
    }

    /**
     * View for Lecturers to print their own official Surat Tugas Pembimbingan.
     */
    public function suratTugas(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi', 'dosen'])) {
            abort(403, 'Akses terbatas untuk Dosen dan Pimpinan.');
        }

        $isStaff = in_array($user->role, ['admin', 'kaprodi']);

        // Determine current academic year and semester
        $currentMonth = (int) now()->format('n');
        $currentYear = (int) now()->format('Y');

        if ($currentMonth >= 9) {
            $defaultAcademicYear = $currentYear . '/' . ($currentYear + 1);
            $defaultSemester = 'Ganjil';
        } elseif ($currentMonth <= 2) {
            $defaultAcademicYear = ($currentYear - 1) . '/' . $currentYear;
            $defaultSemester = 'Ganjil';
        } else {
            $defaultAcademicYear = ($currentYear - 1) . '/' . $currentYear;
            $defaultSemester = 'Genap';
        }

        $academicYear = $request->input('academic_year', $defaultAcademicYear);
        $semester = $request->input('semester', $defaultSemester);

        // Target Dosen
        $targetDosenId = ($isStaff && $request->filled('dosen_id')) ? $request->dosen_id : ($user->role === 'dosen' ? $user->id : User::where('role', 'dosen')->first()?->id);
        $targetDosen = User::find($targetDosenId) ?? $user;

        // Preview next Surat Tugas number
        $letterSetting = LetterSetting::firstOrCreate(
            ['type' => 'surat_tugas_pembimbing'],
            [
                'title' => 'Surat Tugas Dosen Pembimbing (BKD)',
                'format' => '[NUMBER]/PD.1.2/FIK-US/[ROMAN_MONTH]/[YEAR]',
                'last_number' => 460,
            ]
        );

        $nextNumber = str_pad($letterSetting->last_number + 1, 3, '0', STR_PAD_LEFT);
        $month = now()->format('m');
        $year = now()->format('Y');
        $romans = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $romanMonth = $romans[(int)$month] ?? 'I';

        $previewLetterNumber = str_replace(
            ['[NUMBER]', '[ROMAN_MONTH]', '[MONTH]', '[YEAR]'],
            [$nextNumber, $romanMonth, $month, $year],
            $letterSetting->format
        );

        // Signer: Wakil Dekan I (Bambang Tjahjo Utomo, MT)
        $viceDean = User::where('name', 'LIKE', '%Bambang Tjahjo Utomo%')->first();
        $defaultSignatoryTitle = 'Wakil Dekan Fakultas Ilmu Komputer Universitas Subang';
        $defaultSignatoryName = $viceDean ? $viceDean->name : 'BAMBANG TJAHJO UTOMO, MT';
        $defaultSignatoryIdentifier = $viceDean ? $viceDean->identifier : '0413056812';

        // Query supervised theses for this lecturer with status filter
        $statusFilter = $request->input('status_filter', 'active'); // 'active' (default: belum lulus), 'all', 'completed'

        $baseThesesQuery = Thesis::with(['student', 'graduation'])
            ->where(function ($q) use ($targetDosen) {
                $q->where('pembimbing1_id', $targetDosen->id)
                  ->orWhere('pembimbing2_id', $targetDosen->id);
            });

        // Compute counts for UI badges
        $activeCount = (clone $baseThesesQuery)->activeMentoring()->count();
        $graduatedCount = (clone $baseThesesQuery)->graduated()->count();
        $totalSupervisedCount = (clone $baseThesesQuery)->whereIn('status', ['active', 'completed'])->count();

        // Apply filter: by default, only active (non-graduated) students are listed!
        $thesesQuery = clone $baseThesesQuery;
        if ($statusFilter === 'active') {
            $thesesQuery->activeMentoring();
        } elseif ($statusFilter === 'completed') {
            $thesesQuery->graduated();
        } else {
            $thesesQuery->whereIn('status', ['active', 'completed']);
        }

        $allTheses = $thesesQuery->orderBy('id', 'desc')->get();

        $p1Theses = $allTheses->where('pembimbing1_id', $targetDosen->id);
        $p2Theses = $allTheses->where('pembimbing2_id', $targetDosen->id);

        // History of issued Surat Tugas for this lecturer
        $history = AdvisorDecree::where('target_type', 'individual_dosen')
            ->where('dosen_id', $targetDosen->id)
            ->orderBy('id', 'desc')
            ->get();

        $dosens = $isStaff ? User::where('role', 'dosen')->orderBy('name')->get() : collect([$user]);

        return view('documents.advisor_decrees.surat_tugas', compact(
            'isStaff',
            'targetDosen',
            'dosens',
            'defaultAcademicYear',
            'defaultSemester',
            'academicYear',
            'semester',
            'previewLetterNumber',
            'defaultSignatoryTitle',
            'defaultSignatoryName',
            'defaultSignatoryIdentifier',
            'statusFilter',
            'activeCount',
            'graduatedCount',
            'totalSupervisedCount',
            'allTheses',
            'p1Theses',
            'p2Theses',
            'history'
        ));
    }

    /**
     * Process generation of Surat Tugas and immediately stream PDF or redirect.
     */
    public function generateSuratTugas(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi', 'dosen'])) {
            abort(403);
        }

        $isStaff = in_array($user->role, ['admin', 'kaprodi']);
        $targetDosenId = ($isStaff && $request->filled('dosen_id')) ? $request->dosen_id : $user->id;
        $targetDosen = User::findOrFail($targetDosenId);

        $request->validate([
            'academic_year' => 'required|string|max:50',
            'semester' => 'required|string|in:Ganjil,Genap',
            'decree_date' => 'required|date',
            'selected_theses' => 'required|array|min:1',
            'selected_theses.*' => 'exists:theses,id',
        ], [
            'selected_theses.required' => 'Pilih minimal satu mahasiswa bimbingan untuk dicantumkan dalam Surat Tugas.',
            'selected_theses.min' => 'Pilih minimal satu mahasiswa bimbingan.',
        ]);

        // Generate official number
        $letterSetting = LetterSetting::firstOrCreate(
            ['type' => 'surat_tugas_pembimbing'],
            [
                'title' => 'Surat Tugas Dosen Pembimbing (BKD)',
                'format' => '[NUMBER]/PD.1.2/FIK-US/[ROMAN_MONTH]/[YEAR]',
                'last_number' => 460,
            ]
        );

        if ($request->filled('custom_decree_number')) {
            $decreeNumber = trim($request->custom_decree_number);
        } else {
            $letterSetting->increment('last_number');
            $number = str_pad($letterSetting->last_number, 3, '0', STR_PAD_LEFT);
            $month = Carbon::parse($request->decree_date)->format('m');
            $year = Carbon::parse($request->decree_date)->format('Y');
            $romans = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $romanMonth = $romans[(int)$month] ?? 'I';

            $decreeNumber = str_replace(
                ['[NUMBER]', '[ROMAN_MONTH]', '[MONTH]', '[YEAR]'],
                [$number, $romanMonth, $month, $year],
                $letterSetting->format
            );
        }

        // Collect student records sorted: Pembimbing I first, then Pembimbing II
        $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->whereIn('id', $request->selected_theses)
            ->get();

        $p1Data = [];
        $p2Data = [];
        foreach ($theses as $thesis) {
            $isP1 = ($thesis->pembimbing1_id == $targetDosen->id);
            $row = [
                'thesis_id' => $thesis->id,
                'student_id' => $thesis->student_id,
                'student_name' => $thesis->student ? $thesis->student->name : '-',
                'student_npm' => $thesis->student ? ($thesis->student->identifier ?? '-') : '-',
                'student_cohort' => $thesis->student ? ($thesis->student->entry_year ?? '-') : '-',
                'title' => $thesis->display_title,
                'topic' => $thesis->topic ?: '-',
                'role_label' => $isP1 ? 'Pembimbing I' : 'Pembimbing II',
                'pembimbing1_id' => $thesis->pembimbing1_id,
                'pembimbing1_name' => $thesis->pembimbing1 ? $thesis->pembimbing1->name : '-',
                'pembimbing1_nidn' => $thesis->pembimbing1 ? ($thesis->pembimbing1->identifier ?? '-') : '-',
                'pembimbing2_id' => $thesis->pembimbing2_id,
                'pembimbing2_name' => $thesis->pembimbing2 ? $thesis->pembimbing2->name : '-',
                'pembimbing2_nidn' => $thesis->pembimbing2 ? ($thesis->pembimbing2->identifier ?? '-') : '-',
            ];

            if ($isP1) {
                $p1Data[] = $row;
            } else {
                $p2Data[] = $row;
            }
        }
        $thesesData = array_merge($p1Data, $p2Data);

        $verificationToken = Str::random(32) . time();
        $viceDean = User::where('name', 'LIKE', '%Bambang Tjahjo Utomo%')->first();

        $decree = AdvisorDecree::create([
            'decree_number' => $decreeNumber,
            'title' => 'Surat Tugas Pembimbingan Skripsi Mahasiswa',
            'academic_year' => $request->academic_year,
            'semester' => $request->semester,
            'target_type' => 'individual_dosen',
            'dosen_id' => $targetDosen->id,
            'wave_id' => null,
            'decree_date' => $request->decree_date,
            'signatory_title' => $request->signatory_title ?? 'Wakil Dekan Fakultas Ilmu Komputer Universitas Subang',
            'signatory_name' => $request->signatory_name ?? ($viceDean ? $viceDean->name : 'BAMBANG TJAHJO UTOMO, MT'),
            'signatory_identifier' => $request->signatory_identifier ?? ($viceDean ? $viceDean->identifier : '0413056812'),
            'signer_user_id' => $viceDean ? $viceDean->id : null,
            'theses_data' => $thesesData,
            'total_students' => count($thesesData),
            'verification_token' => $verificationToken,
            'created_by' => $user->id,
            'notes' => 'Diterbitkan mandiri untuk pelaporan BKD / SISTER.',
        ]);

        ActivityLog::log(
            'Penerbitan Surat Tugas Pembimbing',
            "{$user->name} menerbitkan Surat Tugas No. {$decreeNumber} untuk {$targetDosen->name}.",
            'Dokumen & SK',
            $decree
        );

        if ($request->has('print_direct')) {
            return redirect()->route('advisor-decrees.pdf', $decree);
        }

        return redirect()->route('advisor-decrees.show', $decree)
            ->with('success', "Surat Tugas No. {$decreeNumber} berhasil diterbitkan dan siap dicetak/diunduh.");
    }

    /**
     * Generate official PDF document for the decree or duty letter.
     */
    public function pdf(AdvisorDecree $advisorDecree)
    {
        $user = Auth::user();
        $isStaff = in_array($user->role, ['admin', 'kaprodi']);

        if (!$isStaff && $user->role === 'dosen') {
            $isIncluded = $advisorDecree->target_type === 'collective'
                || $advisorDecree->dosen_id === $user->id
                || str_contains(json_encode($advisorDecree->theses_data), $user->name);

            if (!$isIncluded) {
                abort(403);
            }
        }

        $advisorDecree->load(['creator', 'signer', 'dosen', 'wave']);
        $signerUser = $advisorDecree->signer ?? User::where('role', 'kaprodi')->first();

        // Render Surat Tugas Template (persis foto referensi 100%)
        if ($advisorDecree->target_type === 'individual_dosen') {
            $pdf = Pdf::loadView('documents.advisor_decrees.pdf_surat_tugas', compact('advisorDecree', 'signerUser'))
                ->setPaper('a4', 'portrait');

            $cleanNumber = Str::slug($advisorDecree->decree_number);
            $fileName = "Surat_Tugas_Pembimbing_{$cleanNumber}.pdf";

            return $pdf->stream($fileName);
        }

        // Render Surat Keputusan Dekan Kolektif Template
        $pdf = Pdf::loadView('documents.advisor_decrees.pdf', compact('advisorDecree', 'signerUser'))
            ->setPaper('a4', 'portrait');

        $cleanNumber = Str::slug($advisorDecree->decree_number);
        $fileName = "SK_Pembimbing_{$cleanNumber}.pdf";

        return $pdf->stream($fileName);
    }

    /**
     * Public verification endpoint for QR code scan.
     */
    public function verify($token)
    {
        $decree = AdvisorDecree::with(['creator', 'signer', 'dosen', 'wave'])
            ->where('verification_token', $token)
            ->first();

        if (!$decree) {
            return view('verification.invalid');
        }

        return view('verification.advisor_decree', compact('decree'));
    }

    /**
     * Remove the specified decree.
     */
    public function destroy(AdvisorDecree $advisorDecree)
    {
        $user = Auth::user();
        $isOwner = ($advisorDecree->target_type === 'individual_dosen' && $advisorDecree->dosen_id === $user->id);
        if (!in_array($user->role, ['admin', 'kaprodi']) && !$isOwner) {
            abort(403);
        }

        $decreeNumber = $advisorDecree->decree_number;
        $isIndividual = ($advisorDecree->target_type === 'individual_dosen');
        $advisorDecree->delete();

        ActivityLog::log(
            'Penghapusan ' . ($isIndividual ? 'Surat Tugas Pembimbing' : 'SK Pembimbing'),
            "{$user->name} menghapus arsip " . ($isIndividual ? 'Surat Tugas' : 'SK Pembimbing') . " No. {$decreeNumber}.",
            'Dokumen & SK'
        );

        $redirectRoute = ($isIndividual && !in_array($user->role, ['admin', 'kaprodi']))
            ? route('advisor-decrees.surat-tugas')
            : route('advisor-decrees.index');

        return redirect($redirectRoute)
            ->with('success', "Arsip " . ($isIndividual ? 'Surat Tugas' : 'SK Pembimbing') . " No. {$decreeNumber} berhasil dihapus.");
    }
}

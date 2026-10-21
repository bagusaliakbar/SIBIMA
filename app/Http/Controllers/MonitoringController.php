<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use App\Models\Wave;
use App\Models\User;
use App\Models\SeminarScheduleDetail;
use App\Models\ThesisDefenseScheduleDetail;
use App\Models\MentoringSession;
use App\Services\MonitoringService;
use App\Exports\MonitoringExport;
use App\Exports\DefenseScoresExport;
use App\Exports\WeeklyMentoringExport;
use App\Exports\UncompletedMentoringExport;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MonitoringController extends Controller implements HasMiddleware
{
    protected $monitoringService;

    public function __construct(MonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'kaprodi') {
                    abort(403);
                }
                return $next($request);
            }),
        ];
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $pembimbingId = $request->input('pembimbing_id');
        $entryYear = $request->input('entry_year');
        $accStatus = $request->input('acc_status', 'all');
        $graduationStatus = $request->input('graduation_status', 'active');
        
        $baseGlobalQuery = Thesis::query()
            ->when($pembimbingId, function($query, $pembimbingId) {
                return $query->where(function($q) use ($pembimbingId) {
                    $q->where('pembimbing1_id', $pembimbingId)
                      ->orWhere('pembimbing2_id', $pembimbingId);
                });
            })
            ->when($entryYear, function($query, $entryYear) {
                return $query->whereHas('student', function($q) use ($entryYear) {
                    $q->where('entry_year', $entryYear);
                });
            })
            ->when($search, function($query, $search) {
                return $query->search($search);
            });

        $graduationCounts = [
            'active' => (clone $baseGlobalQuery)->notGraduated()->count(),
            'graduated' => (clone $baseGlobalQuery)->graduated()->count(),
            'all' => (clone $baseGlobalQuery)->count(),
        ];

        $baseCountQuery = (clone $baseGlobalQuery)
            ->when($graduationStatus === 'active', fn($q) => $q->notGraduated())
            ->when($graduationStatus === 'graduated', fn($q) => $q->graduated());

        $accCounts = [
            'all' => (clone $baseCountQuery)->count(),
            'ready_seminar' => (clone $baseCountQuery)->accStatus('ready_seminar')->count(),
            'ready_defense' => (clone $baseCountQuery)->accStatus('ready_defense')->count(),
            'partial_acc' => (clone $baseCountQuery)->accStatus('partial_acc')->count(),
            'in_mentoring' => (clone $baseCountQuery)->accStatus('in_mentoring')->count(),
        ];

        $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2', 'graduation'])
            ->withMentoringCounts()
            ->when($graduationStatus === 'active', fn($q) => $q->notGraduated())
            ->when($graduationStatus === 'graduated', fn($q) => $q->graduated())
            ->search($search)
            ->when($pembimbingId, function($query, $pembimbingId) {
                return $query->where(function($q) use ($pembimbingId) {
                    $q->where('pembimbing1_id', $pembimbingId)
                      ->orWhere('pembimbing2_id', $pembimbingId);
                });
            })
            ->when($entryYear, function($query, $entryYear) {
                return $query->whereHas('student', function($q) use ($entryYear) {
                    $q->where('entry_year', $entryYear);
                });
            })
            ->when($accStatus && $accStatus !== 'all', function($query) use ($accStatus) {
                return $query->accStatus($accStatus);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends([
                'search' => $search, 
                'pembimbing_id' => $pembimbingId, 
                'entry_year' => $entryYear,
                'acc_status' => $accStatus,
                'graduation_status' => $graduationStatus,
            ]);

        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        $entryYears = User::where('role', 'mahasiswa')->whereNotNull('entry_year')->distinct()->orderBy('entry_year', 'desc')->pluck('entry_year');

        // Chart Data Calculation
        $chartDosens = User::where('role', 'dosen')
            ->when($pembimbingId, function($q) use ($pembimbingId) {
                return $q->where('id', $pembimbingId);
            })
            ->orderBy('name')
            ->get();

        $chartLabels = [];
        $dataProposal = [];
        $dataPenelitian = [];
        $dataSiapSidang = [];
        $dataKritikal = [];

        $allChartTheses = (clone $baseCountQuery)
            ->with(['student', 'graduation'])
            ->get();

        foreach ($chartDosens as $dosen) {
            $thesesQuery = $allChartTheses->filter(function($t) use ($dosen) {
                return $t->pembimbing1_id == $dosen->id || $t->pembimbing2_id == $dosen->id;
            });

            if ($thesesQuery->count() > 0 || $pembimbingId) {
                // Shorten name for chart label
                $nameParts = explode(' ', $dosen->name);
                $shortName = count($nameParts) > 2 ? $nameParts[0] . ' ' . $nameParts[1] . '...' : $dosen->name;
                $chartLabels[] = $shortName;

                $proposal = 0;
                $penelitian = 0;
                $siapSidang = 0;
                $kritikal = 0;

                foreach ($thesesQuery as $thesis) {
                    if ($thesis->student && $thesis->student->current_semester >= 13) {
                        $kritikal++;
                    } elseif ($thesis->isAccSidangFinal() || $thesis->isGraduated()) {
                        $siapSidang++;
                    } elseif ($thesis->isAccUpFinal()) {
                        $penelitian++;
                    } else {
                        $proposal++;
                    }
                }

                $dataProposal[] = $proposal;
                $dataPenelitian[] = $penelitian;
                $dataSiapSidang[] = $siapSidang;
                $dataKritikal[] = $kritikal;
            }
        }

        $chartData = [
            'labels' => $chartLabels,
            'datasets' => [
                ['label' => 'Belum Seminar', 'data' => $dataProposal, 'backgroundColor' => '#3b82f6', 'borderRadius' => 4],
                ['label' => 'Seminar', 'data' => $dataPenelitian, 'backgroundColor' => '#f97316', 'borderRadius' => 4],
                ['label' => 'Sidang Akhir', 'data' => $dataSiapSidang, 'backgroundColor' => '#10b981', 'borderRadius' => 4],
                ['label' => 'Kritikal (Sem >= 13)', 'data' => $dataKritikal, 'backgroundColor' => '#ef4444', 'borderRadius' => 4],
            ]
        ];

        return view('monitoring.index', compact('theses', 'search', 'dosens', 'pembimbingId', 'entryYears', 'entryYear', 'chartData', 'accStatus', 'accCounts', 'graduationStatus', 'graduationCounts'));
    }

    public function export(Request $request)
    {
        $type = $request->input('type', 'akademik');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $format = $request->input('format', 'excel');
        $accStatus = $request->input('acc_status', 'all');
        $graduationStatus = $request->input('graduation_status', 'active');
        $pembimbingId = $request->input('pembimbing_id');
        $entryYear = $request->input('entry_year');
        $waveId = $request->input('wave_id');
        $search = $request->input('search');

        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();
        $waveName = 'Semua Gelombang';
        if ($waveId) {
            $w = Wave::find($waveId);
            if ($w) $waveName = $w->name;
        }

        $pembimbingName = 'Semua Pembimbing';
        if ($pembimbingId) {
            $p = User::find($pembimbingId);
            if ($p) $pembimbingName = $p->name;
        }

        switch ($type) {
            case 'nilai_sidang':
                $query = ThesisDefenseScheduleDetail::with([
                    'thesis.student', 
                    'thesis.pembimbing1', 
                    'thesis.pembimbing2', 
                    'schedule.wave', 
                    'examiner1', 
                    'examiner2', 
                    'revisions'
                ])->whereHas('thesis');

                if ($waveId) {
                    $query->whereHas('schedule', fn($q) => $q->where('wave_id', $waveId));
                }
                if ($entryYear) {
                    $query->whereHas('thesis.student', fn($q) => $q->where('entry_year', $entryYear));
                }
                if ($pembimbingId) {
                    $query->whereHas('thesis', function($q) use ($pembimbingId) {
                        $q->where('pembimbing1_id', $pembimbingId)->orWhere('pembimbing2_id', $pembimbingId);
                    });
                }
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
                if ($search) {
                    $query->whereHas('thesis.student', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")->orWhere('identifier', 'like', "%{$search}%");
                    });
                }
                $data = $query->orderBy('id', 'desc')->get();
                break;

            case 'dosen':
            case 'beban_dosen':
                $type = 'beban_dosen';
                $query = User::where('role', 'dosen')->withCount(['thesesAsP1', 'thesesAsP2']);
                if ($pembimbingId) {
                    $query->where('id', $pembimbingId);
                }
                if ($search) {
                    $query->where('name', 'like', "%{$search}%");
                }
                $data = $query->orderBy('name')->get();
                $data->each(function($dosen) use ($waveId, $startDate, $endDate) {
                    $semQuery = SeminarScheduleDetail::where(fn($q) => $q->where('examiner1_id', $dosen->id)->orWhere('examiner2_id', $dosen->id));
                    if ($waveId) $semQuery->whereHas('schedule', fn($q) => $q->where('wave_id', $waveId));
                    if ($startDate && $endDate) $semQuery->whereBetween('created_at', [$startDate, $endDate]);
                    $dosen->seminar_examiner_count = $semQuery->count();

                    $defQuery = ThesisDefenseScheduleDetail::where(fn($q) => $q->where('examiner1_id', $dosen->id)->orWhere('examiner2_id', $dosen->id));
                    if ($waveId) $defQuery->whereHas('schedule', fn($q) => $q->where('wave_id', $waveId));
                    if ($startDate && $endDate) $defQuery->whereBetween('created_at', [$startDate, $endDate]);
                    $dosen->defense_examiner_count = $defQuery->count();

                    $dosen->total_workload = ($dosen->theses_as_p1_count ?? 0) + ($dosen->theses_as_p2_count ?? 0) + $dosen->seminar_examiner_count + $dosen->defense_examiner_count;
                });
                break;

            case 'kepatuhan':
                $query = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
                    ->where('status', 'active')
                    ->withMentoringCounts();
                if ($pembimbingId) {
                    $query->where(fn($q) => $q->where('pembimbing1_id', $pembimbingId)->orWhere('pembimbing2_id', $pembimbingId));
                }
                if ($entryYear) {
                    $query->whereHas('student', fn($q) => $q->where('entry_year', $entryYear));
                }
                if ($search) {
                    $query->search($search);
                }
                $data = $query->get()->sortByDesc(fn($t) => $t->days_since_last_mentoring ?? 0)->values();
                break;

            case 'topik':
                $query = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])->whereNotNull('title');
                if ($graduationStatus === 'graduated') {
                    $query->graduated();
                } elseif ($graduationStatus === 'active') {
                    $query->notGraduated();
                }
                if ($entryYear) {
                    $query->whereHas('student', fn($q) => $q->where('entry_year', $entryYear));
                }
                if ($pembimbingId) {
                    $query->where(fn($q) => $q->where('pembimbing1_id', $pembimbingId)->orWhere('pembimbing2_id', $pembimbingId));
                }
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
                if ($search) {
                    $query->search($search);
                }
                $data = $query->orderBy('topic')->orderBy('created_at', 'desc')->get();
                break;

            case 'waktu':
                $waves = Wave::with(['defenseApplications.thesis' => function($q) {
                    $q->where('status', 'completed');
                }])->get();
                $data = collect();
                foreach ($waves as $wave) {
                    $completed = $wave->defenseApplications->map(fn($app) => $app->thesis)->filter(fn($t) => $t && $t->status === 'completed');
                    $count = $completed->count();
                    $avgMonths = 0; $onTimeCount = 0; $lateCount = 0;
                    if ($count > 0) {
                        $totalDays = $completed->sum(fn($t) => $t->created_at->diffInDays($t->updated_at));
                        $avgMonths = round(($totalDays / $count) / 30, 1);
                        foreach ($completed as $t) {
                            if ($t->student && $t->student->entry_year) {
                                ($t->updated_at->year - $t->student->entry_year <= 4) ? $onTimeCount++ : $lateCount++;
                            }
                        }
                    }
                    $data->push((object)[
                        'wave_name' => $wave->name,
                        'period' => ($wave->start_date?->format('d/m/Y') ?? '-') . ' s/d ' . ($wave->end_date?->format('d/m/Y') ?? '-'),
                        'completed_count' => $count,
                        'avg_duration_months' => $avgMonths,
                        'on_time_count' => $onTimeCount,
                        'late_count' => $lateCount,
                        'on_time_percent' => $count > 0 ? round(($onTimeCount / $count) * 100, 1) . '%' : '0%',
                    ]);
                }
                break;

            case 'kelulusan':
                $query = Thesis::with(['student', 'pembimbing1', 'pembimbing2', 'graduation'])->graduated();
                if ($startDate && $endDate) $query->whereBetween('updated_at', [$startDate, $endDate]);
                if ($pembimbingId) $query->where(fn($q) => $q->where('pembimbing1_id', $pembimbingId)->orWhere('pembimbing2_id', $pembimbingId));
                if ($entryYear) $query->whereHas('student', fn($q) => $q->where('entry_year', $entryYear));
                if ($search) $query->search($search);
                $data = $query->orderBy('updated_at', 'desc')->get();
                break;

            case 'logs':
                $data = \App\Models\ActivityLog::with('user')
                    ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
                    ->when($search, fn($q) => $q->where('description', 'like', "%{$search}%"))
                    ->latest()->get();
                break;

            case 'mahasiswa':
            case 'akademik':
            default:
                $type = 'akademik';
                $query = Thesis::with(['student', 'pembimbing1', 'pembimbing2', 'graduation'])->withMentoringCounts();
                if ($graduationStatus === 'active') {
                    $query->notGraduated();
                } elseif ($graduationStatus === 'graduated') {
                    $query->graduated();
                }
                if ($startDate && $endDate) $query->whereBetween('created_at', [$startDate, $endDate]);
                if ($pembimbingId) $query->where(fn($q) => $q->where('pembimbing1_id', $pembimbingId)->orWhere('pembimbing2_id', $pembimbingId));
                if ($entryYear) $query->whereHas('student', fn($q) => $q->where('entry_year', $entryYear));
                if (!empty($accStatus) && $accStatus !== 'all') $query->accStatus($accStatus);
                if ($search) $query->search($search);
                $data = $query->orderBy('created_at', 'desc')->get();
                break;
        }

        if ($format === 'pdf') {
            $viewName = 'monitoring.reports.' . $type . '_pdf';
            if (!view()->exists($viewName)) {
                $viewName = 'monitoring.reports.akademik_pdf';
            }

            $pdf = Pdf::loadView($viewName, compact(
                'data', 
                'startDate', 
                'endDate', 
                'waveName', 
                'pembimbingName', 
                'entryYear', 
                'graduationStatus', 
                'kaprodi'
            ))->setPaper('a4', 'landscape');

            return $pdf->download('Laporan_' . ucfirst($type) . '_' . now()->format('Ymd') . '.pdf');
        }

        return Excel::download(
            new MonitoringExport($type, $startDate, $endDate, $accStatus, $pembimbingId, $entryYear, $search, $graduationStatus, $waveId),
            'Laporan_' . ucfirst($type) . '_' . now()->format('Ymd') . '.xlsx'
        );
    }

    public function revisions(Request $request)
    {
        $search = $request->input('search');
        [$activeWave, $selectedWaveId] = $this->monitoringService->getActiveWave($request->input('wave_id'));

        $seminarDetails = SeminarScheduleDetail::with(['thesis.student', 'schedule', 'examiner1', 'examiner2', 'revisions.messages'])
            ->whereHas('thesis')
            ->when($selectedWaveId, function($q) use ($selectedWaveId) {
                $q->whereHas('thesis.seminarApplication', function($query) use ($selectedWaveId) {
                    $query->where('wave_id', $selectedWaveId);
                });
            })
            ->when($search, function ($query, $search) {
                $query->whereHas('thesis.student', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('identifier', 'like', "%{$search}%");
                });
            })
            ->join('seminar_schedules', 'seminar_schedule_details.seminar_schedule_id', '=', 'seminar_schedules.id')
            ->orderBy('seminar_schedules.date', 'desc')
            ->select('seminar_schedule_details.*')
            ->paginate(15)
            ->appends(['search' => $search, 'wave_id' => $selectedWaveId]);

        $waves = Wave::orderBy('created_at', 'desc')->get();

        return view('monitoring.revisions', compact('seminarDetails', 'search', 'waves', 'selectedWaveId', 'activeWave'));
    }

    public function defenseRevisions(Request $request)
    {
        $search = $request->input('search');
        [$activeWave, $selectedWaveId] = $this->monitoringService->getActiveWave($request->input('wave_id'));

        $defenseDetails = ThesisDefenseScheduleDetail::with(['thesis.student', 'thesis.pembimbing1', 'schedule', 'examiner1', 'examiner2', 'revisions.messages'])
            ->whereHas('thesis')
            ->when($selectedWaveId, function($q) use ($selectedWaveId) {
                $q->whereHas('thesis.defenseApplication', function($query) use ($selectedWaveId) {
                    $query->where('wave_id', $selectedWaveId);
                });
            })
            ->when($search, function ($query, $search) {
                $query->whereHas('thesis.student', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('identifier', 'like', "%{$search}%");
                });
            })
            ->join('thesis_defense_schedules', 'thesis_defense_schedule_details.thesis_defense_schedule_id', '=', 'thesis_defense_schedules.id')
            ->orderBy('thesis_defense_schedules.date', 'desc')
            ->select('thesis_defense_schedule_details.*')
            ->paginate(15)
            ->appends(['search' => $search, 'wave_id' => $selectedWaveId]);

        $waves = Wave::orderBy('created_at', 'desc')->get();

        return view('monitoring.defense_revisions', compact('defenseDetails', 'search', 'waves', 'selectedWaveId', 'activeWave'));
    }

    public function defenseScores(Request $request)
    {
        $search = $request->input('search');
        [$activeWave, $selectedWaveId] = $this->monitoringService->getActiveWave($request->input('wave_id'));

        $defenseDetails = ThesisDefenseScheduleDetail::with(['thesis.student', 'thesis.pembimbing1', 'schedule', 'examiner1', 'examiner2', 'revisions'])
            ->whereHas('thesis')
            ->when($selectedWaveId, function($q) use ($selectedWaveId) {
                $q->whereHas('thesis.defenseApplication', function($query) use ($selectedWaveId) {
                    $query->where('wave_id', $selectedWaveId);
                });
            })
            ->when($search, function ($query, $search) {
                $query->whereHas('thesis.student', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('identifier', 'like', "%{$search}%");
                });
            })
            ->join('thesis_defense_schedules', 'thesis_defense_schedule_details.thesis_defense_schedule_id', '=', 'thesis_defense_schedules.id')
            ->orderBy('thesis_defense_schedules.date', 'desc')
            ->select('thesis_defense_schedule_details.*')
            ->paginate(15)
            ->appends(['search' => $search, 'wave_id' => $selectedWaveId]);

        $waves = Wave::orderBy('created_at', 'desc')->get();

        return view('monitoring.defense_scores', compact('defenseDetails', 'search', 'waves', 'selectedWaveId', 'activeWave'));
    }

    public function exportDefenseScoresExcel(Request $request)
    {
        $waveId = $request->input('wave_id');
        $wave = $waveId ? Wave::find($waveId) : null;
        $waveName = $wave ? str_replace([' ', '/', '\\'], '_', $wave->name) : 'Semua_Gelombang';
        $fileName = 'Rekap_Nilai_Sidang_' . $waveName . '_' . now()->format('Ymd') . '.xlsx';

        return Excel::download(new DefenseScoresExport($waveId), $fileName);
    }

    public function exportDefenseScoresPdf(Request $request)
    {
        $waveId = $request->input('wave_id');
        $wave = $waveId ? Wave::find($waveId) : null;

        $defenseDetails = ThesisDefenseScheduleDetail::with(['thesis.student', 'thesis.pembimbing1', 'schedule', 'examiner1', 'examiner2', 'revisions'])
            ->whereHas('thesis')
            ->when($waveId, function($q) use ($waveId) {
                $q->whereHas('thesis.defenseApplication', function($query) use ($waveId) {
                    $query->where('wave_id', $waveId);
                });
            })
            ->get();

        $pdf = Pdf::loadView('monitoring.defense_scores_pdf', compact('defenseDetails', 'wave'))
                  ->setPaper('a4', 'landscape');

        $waveName = $wave ? str_replace([' ', '/', '\\'], '_', $wave->name) : 'Semua_Gelombang';
        return $pdf->download('Rekap_Nilai_Sidang_' . $waveName . '.pdf');
    }

    public function exportBeritaAcara(ThesisDefenseScheduleDetail $detail)
    {
        $scoresData = $this->monitoringService->calculateDefenseScores($detail);

        $pdf = Pdf::loadView('monitoring.berita_acara_pdf', array_merge(['detail' => $detail], $scoresData));

        $fileName = 'Berita_Acara_Sidang_' . str_replace(' ', '_', $detail->thesis->student->name) . '.pdf';
        return $pdf->download($fileName);
    }

    public function exportBeritaAcaraSeminar(SeminarScheduleDetail $detail)
    {
        $scoresData = $this->monitoringService->calculateSeminarScores($detail);

        $pdf = Pdf::loadView('monitoring.berita_acara_seminar_pdf', array_merge(['detail' => $detail], $scoresData));

        $fileName = 'Berita_Acara_Seminar_' . str_replace(' ', '_', $detail->thesis->student->name) . '.pdf';
        return $pdf->download($fileName);
    }


    public function criticalStudents(Request $request)
    {
        $search = $request->input('search');
        $pembimbingId = $request->input('pembimbing_id');

        $students = $this->monitoringService->getCriticalStudentsQuery($search, $pembimbingId)
            ->paginate(15)
            ->appends(['search' => $search, 'pembimbing_id' => $pembimbingId]);

        $dosens = User::where('role', 'dosen')->orderBy('name')->get();

        return view('monitoring.critical', compact('students', 'search', 'dosens', 'pembimbingId'));
    }

    public function batchExportBeritaAcara(Request $request)
    {
        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        $waveId = $request->input('wave_id');
        $category = $request->input('category', 'defense'); // seminar or defense

        if ($waveId && (!$ids || count($ids) === 0)) {
            // Fetch all IDs in wave
            if ($category === 'defense') {
                $ids = ThesisDefenseScheduleDetail::whereHas('thesis.defenseApplication', function($q) use ($waveId) {
                    $q->where('wave_id', $waveId);
                })->pluck('id')->toArray();
            } else {
                $ids = SeminarScheduleDetail::whereHas('thesis.seminarApplication', function($q) use ($waveId) {
                    $q->where('wave_id', $waveId);
                })->pluck('id')->toArray();
            }
        }

        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal satu data mahasiswa untuk diekspor.');
        }

        $zipName = 'Berita_Acara_' . ucfirst($category) . '_' . now()->format('YmdHis') . '.zip';
        $zipPath = storage_path('app/public/' . $zipName);
        $zip = new \ZipArchive;

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($ids as $id) {
                if ($category === 'defense') {
                    $detail = ThesisDefenseScheduleDetail::with(['thesis.student', 'thesis.pembimbing1', 'schedule', 'examiner1', 'examiner2'])->find($id);
                    if ($detail) {
                        $scoresData = $this->monitoringService->calculateDefenseScores($detail);
                        $pdf = Pdf::loadView('monitoring.berita_acara_pdf', array_merge(['detail' => $detail], $scoresData));
                        $fileName = 'Berita_Acara_Sidang_' . str_replace([' ', '/', '\\'], '_', $detail->thesis->student->name) . '_' . $detail->id . '.pdf';
                        $zip->addFromString($fileName, $pdf->output());
                    }
                } else {
                    $detail = SeminarScheduleDetail::with(['thesis.student', 'schedule', 'examiner1', 'examiner2'])->find($id);
                    if ($detail) {
                        $scoresData = $this->monitoringService->calculateSeminarScores($detail);
                        $pdf = Pdf::loadView('monitoring.berita_acara_seminar_pdf', array_merge(['detail' => $detail], $scoresData));
                        $fileName = 'Berita_Acara_Seminar_' . str_replace([' ', '/', '\\'], '_', $detail->thesis->student->name) . '_' . $detail->id . '.pdf';
                        $zip->addFromString($fileName, $pdf->output());
                    }
                }
            }
            $zip->close();
        }

        if (!file_exists($zipPath)) {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function printSeminarRevision(SeminarScheduleDetail $detail, Request $request)
    {
        $detail->load(['thesis.student', 'schedule', 'examiner1', 'examiner2', 'revisions.messages']);

        if (!$detail->thesis || !$detail->thesis->student) {
            return back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $examinerId = $request->input('examiner_id');
        $pdfData = $this->prepareRevisionPrintData($detail, 'seminar', $examinerId);

        $pdf = Pdf::loadView('monitoring.print_revision_seminar', $pdfData);

        $studentId = $detail->thesis->student->identifier ?? 'Mahasiswa';
        $studentName = str_replace([' ', '/', '\\'], '_', $detail->thesis->student->name ?? '');
        $suffix = $examinerId ? ($examinerId == $detail->examiner1_id ? '_P1' : '_P2') : '_Lengkap';
        $fileName = 'Pernyataan_Revisi_Seminar_' . $studentId . '_' . $studentName . $suffix . '.pdf';

        return $pdf->stream($fileName);
    }

    public function printDefenseRevision(ThesisDefenseScheduleDetail $detail, Request $request)
    {
        $detail->load(['thesis.student', 'schedule', 'examiner1', 'examiner2', 'revisions.messages']);

        if (!$detail->thesis || !$detail->thesis->student) {
            return back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $examinerId = $request->input('examiner_id');
        $pdfData = $this->prepareRevisionPrintData($detail, 'defense', $examinerId);

        $pdf = Pdf::loadView('monitoring.print_revision_defense', $pdfData);

        $studentId = $detail->thesis->student->identifier ?? 'Mahasiswa';
        $studentName = str_replace([' ', '/', '\\'], '_', $detail->thesis->student->name ?? '');
        $suffix = $examinerId ? ($examinerId == $detail->examiner1_id ? '_P1' : '_P2') : '_Lengkap';
        $fileName = 'Pernyataan_Revisi_Sidang_' . $studentId . '_' . $studentName . $suffix . '.pdf';

        return $pdf->stream($fileName);
    }

    public function batchExportRevisions(Request $request)
    {
        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }
        $waveId = $request->input('wave_id');
        $category = $request->input('category', 'defense'); // 'seminar' or 'defense'

        if ($waveId && (!$ids || count($ids) === 0)) {
            if ($category === 'defense') {
                $ids = ThesisDefenseScheduleDetail::whereHas('thesis.defenseApplication', function($q) use ($waveId) {
                    $q->where('wave_id', $waveId);
                })->pluck('id')->toArray();
            } else {
                $ids = SeminarScheduleDetail::whereHas('thesis.seminarApplication', function($q) use ($waveId) {
                    $q->where('wave_id', $waveId);
                })->pluck('id')->toArray();
            }
        }

        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal satu data mahasiswa untuk diekspor.');
        }

        $zipName = 'Lembar_Revisi_' . ucfirst($category) . '_' . now()->format('YmdHis') . '.zip';
        $zipPath = storage_path('app/public/' . $zipName);
        $zip = new \ZipArchive;

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($ids as $id) {
                if ($category === 'defense') {
                    $detail = ThesisDefenseScheduleDetail::with(['thesis.student', 'schedule', 'examiner1', 'examiner2', 'revisions.messages'])->find($id);
                    if ($detail && $detail->thesis && $detail->thesis->student) {
                        $pdfData = $this->prepareRevisionPrintData($detail, 'defense');
                        $pdf = Pdf::loadView('monitoring.print_revision_defense', $pdfData);
                        $fileName = 'Lembar_Revisi_Sidang_' . str_replace([' ', '/', '\\'], '_', $detail->thesis->student->identifier . '_' . $detail->thesis->student->name) . '.pdf';
                        $zip->addFromString($fileName, $pdf->output());
                    }
                } else {
                    $detail = SeminarScheduleDetail::with(['thesis.student', 'schedule', 'examiner1', 'examiner2', 'revisions.messages'])->find($id);
                    if ($detail && $detail->thesis && $detail->thesis->student) {
                        $pdfData = $this->prepareRevisionPrintData($detail, 'seminar');
                        $pdf = Pdf::loadView('monitoring.print_revision_seminar', $pdfData);
                        $fileName = 'Lembar_Revisi_Seminar_' . str_replace([' ', '/', '\\'], '_', $detail->thesis->student->identifier . '_' . $detail->thesis->student->name) . '.pdf';
                        $zip->addFromString($fileName, $pdf->output());
                    }
                }
            }
            $zip->close();
        }

        if (!file_exists($zipPath)) {
            return back()->with('error', 'Gagal membuat file ZIP revisi.');
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    protected function prepareRevisionPrintData($detail, string $type, $examinerId = null): array
    {
        $items = [];
        $examinerList = [];

        if ($examinerId) {
            if ($detail->examiner1_id == $examinerId) {
                $examinerList[] = ['examiner' => $detail->examiner1, 'role_label' => 'Penguji 1'];
            } elseif ($detail->examiner2_id == $examinerId) {
                $examinerList[] = ['examiner' => $detail->examiner2, 'role_label' => 'Penguji 2'];
            }
        } else {
            if ($detail->examiner1) {
                $examinerList[] = ['examiner' => $detail->examiner1, 'role_label' => 'Penguji 1'];
            }
            if ($detail->examiner2) {
                $examinerList[] = ['examiner' => $detail->examiner2, 'role_label' => 'Penguji 2'];
            }
        }

        foreach ($examinerList as $ex) {
            $examiner = $ex['examiner'];
            $roleLabel = $ex['role_label'];
            $revision = $examiner ? $detail->getRevisionFor($examiner->id) : null;

            $rawMessage = '';
            if ($revision) {
                $firstMessage = $revision->messages->where('sender_id', $examiner->id)->first()
                    ?? $revision->messages->where('sender_id', '!=', $detail->thesis?->student_id)->first()
                    ?? $revision->messages->first();

                if ($firstMessage && !empty($firstMessage->message)) {
                    $rawMessage = (string) $firstMessage->message;
                } elseif (!empty($revision->revision_notes)) {
                    $rawMessage = (string) $revision->revision_notes;
                }
            }

            // Parse points
            $lines = explode("\n", str_replace("\r", "", $rawMessage));
            $parsedPoints = [];
            $parsedNotes = [];
            $isNumbered = false;
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed === '') continue;
                if (preg_match('/^(\d+)[\.\)]\s*(.+)$/u', $trimmed, $m)) {
                    $isNumbered = true;
                    $parsedPoints[] = ['num' => $m[1], 'text' => $m[2]];
                } elseif ($isNumbered && !empty($parsedPoints)) {
                    $parsedNotes[] = $trimmed;
                }
            }

            $items[] = [
                'examiner' => $examiner,
                'role_label' => $roleLabel,
                'revision' => $revision,
                'rawMessage' => $rawMessage,
                'parsedPoints' => $parsedPoints,
                'parsedNotes' => $parsedNotes,
                'isNumbered' => $isNumbered,
            ];
        }

        return [
            'detail' => $detail,
            'items' => $items,
            'type' => $type,
        ];
    }

    /**
     * Display weekly mentoring monitoring page for Kaprodi and Admin.
     */
    public function weekly(Request $request)
    {
        $dateParam = $request->input('date');
        $baseDate = $dateParam ? Carbon::parse($dateParam) : Carbon::now();

        $weekOffset = (int) $request->input('week_offset', 0);
        if ($weekOffset !== 0) {
            $baseDate = $baseDate->copy()->addWeeks($weekOffset);
        }

        $startDate = $baseDate->copy()->startOfWeek(CarbonInterface::MONDAY)->startOfDay();
        $endDate = $baseDate->copy()->endOfWeek(CarbonInterface::SUNDAY)->endOfDay();

        $filters = [
            'search' => $request->input('search'),
            'pembimbing_id' => $request->input('pembimbing_id'),
            'entry_year' => $request->input('entry_year'),
            'compliance_status' => $request->input('compliance_status'),
        ];

        $weeklyData = $this->monitoringService->getWeeklyMentoringData($startDate, $endDate, $filters);

        $theses = $weeklyData['paginated'];
        $stats = $weeklyData['stats'];

        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        $entryYears = User::where('role', 'mahasiswa')->whereNotNull('entry_year')->distinct()->orderBy('entry_year', 'desc')->pluck('entry_year');

        // Navigation dates
        $prevWeekDate = $startDate->copy()->subWeek()->format('Y-m-d');
        $nextWeekDate = $startDate->copy()->addWeek()->format('Y-m-d');
        $currentWeekDate = Carbon::now()->startOfWeek(CarbonInterface::MONDAY)->format('Y-m-d');
        $isCurrentWeek = $startDate->isSameDay(Carbon::now()->startOfWeek(CarbonInterface::MONDAY));

        return view('monitoring.weekly', compact(
            'theses',
            'stats',
            'dosens',
            'entryYears',
            'startDate',
            'endDate',
            'filters',
            'prevWeekDate',
            'nextWeekDate',
            'currentWeekDate',
            'isCurrentWeek'
        ));
    }

    /**
     * Send WhatsApp reminder to student who hasn't completed required weekly mentoring.
     */
    public function sendWeeklyReminder(Request $request, Thesis $thesis, WhatsAppService $whatsAppService)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfWeek(CarbonInterface::MONDAY);
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfWeek(CarbonInterface::SUNDAY);

        $student = $thesis->student;
        if (!$student || empty($student->phone)) {
            return back()->with('error', 'Nomor WhatsApp mahasiswa tidak ditemukan atau belum diatur di profil.');
        }

        // Count sessions for this week to produce accurate reminder copy
        $weekSessions = $thesis->mentoringSessions()
            ->where('status', 'completed')
            ->where('is_absent', false)
            ->whereBetween('scheduled_at', [$startDate, $endDate])
            ->get();

        $thesis->weekly_p1_count = $weekSessions->where('dosen_id', $thesis->pembimbing1_id)->count();
        $thesis->weekly_p2_count = $weekSessions->where('dosen_id', $thesis->pembimbing2_id)->count();

        $customMessage = $request->input('message');
        $message = $customMessage ?: $this->monitoringService->generateWeeklyReminderMessage($thesis, $startDate, $endDate);

        $sent = $whatsAppService->sendMessage($student->phone, $message);

        if ($sent) {
            return back()->with('success', "Pesan pengingat WhatsApp berhasil dikirim ke {$student->name} ({$student->phone}).");
        }

        return back()->with('warning', "Pesan tidak dapat terkirim otomatis melalui gateway WhatsApp (fitur WA mungkin belum aktif atau gateway sedang offline). Anda dapat menghubungi mahasiswa secara langsung melalui no: {$student->phone}.");
    }

    /**
     * Export weekly mentoring monitoring report to Excel.
     */
    public function exportWeeklyExcel(Request $request)
    {
        $dateParam = $request->input('date');
        $baseDate = $dateParam ? Carbon::parse($dateParam) : Carbon::now();

        $startDate = $baseDate->copy()->startOfWeek(CarbonInterface::MONDAY)->startOfDay();
        $endDate = $baseDate->copy()->endOfWeek(CarbonInterface::SUNDAY)->endOfDay();

        $filters = [
            'search' => $request->input('search'),
            'pembimbing_id' => $request->input('pembimbing_id'),
            'entry_year' => $request->input('entry_year'),
            'compliance_status' => $request->input('compliance_status'),
        ];

        $weeklyData = $this->monitoringService->getWeeklyMentoringData($startDate, $endDate, $filters);
        $fileName = 'Monitoring_Bimbingan_Mingguan_' . $startDate->format('Y-m-d') . '_sd_' . $endDate->format('Y-m-d') . '.xlsx';

        return Excel::download(new WeeklyMentoringExport($weeklyData['all'], $startDate, $endDate), $fileName);
    }

    /**
     * Display Mentoring Activity Leaderboard and Inactive Early Warning Radar.
     */
    public function activity(Request $request)
    {
        $filters = [
            'period' => $request->input('period', 'this_month'),
            'cohort' => $request->input('cohort'),
            'search' => $request->input('search'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        $activityData = $this->monitoringService->getMentoringActivityData($filters);

        $cohortYears = User::where('role', 'mahasiswa')
            ->whereNotNull('entry_year')
            ->distinct()
            ->orderBy('entry_year', 'desc')
            ->pluck('entry_year');

        $activeTab = $request->input('tab', 'mahasiswa'); // 'mahasiswa' or 'dosen'

        return view('monitoring.activity', array_merge($activityData, [
            'filters' => $filters,
            'cohortYears' => $cohortYears,
            'activeTab' => $activeTab,
        ]));
    }

    /**
     * Send WhatsApp reminder to inactive student or lecturer.
     */
    public function sendActivityReminder(Request $request, WhatsAppService $whatsAppService)
    {
        $type = $request->input('type'); // 'student' or 'dosen'
        $userId = $request->input('user_id');
        $user = User::findOrFail($userId);

        $phone = $user->phone_number ?? $user->phone;
        if (empty($phone)) {
            return back()->with('error', "Nomor WhatsApp {$user->name} tidak ditemukan atau belum diatur di profil.");
        }

        $thesis = null;
        if ($type === 'student' && $request->input('thesis_id')) {
            $thesis = Thesis::with(['pembimbing1', 'pembimbing2'])->find($request->input('thesis_id'));
        }

        $daysInactive = $request->input('days_inactive') ? (int) $request->input('days_inactive') : null;
        $customMessage = $request->input('message');
        $message = $customMessage ?: $this->monitoringService->generateActivityReminderMessage($type, $user, $thesis, $daysInactive);

        $sent = $whatsAppService->sendMessage($phone, $message);

        if ($sent) {
            return back()->with('success', "Pesan pengingat WhatsApp berhasil dikirim ke {$user->name} ({$phone}).");
        }

        return back()->with('warning', "Pesan tidak dapat terkirim otomatis melalui gateway WhatsApp (fitur WA mungkin belum aktif atau gateway sedang offline). Anda dapat menghubungi secara langsung melalui no: {$phone}.");
    }

    /**
     * Export Mentoring Activity Leaderboard & Radar to Excel.
     */
    public function exportActivityExcel(Request $request)
    {
        $filters = [
            'period' => $request->input('period', 'this_month'),
            'cohort' => $request->input('cohort'),
            'search' => $request->input('search'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        $activityData = $this->monitoringService->getMentoringActivityData($filters);
        $fileName = 'Radar_Keaktifan_Bimbingan_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new \App\Exports\MentoringActivityRankExport($activityData), $fileName);
    }

    /**
     * Export Mentoring Activity Leaderboard & Radar to PDF.
     */
    public function exportActivityPdf(Request $request)
    {
        $filters = [
            'period' => $request->input('period', 'this_month'),
            'cohort' => $request->input('cohort'),
            'search' => $request->input('search'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        $activityData = $this->monitoringService->getMentoringActivityData($filters);
        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();

        $pdf = Pdf::loadView('monitoring.activity_pdf', array_merge($activityData, [
            'kaprodi' => $kaprodi,
        ]))->setPaper('a4', 'landscape');

        $fileName = 'Radar_Keaktifan_Bimbingan_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Display list and monitoring of uncompleted mentoring sessions for Admin & Kaprodi.
     */
    public function uncompletedMentoring(Request $request)
    {
        $filters = [
            'scope' => $request->input('scope', 'overdue'), // 'overdue' (default: lewat hari), 'today' (hari ini), 'all' (semua)
            'dosen_id' => $request->input('dosen_id'),
            'search' => $request->input('search'),
            'view' => $request->input('view', 'dosen'), // 'dosen' (default) or 'session'
        ];

        $data = $this->monitoringService->getUncompletedMentoringData($filters);

        $dosens = User::whereIn('role', ['dosen', 'kaprodi'])
            ->orderBy('name', 'asc')
            ->get();

        return view('monitoring.uncompleted_mentoring', array_merge($data, [
            'filters' => $filters,
            'dosens' => $dosens,
        ]));
    }

    /**
     * Send WhatsApp reminder to a lecturer regarding their uncompleted mentoring sessions.
     */
    public function sendUncompletedMentoringReminder(Request $request, WhatsAppService $whatsAppService)
    {
        $dosenId = $request->input('dosen_id');
        $dosen = User::findOrFail($dosenId);

        $phone = $dosen->phone_number ?? $dosen->phone;
        if (empty($phone)) {
            return back()->with('error', "Nomor WhatsApp {$dosen->name} tidak ditemukan atau belum diatur di profil.");
        }

        // Get uncompleted sessions for this lecturer (HANYA SESI YANG SUDAH TERLEWAT)
        $sessions = MentoringSession::with(['thesis.student'])
            ->where(function($q) use ($dosenId) {
                $q->where('dosen_id', $dosenId)
                  ->orWhereHas('thesis', fn($t) => $t->where('pembimbing1_id', $dosenId)->orWhere('pembimbing2_id', $dosenId));
            })
            ->whereNotIn('status', ['completed', 'rejected'])
            ->where('scheduled_at', '<', Carbon::today()->startOfDay())
            ->orderBy('scheduled_at', 'asc')
            ->get();

        if ($sessions->isEmpty()) {
            return back()->with('error', "Tidak ada sesi bimbingan yang terlewat untuk {$dosen->name}.");
        }

        $customMessage = $request->input('message');
        $message = $customMessage ?: $this->monitoringService->generateUncompletedMentoringReminderMessage($dosen, $sessions);

        $sent = $whatsAppService->sendMessage($phone, $message);

        if ($sent) {
            return back()->with('success', "Pesan pengingat WhatsApp berhasil dikirim ke {$dosen->name} ({$phone}).");
        }

        return back()->with('warning', "Pesan tidak dapat terkirim otomatis melalui gateway WhatsApp (fitur WA mungkin belum aktif atau gateway sedang offline). Anda dapat menghubungi secara langsung melalui tautan WhatsApp.");
    }

    /**
     * Export uncompleted mentoring monitoring report to Excel.
     */
    public function exportUncompletedMentoringExcel(Request $request)
    {
        $filters = [
            'scope' => $request->input('scope', 'overdue'),
            'dosen_id' => $request->input('dosen_id'),
            'search' => $request->input('search'),
        ];

        $data = $this->monitoringService->getUncompletedMentoringData($filters);
        $fileName = 'Monitoring_Bimbingan_Belum_Selesai_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new \App\Exports\UncompletedMentoringExport($data), $fileName);
    }
}

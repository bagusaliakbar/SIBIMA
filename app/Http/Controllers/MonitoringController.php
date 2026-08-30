<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use App\Models\Wave;
use App\Models\User;
use App\Models\SeminarScheduleDetail;
use App\Models\ThesisDefenseScheduleDetail;
use App\Services\MonitoringService;
use App\Exports\MonitoringExport;
use App\Exports\DefenseScoresExport;
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
        
        $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->withMentoringCounts()
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
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(['search' => $search, 'pembimbing_id' => $pembimbingId, 'entry_year' => $entryYear]);

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

        foreach ($chartDosens as $dosen) {
            $thesesQuery = Thesis::where('status', '!=', 'completed')
                ->where(function($q) use ($dosen) {
                    $q->where('pembimbing1_id', $dosen->id)
                      ->orWhere('pembimbing2_id', $dosen->id);
                })
                ->whereHas('student', function($q) use ($entryYear) {
                    if ($entryYear) {
                        $q->where('entry_year', $entryYear);
                    }
                })
                ->with('student')
                ->get();

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
                    } elseif ($thesis->isAccSidangFinal()) {
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

        return view('monitoring.index', compact('theses', 'search', 'dosens', 'pembimbingId', 'entryYears', 'entryYear', 'chartData'));
    }

    public function export(Request $request)
    {
        $type = $request->input('type', 'akademik');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $format = $request->input('format', 'excel');

        $query = Thesis::with(['student', 'pembimbing1', 'pembimbing2']);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        switch ($type) {
            case 'mahasiswa':
                $data = $query->get();
                break;
            case 'kelulusan':
                $data = $query->where('status', 'completed')->get();
                break;
            case 'dosen':
                $data = User::where('role', 'dosen')->withCount(['thesesAsP1', 'thesesAsP2'])->get();
                break;
            case 'logs':
                $data = \App\Models\ActivityLog::with('user')
                    ->when($startDate && $endDate, function($q) use ($startDate, $endDate) {
                        $q->whereBetween('created_at', [$startDate, $endDate]);
                    })->latest()->get();
                break;
            default:
                $data = $query->get();
                break;
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('monitoring.reports.' . $type . '_pdf', compact('data', 'startDate', 'endDate'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('Laporan_' . ucfirst($type) . '_' . now()->format('Ymd') . '.pdf');
        }

        // Default to Excel using the existing MonitoringExport (or specialized one)
        return Excel::download(new MonitoringExport($type, $startDate, $endDate), 'Laporan_' . ucfirst($type) . '_' . now()->format('Ymd') . '.xlsx');
    }

    public function revisions(Request $request)
    {
        $search = $request->input('search');
        [$activeWave, $selectedWaveId] = $this->monitoringService->getActiveWave($request->input('wave_id'));

        $seminarDetails = SeminarScheduleDetail::with(['thesis.student', 'schedule', 'examiner1', 'examiner2', 'revisions'])
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

        $defenseDetails = ThesisDefenseScheduleDetail::with(['thesis.student', 'schedule', 'examiner1', 'examiner2', 'revisions'])
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
}

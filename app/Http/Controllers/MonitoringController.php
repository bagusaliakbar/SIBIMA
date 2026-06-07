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
        
        $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->withMentoringCounts()
            ->search($search)
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(['search' => $search]);

        return view('monitoring.index', compact('theses', 'search'));
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
        $students = $this->monitoringService->getCriticalStudentsQuery($search)
            ->paginate(15)
            ->appends(['search' => $search]);

        return view('monitoring.critical', compact('students', 'search'));
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

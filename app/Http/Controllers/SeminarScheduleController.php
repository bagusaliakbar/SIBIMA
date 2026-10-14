<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\SeminarSchedule;
use App\Models\SeminarScheduleDetail;
use App\Models\Thesis;
use App\Models\User;
use App\Models\Wave;
use App\Services\ScheduleService;
use App\Services\ScheduleWordExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SeminarScheduleController extends Controller implements HasMiddleware
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $userRole = Auth::user()->role;
                if (!in_array($userRole, ['admin', 'kaprodi', 'dosen'])) {
                    abort(403);
                }
                if ($userRole === 'dosen' && !in_array($request->route()->getName(), ['seminar-schedules.index', 'seminar-schedules.show', 'seminar-schedules.export-pdf', 'seminar-schedules.export-batch-pdf'])) {
                    abort(403);
                }
                return $next($request);
            }),
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $activeWave = Wave::getCurrentActive();

        if ($user->role === 'dosen') {
            $selectedWaveId = $request->input('wave_id') ?: $activeWave?->id;
            $hasWaveFilter = !empty($selectedWaveId);
        } else {
            $selectedWaveId = $request->input('wave_id', $activeWave?->id);
            $hasWaveFilter = $request->filled('wave_id');
        }

        $filterDate = $request->input('filter_date'); // 'all', 'today', 'upcoming', 'past', 'custom'
        $selectedDate = $request->input('date');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $mySchedules = $request->boolean('my_schedules');
        $searchQuery = $request->input('search');

        // Default filter_date for dosen if no date filter is explicitly passed
        if (!$request->has('filter_date') && !$request->filled('date') && !$request->filled('date_from') && !$request->filled('date_to')) {
            if ($user->role === 'dosen') {
                $filterDate = ($request->filled('wave_id') && $request->input('wave_id') != $activeWave?->id) ? 'all' : 'upcoming';
            } else {
                $filterDate = 'all';
            }
        }

        $query = SeminarSchedule::with([
            'chairman', 
            'moderator', 
            'creator', 
            'details' => function($dq) use ($mySchedules, $user) {
                if ($mySchedules && $user->role === 'dosen') {
                    $userId = $user->id;
                    $dq->where(function($sub) use ($userId) {
                        $sub->where('examiner1_id', $userId)
                            ->orWhere('examiner2_id', $userId)
                            ->orWhereHas('thesis', function($tq) use ($userId) {
                                $tq->where('pembimbing1_id', $userId)
                                   ->orWhere('pembimbing2_id', $userId);
                            });
                    });
                }
                $dq->with([
                    'thesis.student',
                    'thesis.pembimbing1',
                    'thesis.pembimbing2',
                    'examiner1',
                    'examiner2'
                ]);
            }
        ]);

        // Wave filter
        if ($selectedWaveId) {
            $query->where('wave_id', $selectedWaveId);
        }

        // Date filtering
        if ($selectedDate) {
            $query->whereDate('date', $selectedDate);
            $filterDate = 'custom';
        } elseif ($dateFrom || $dateTo) {
            if ($dateFrom) $query->whereDate('date', '>=', $dateFrom);
            if ($dateTo) $query->whereDate('date', '<=', $dateTo);
            $filterDate = 'custom';
        } elseif ($filterDate === 'today') {
            $query->whereDate('date', now()->toDateString());
        } elseif ($filterDate === 'upcoming') {
            $query->whereDate('date', '>=', now()->toDateString());
        } elseif ($filterDate === 'past') {
            $query->whereDate('date', '<', now()->toDateString());
        }

        // "Jadwal Saya" filter: Hanya tampilkan jika dosen jadi pembimbing atau penguji
        if ($mySchedules && $user->role === 'dosen') {
            $userId = $user->id;
            $query->whereHas('details', function($dq) use ($userId) {
                $dq->where('examiner1_id', $userId)
                   ->orWhere('examiner2_id', $userId)
                   ->orWhereHas('thesis', function($tq) use ($userId) {
                       $tq->where('pembimbing1_id', $userId)
                          ->orWhere('pembimbing2_id', $userId);
                   });
            });
        }

        // Keyword Search
        if ($request->filled('search')) {
            $search = '%' . trim($searchQuery) . '%';
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('location', 'like', $search)
                  ->orWhereHas('details', function($dq) use ($search) {
                      $dq->where('activity_name', 'like', $search)
                         ->orWhereHas('thesis', function($tq) use ($search) {
                             $tq->where('title', 'like', $search)
                                ->orWhereHas('student', function($sq) use ($search) {
                                    $sq->where('name', 'like', $search)
                                       ->orWhere('identifier', 'like', $search);
                                });
                         })
                         ->orWhereHas('examiner1', fn($eq) => $eq->where('name', 'like', $search))
                         ->orWhereHas('examiner2', fn($eq) => $eq->where('name', 'like', $search));
                  });
            });
        }

        // Count queries for Quick Filter Badges (scoped to current wave context)
        $countBaseQuery = SeminarSchedule::query();
        if ($selectedWaveId) {
            $countBaseQuery->where('wave_id', $selectedWaveId);
        }

        $todayStr = now()->toDateString();
        $dateStats = (clone $countBaseQuery)->selectRaw("
            COUNT(*) as all_count,
            SUM(CASE WHEN date = ? THEN 1 ELSE 0 END) as today_count,
            SUM(CASE WHEN date >= ? THEN 1 ELSE 0 END) as upcoming_count,
            SUM(CASE WHEN date < ? THEN 1 ELSE 0 END) as past_count
        ", [$todayStr, $todayStr, $todayStr])->first();

        $counts = [
            'all' => (int) ($dateStats->all_count ?? 0),
            'today' => (int) ($dateStats->today_count ?? 0),
            'upcoming' => (int) ($dateStats->upcoming_count ?? 0),
            'past' => (int) ($dateStats->past_count ?? 0),
            'mySchedules' => 0,
        ];

        if ($user->role === 'dosen') {
            $userId = $user->id;
            $counts['mySchedules'] = (clone $countBaseQuery)->whereHas('details', function($dq) use ($userId) {
                $dq->where('examiner1_id', $userId)
                   ->orWhere('examiner2_id', $userId)
                   ->orWhereHas('thesis', function($tq) use ($userId) {
                       $tq->where('pembimbing1_id', $userId)
                          ->orWhere('pembimbing2_id', $userId);
                   });
            })->count();
        }

        $sortDirection = ($filterDate === 'upcoming' || $filterDate === 'today') ? 'asc' : 'desc';
        $schedules = $query->orderBy('date', $sortDirection)
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->appends($request->query());

        $waves = Wave::orderBy('created_at', 'desc')->get();

        return view('seminar_schedules.index', compact(
            'schedules', 
            'waves', 
            'selectedWaveId', 
            'activeWave', 
            'hasWaveFilter',
            'filterDate',
            'selectedDate',
            'dateFrom',
            'dateTo',
            'mySchedules',
            'searchQuery',
            'counts'
        ));
    }

    public function create(Request $request)
    {
        $waves = Wave::orderBy('created_at', 'desc')->get();
        $activeWave = Wave::getCurrentActive();
        $selectedWaveId = $request->input('wave_id', $activeWave?->id);
        $targetWave = $waves->firstWhere('id', $selectedWaveId) ?? $activeWave ?? $waves->first();

        if (!$targetWave) {
            return redirect()->route('waves.index')->with('error', 'Silakan buat gelombang terlebih dahulu.');
        }

        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        $scheduledThesisIds = SeminarScheduleDetail::whereNotNull('thesis_id')->pluck('thesis_id');

        $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->where('status', '!=', 'completed')
            ->where('acc_up_p1', true)
            ->where('acc_up_p2', true)
            ->whereNotIn('id', $scheduledThesisIds)
            ->whereHas('seminarApplication', function($q) use ($targetWave) {
                $q->where('wave_id', $targetWave->id);
            })
            ->get();

        return view('seminar_schedules.create', compact('dosens', 'theses', 'waves', 'targetWave', 'selectedWaveId', 'activeWave'));
    }

    public function store(StoreScheduleRequest $request)
    {
        $validated = $request->validated();
        $waveId = $validated['wave_id'] ?? Wave::getCurrentActive()?->id;

        if (!$waveId) return back()->with('error', 'Tidak ada gelombang yang dipilih atau aktif.');

        $this->scheduleService->storeSchedule(SeminarSchedule::class, SeminarScheduleDetail::class, $validated, $waveId);

        return redirect()->route('seminar-schedules.index')->with('success', 'Jadwal seminar berhasil dibuat.');
    }

    public function show(SeminarSchedule $seminarSchedule)
    {
        $seminarSchedule->load(['chairman', 'moderator', 'details.thesis.student', 'details.thesis.pembimbing1', 'details.thesis.pembimbing2', 'details.examiner1', 'details.examiner2']);

        return view('seminar_schedules.show', compact('seminarSchedule'));
    }

    public function edit(SeminarSchedule $seminarSchedule)
    {
        $seminarSchedule->load('details');
        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        $scheduledOtherThesisIds = SeminarScheduleDetail::whereNotNull('thesis_id')
            ->where('seminar_schedule_id', '!=', $seminarSchedule->id)
            ->pluck('thesis_id');

        $currentScheduleThesisIds = $seminarSchedule->details()->whereNotNull('thesis_id')->pluck('thesis_id');

        $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->where(function($query) use ($currentScheduleThesisIds) {
                $query->where(function($q) {
                    $q->where('status', '!=', 'completed')
                      ->where('acc_up_p1', true)
                      ->where('acc_up_p2', true);
                })->orWhereIn('id', $currentScheduleThesisIds);
            })
            ->whereNotIn('id', $scheduledOtherThesisIds)
            ->get();

        $mappedDetails = $seminarSchedule->details->map(function($d) {
            return [
                'type' => $d->thesis_id ? 'student' : 'activity',
                'start_time' => \Carbon\Carbon::parse($d->start_time)->format('H:i'),
                'end_time' => \Carbon\Carbon::parse($d->end_time)->format('H:i'),
                'activity_name' => $d->activity_name,
                'thesis_id' => $d->thesis_id,
                'examiner1_id' => $d->examiner1_id,
                'examiner2_id' => $d->examiner2_id
            ];
        });

        return view('seminar_schedules.edit', compact('seminarSchedule', 'dosens', 'theses', 'mappedDetails'));
    }

    public function update(UpdateScheduleRequest $request, SeminarSchedule $seminarSchedule)
    {
        $this->scheduleService->updateSchedule($seminarSchedule, SeminarScheduleDetail::class, $request->validated());

        return redirect()->route('seminar-schedules.index')->with('success', 'Jadwal seminar berhasil diperbarui.');
    }

    public function destroy(SeminarSchedule $seminarSchedule)
    {
        $seminarSchedule->delete();

        return redirect()->route('seminar-schedules.index')->with('success', 'Jadwal seminar berhasil dihapus.');
    }

    public function exportBatchPdf(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi', 'dosen'])) {
            abort(403);
        }

        $mySchedules = $request->boolean('my_schedules');
        $activeWave = Wave::getCurrentActive();
        $selectedWaveId = $request->input('wave_id', $activeWave?->id);

        $query = SeminarSchedule::with([
            'chairman', 
            'moderator', 
            'creator', 
            'details' => function($dq) use ($mySchedules, $user) {
                if ($mySchedules && $user->role === 'dosen') {
                    $userId = $user->id;
                    $dq->where(function($sub) use ($userId) {
                        $sub->where('examiner1_id', $userId)
                            ->orWhere('examiner2_id', $userId)
                            ->orWhereHas('thesis', function($tq) use ($userId) {
                                $tq->where('pembimbing1_id', $userId)
                                   ->orWhere('pembimbing2_id', $userId);
                            });
                    });
                }
                $dq->with([
                    'thesis.student',
                    'thesis.pembimbing1',
                    'thesis.pembimbing2',
                    'examiner1',
                    'examiner2'
                ]);
            }
        ]);

        if ($selectedWaveId) {
            $query->where('wave_id', $selectedWaveId);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->input('date'));
        }

        if ($mySchedules && $user->role === 'dosen') {
            $userId = $user->id;
            $query->whereHas('details', function($dq) use ($userId) {
                $dq->where('examiner1_id', $userId)
                   ->orWhere('examiner2_id', $userId)
                   ->orWhereHas('thesis', function($tq) use ($userId) {
                       $tq->where('pembimbing1_id', $userId)
                          ->orWhere('pembimbing2_id', $userId);
                   });
            });
        }

        $schedules = $query->orderBy('date', 'asc')->orderBy('location', 'asc')->get();

        if ($schedules->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada jadwal seminar yang ditemukan untuk dicetak.');
        }

        $wave = $selectedWaveId ? Wave::find($selectedWaveId) : null;
        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();

        $pdf = Pdf::loadView('seminar_schedules.pdf_batch', compact('schedules', 'wave', 'kaprodi', 'mySchedules'))->setPaper('a4', 'landscape');

        $waveName = $wave ? str_replace([' ', '/', '\\'], '_', $wave->name) : 'Semua';
        $prefix = ($mySchedules && $user->role === 'dosen') ? 'Ringkasan_Jadwal_Seminar_Saya_' : 'Jadwal_Seminar_Gelombang_';
        return $pdf->download("{$prefix}{$waveName}.pdf");
    }

    public function exportPdf(SeminarSchedule $seminarSchedule, Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi', 'dosen'])) {
            abort(403);
        }

        $mySchedules = $request->boolean('my_schedules');

        $seminarSchedule->load([
            'chairman', 
            'moderator', 
            'details' => function($dq) use ($mySchedules, $user) {
                if ($mySchedules && $user->role === 'dosen') {
                    $userId = $user->id;
                    $dq->where(function($sub) use ($userId) {
                        $sub->where('examiner1_id', $userId)
                            ->orWhere('examiner2_id', $userId)
                            ->orWhereHas('thesis', function($tq) use ($userId) {
                                $tq->where('pembimbing1_id', $userId)
                                   ->orWhere('pembimbing2_id', $userId);
                            });
                    });
                }
                $dq->with([
                    'thesis.student',
                    'thesis.pembimbing1',
                    'thesis.pembimbing2',
                    'examiner1',
                    'examiner2'
                ]);
            }
        ]);

        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();
        $pdf = Pdf::loadView('seminar_schedules.pdf', compact('seminarSchedule', 'kaprodi', 'mySchedules'))->setPaper('a4', 'landscape');

        $prefix = ($mySchedules && $user->role === 'dosen') ? 'Ringkasan_Jadwal_Seminar_' : 'Jadwal_Seminar_';
        return $pdf->download($prefix . str_replace([' ', '/', '\\'], '_', $seminarSchedule->title) . '.pdf');
    }

    public function exportBatchWord(Request $request, ScheduleWordExportService $wordService)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $selectedWaveId = $request->input('wave_id');

        $query = SeminarSchedule::with([
            'chairman', 
            'moderator', 
            'creator', 
            'details' => function($dq) {
                $dq->with([
                    'thesis.student',
                    'thesis.pembimbing1',
                    'thesis.pembimbing2',
                    'examiner1',
                    'examiner2'
                ]);
            }
        ]);

        if ($selectedWaveId) {
            $query->where('wave_id', $selectedWaveId);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->input('date'));
        }

        $schedules = $query->orderBy('date', 'asc')->orderBy('location', 'asc')->get();

        if ($schedules->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada jadwal seminar yang ditemukan untuk diexport.');
        }

        $wave = $selectedWaveId ? Wave::find($selectedWaveId) : null;
        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();

        $tempFile = $wordService->exportSeminarBatch($schedules, $wave, $kaprodi);
        $waveName = $wave ? str_replace([' ', '/', '\\'], '_', $wave->name) : 'Semua';
        $fileName = "Jadwal_Seminar_Gelombang_{$waveName}.docx";

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }

    public function exportWord(SeminarSchedule $seminarSchedule, ScheduleWordExportService $wordService)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $seminarSchedule->load([
            'chairman', 
            'moderator', 
            'details' => function($dq) {
                $dq->with([
                    'thesis.student',
                    'thesis.pembimbing1',
                    'thesis.pembimbing2',
                    'examiner1',
                    'examiner2'
                ]);
            }
        ]);

        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();
        $tempFile = $wordService->exportSeminar($seminarSchedule, $kaprodi);
        $fileName = 'Jadwal_Seminar_' . str_replace([' ', '/', '\\'], '_', $seminarSchedule->title) . '.docx';

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SeminarScheduleTemplateExport,
            'Template_Import_Jadwal_Seminar_' . date('Ymd_His') . '.xlsx'
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'wave_id' => 'nullable|exists:waves,id',
        ]);

        $waveId = $request->input('wave_id') ?: Wave::getCurrentActive()?->id;
        if (!$waveId) {
            return redirect()->back()->with('error', 'Silakan pilih gelombang yang aktif atau tersedia terlebih dahulu.');
        }

        try {
            $importer = new \App\Imports\SeminarSchedulesImport($waveId);
            \Maatwebsite\Excel\Facades\Excel::import($importer, $request->file('file'));

            $message = "Berhasil mengimpor {$importer->schedulesCount} jadwal seminar dengan total {$importer->sessionsCount} sesi.";
            if ($importer->skippedCount > 0) {
                $message .= " ({$importer->skippedCount} baris sesi dilewati).";
            }

            $redirect = redirect()->route('seminar-schedules.index', ['wave_id' => $waveId])->with('success', $message);
            if (!empty($importer->skippedDetails)) {
                $redirect->with('skippedDetails', $importer->skippedDetails);
            }

            return $redirect;
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }
}

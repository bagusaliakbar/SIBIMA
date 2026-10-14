<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\ThesisDefenseSchedule;
use App\Models\ThesisDefenseScheduleDetail;
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

class ThesisDefenseScheduleController extends Controller implements HasMiddleware
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
                if ($userRole === 'dosen' && !in_array($request->route()->getName(), ['thesis-defense-schedules.index', 'thesis-defense-schedules.show', 'thesis-defense-schedules.export-pdf', 'thesis-defense-schedules.export-batch-pdf'])) {
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

        $query = ThesisDefenseSchedule::with([
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
                            })
                            ->orWhereHas('schedule', function($sq) use ($userId) {
                                $sq->where('chairman_id', $userId)
                                   ->orWhere('moderator_id', $userId);
                            });
                    });
                }
                $dq->with([
                    'thesis.student',
                    'thesis.pembimbing1',
                    'thesis.pembimbing2',
                    'examiner1',
                    'examiner2'
                ])->orderBy('start_time', 'asc')->orderBy('order', 'asc');
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

        // "Jadwal Saya" filter: Hanya tampilkan jika dosen jadi pembimbing, penguji, ketua sidang, atau moderator
        if ($mySchedules && $user->role === 'dosen') {
            $userId = $user->id;
            $query->where(function($q) use ($userId) {
                $q->where('chairman_id', $userId)
                  ->orWhere('moderator_id', $userId)
                  ->orWhereHas('details', function($dq) use ($userId) {
                      $dq->where('examiner1_id', $userId)
                         ->orWhere('examiner2_id', $userId)
                         ->orWhereHas('thesis', function($tq) use ($userId) {
                             $tq->where('pembimbing1_id', $userId)
                                ->orWhere('pembimbing2_id', $userId);
                         });
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
        $countBaseQuery = ThesisDefenseSchedule::query();
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
            $counts['mySchedules'] = (clone $countBaseQuery)->where(function($q) use ($userId) {
                $q->where('chairman_id', $userId)
                  ->orWhere('moderator_id', $userId)
                  ->orWhereHas('details', function($dq) use ($userId) {
                      $dq->where('examiner1_id', $userId)
                         ->orWhere('examiner2_id', $userId)
                         ->orWhereHas('thesis', function($tq) use ($userId) {
                             $tq->where('pembimbing1_id', $userId)
                                ->orWhere('pembimbing2_id', $userId);
                         });
                  });
            })->count();
        }

        $startTimeSubquery = ThesisDefenseScheduleDetail::select('start_time')
            ->whereColumn('thesis_defense_schedule_details.thesis_defense_schedule_id', 'thesis_defense_schedules.id')
            ->when($mySchedules && $user->role === 'dosen', function($sq) use ($user) {
                $userId = $user->id;
                $sq->where(function($sub) use ($userId) {
                    $sub->where('examiner1_id', $userId)
                        ->orWhere('examiner2_id', $userId)
                        ->orWhereHas('thesis', function($tq) use ($userId) {
                            $tq->where('pembimbing1_id', $userId)
                               ->orWhere('pembimbing2_id', $userId);
                        })
                        ->orWhereHas('schedule', function($schQ) use ($userId) {
                            $schQ->where('chairman_id', $userId)
                                 ->orWhere('moderator_id', $userId);
                        });
                });
            })
            ->orderBy('start_time', 'asc')
            ->limit(1);

        $sortDirection = ($filterDate === 'upcoming' || $filterDate === 'today' || $mySchedules) ? 'asc' : 'desc';
        $schedules = $query->orderBy('date', $sortDirection)
            ->orderBy($startTimeSubquery, 'asc')
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->appends($request->query());

        $waves = Wave::orderBy('created_at', 'desc')->get();

        return view('thesis_defense_schedules.index', compact(
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
        $scheduledThesisIds = ThesisDefenseScheduleDetail::whereNotNull('thesis_id')->pluck('thesis_id');

        $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->where('status', '!=', 'completed')
            ->where('acc_sidang_p1', true)
            ->where('acc_sidang_p2', true)
            ->whereNotIn('id', $scheduledThesisIds)
            ->whereHas('defenseApplication', function($q) use ($targetWave) {
                $q->where('wave_id', $targetWave->id);
            })
            ->get();

        return view('thesis_defense_schedules.create', compact('dosens', 'theses', 'waves', 'targetWave', 'selectedWaveId', 'activeWave'));
    }

    public function store(StoreScheduleRequest $request)
    {
        $validated = $request->validated();
        $waveId = $validated['wave_id'] ?? Wave::getCurrentActive()?->id;

        if (!$waveId) return back()->with('error', 'Tidak ada gelombang yang dipilih atau aktif.');

        $this->scheduleService->storeSchedule(ThesisDefenseSchedule::class, ThesisDefenseScheduleDetail::class, $validated, $waveId);

        return redirect()->route('thesis-defense-schedules.index')->with('success', 'Jadwal sidang berhasil dibuat.');
    }

    public function show(ThesisDefenseSchedule $thesisDefenseSchedule)
    {
        $thesisDefenseSchedule->load(['chairman', 'moderator', 'details.thesis.student', 'details.thesis.pembimbing1', 'details.thesis.pembimbing2', 'details.examiner1', 'details.examiner2']);

        return view('thesis_defense_schedules.show', compact('thesisDefenseSchedule'));
    }

    public function edit(ThesisDefenseSchedule $thesisDefenseSchedule)
    {
        $thesisDefenseSchedule->load('details');
        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        $scheduledOtherThesisIds = ThesisDefenseScheduleDetail::whereNotNull('thesis_id')
            ->where('thesis_defense_schedule_id', '!=', $thesisDefenseSchedule->id)
            ->pluck('thesis_id');

        $currentScheduleThesisIds = $thesisDefenseSchedule->details()->whereNotNull('thesis_id')->pluck('thesis_id');

        $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->where(function($query) use ($currentScheduleThesisIds) {
                $query->where(function($q) {
                    $q->where('status', '!=', 'completed')
                      ->where('acc_sidang_p1', true)
                      ->where('acc_sidang_p2', true);
                })->orWhereIn('id', $currentScheduleThesisIds);
            })
            ->whereNotIn('id', $scheduledOtherThesisIds)
            ->get();

        $mappedDetails = $thesisDefenseSchedule->details->map(function($d) {
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

        return view('thesis_defense_schedules.edit', compact('thesisDefenseSchedule', 'dosens', 'theses', 'mappedDetails'));
    }

    public function update(UpdateScheduleRequest $request, ThesisDefenseSchedule $thesisDefenseSchedule)
    {
        $this->scheduleService->updateSchedule($thesisDefenseSchedule, ThesisDefenseScheduleDetail::class, $request->validated());

        return redirect()->route('thesis-defense-schedules.index')->with('success', 'Jadwal sidang berhasil diperbarui.');
    }

    public function destroy(ThesisDefenseSchedule $thesisDefenseSchedule)
    {
        $thesisDefenseSchedule->delete();

        return redirect()->route('thesis-defense-schedules.index')->with('success', 'Jadwal sidang berhasil dihapus.');
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

        $query = ThesisDefenseSchedule::with([
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
            return redirect()->back()->with('error', 'Tidak ada jadwal sidang yang ditemukan untuk dicetak.');
        }

        $wave = $selectedWaveId ? Wave::find($selectedWaveId) : null;
        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();

        $pdf = Pdf::loadView('thesis_defense_schedules.pdf_batch', compact('schedules', 'wave', 'kaprodi', 'mySchedules'))->setPaper('a4', 'landscape');

        $waveName = $wave ? str_replace([' ', '/', '\\'], '_', $wave->name) : 'Semua';
        $prefix = ($mySchedules && $user->role === 'dosen') ? 'Ringkasan_Jadwal_Sidang_Saya_' : 'Jadwal_Sidang_Skripsi_Gelombang_';
        return $pdf->download("{$prefix}{$waveName}.pdf");
    }

    public function exportPdf(ThesisDefenseSchedule $thesisDefenseSchedule, Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi', 'dosen'])) {
            abort(403);
        }

        $mySchedules = $request->boolean('my_schedules');

        $thesisDefenseSchedule->load([
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
        $pdf = Pdf::loadView('thesis_defense_schedules.pdf', compact('thesisDefenseSchedule', 'kaprodi', 'mySchedules'))->setPaper('a4', 'landscape');

        $prefix = ($mySchedules && $user->role === 'dosen') ? 'Ringkasan_Jadwal_Sidang_' : 'Jadwal_Sidang_';
        return $pdf->download($prefix . str_replace([' ', '/', '\\'], '_', $thesisDefenseSchedule->title) . '.pdf');
    }

    public function exportBatchWord(Request $request, ScheduleWordExportService $wordService)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $selectedWaveId = $request->input('wave_id');

        $query = ThesisDefenseSchedule::with([
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
            return redirect()->back()->with('error', 'Tidak ada jadwal sidang yang ditemukan untuk diexport.');
        }

        $wave = $selectedWaveId ? Wave::find($selectedWaveId) : null;
        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();

        $tempFile = $wordService->exportDefenseBatch($schedules, $wave, $kaprodi);
        $waveName = $wave ? str_replace([' ', '/', '\\'], '_', $wave->name) : 'Semua';
        $fileName = "Jadwal_Sidang_Skripsi_Gelombang_{$waveName}.docx";

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }

    public function exportWord(ThesisDefenseSchedule $thesisDefenseSchedule, ScheduleWordExportService $wordService)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $thesisDefenseSchedule->load([
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
        $tempFile = $wordService->exportDefense($thesisDefenseSchedule, $kaprodi);
        $fileName = 'Jadwal_Sidang_' . str_replace([' ', '/', '\\'], '_', $thesisDefenseSchedule->title) . '.docx';

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ThesisDefenseScheduleTemplateExport,
            'Template_Import_Jadwal_Sidang_' . date('Ymd_His') . '.xlsx'
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
            $importer = new \App\Imports\ThesisDefenseSchedulesImport($waveId);
            \Maatwebsite\Excel\Facades\Excel::import($importer, $request->file('file'));

            $message = "Berhasil mengimpor {$importer->schedulesCount} jadwal sidang dengan total {$importer->sessionsCount} sesi.";
            if ($importer->skippedCount > 0) {
                $message .= " ({$importer->skippedCount} baris sesi dilewati).";
            }

            $redirect = redirect()->route('thesis-defense-schedules.index', ['wave_id' => $waveId])->with('success', $message);
            if (!empty($importer->skippedDetails)) {
                $redirect->with('skippedDetails', $importer->skippedDetails);
            }

            return $redirect;
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }
}

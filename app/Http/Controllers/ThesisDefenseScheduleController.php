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
                if ($userRole === 'dosen' && !in_array($request->route()->getName(), ['thesis-defense-schedules.index', 'thesis-defense-schedules.show', 'thesis-defense-schedules.export-pdf'])) {
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
        $hasWaveFilter = $request->filled('wave_id');
        $selectedWaveId = $request->input('wave_id');

        $query = ThesisDefenseSchedule::with(['chairman', 'moderator', 'creator', 'details.thesis.student', 'details.thesis.pembimbing1', 'details.thesis.pembimbing2', 'details.examiner1', 'details.examiner2']);

        if ($user->role === 'dosen') {
            if ($hasWaveFilter) {
                // Dosen memilih gelombang secara eksplisit untuk melihat seluruh agenda gelombang tersebut
                $query->where('wave_id', $selectedWaveId);
            } else {
                // Dosen belum memilih gelombang:
                // Jangan tampilkan jadwal yang tanggal pelaksanaannya sudah lewat (date < today).
                // Tampilkan hanya jadwal aktif / mendatang (date >= today).
                $query->where('date', '>=', now()->toDateString())
                      ->when($activeWave, function($q) use ($activeWave) {
                          $q->where('wave_id', $activeWave->id);
                      });
            }
        } else {
            // Admin & Kaprodi: default to activeWave if no wave is selected
            $selectedWaveId = $request->input('wave_id', $activeWave?->id);
            $query->when($selectedWaveId, function($q) use ($selectedWaveId) {
                $q->where('wave_id', $selectedWaveId);
            });
        }

        $schedules = $query->orderBy('date', 'desc')
            ->paginate(10)
            ->appends($request->query());

        $waves = Wave::orderBy('created_at', 'desc')->get();

        return view('thesis_defense_schedules.index', compact('schedules', 'waves', 'selectedWaveId', 'activeWave', 'hasWaveFilter'));
    }

    public function create()
    {
        $activeWave = Wave::getCurrentActive();
        if (!$activeWave) {
            return redirect()->route('waves.index')->with('error', 'Silakan aktifkan gelombang terlebih dahulu.');
        }

        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        $scheduledThesisIds = ThesisDefenseScheduleDetail::whereNotNull('thesis_id')->pluck('thesis_id');

        $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->where('status', '!=', 'completed')
            ->where('acc_sidang_p1', true)
            ->where('acc_sidang_p2', true)
            ->whereNotIn('id', $scheduledThesisIds)
            ->whereHas('defenseApplication', function($q) use ($activeWave) {
                $q->where('wave_id', $activeWave->id);
            })
            ->get();

        return view('thesis_defense_schedules.create', compact('dosens', 'theses'));
    }

    public function store(StoreScheduleRequest $request)
    {
        $activeWave = Wave::getCurrentActive();
        if (!$activeWave) return back()->with('error', 'Tidak ada gelombang aktif.');

        $this->scheduleService->storeSchedule(ThesisDefenseSchedule::class, ThesisDefenseScheduleDetail::class, $request->validated(), $activeWave->id);

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

    public function exportPdf(ThesisDefenseSchedule $thesisDefenseSchedule)
    {
        $user = Auth::user();
        $isAuthorized = $user->role === 'admin' 
            || $user->role === 'kaprodi'
            || ($thesisDefenseSchedule->chairman_id && $user->id === $thesisDefenseSchedule->chairman_id)
            || ($thesisDefenseSchedule->moderator_id && $user->id === $thesisDefenseSchedule->moderator_id)
            || $thesisDefenseSchedule->details()->where(function($q) use ($user) {
                $q->where('examiner1_id', $user->id)->orWhere('examiner2_id', $user->id);
            })->exists();

        if (!$isAuthorized) abort(403);

        $thesisDefenseSchedule->load(['chairman', 'moderator', 'details.thesis.student', 'details.thesis.pembimbing1', 'details.thesis.pembimbing2', 'details.examiner1', 'details.examiner2']);

        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();
        $pdf = Pdf::loadView('thesis_defense_schedules.pdf', compact('thesisDefenseSchedule', 'kaprodi'))->setPaper('a4', 'landscape');

        return $pdf->download('Jadwal_Sidang_' . str_replace([' ', '/', '\\'], '_', $thesisDefenseSchedule->title) . '.pdf');
    }
}

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
                if ($userRole === 'dosen' && !in_array($request->route()->getName(), ['seminar-schedules.index', 'seminar-schedules.show', 'seminar-schedules.export-pdf'])) {
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

        $query = SeminarSchedule::with(['chairman', 'moderator', 'creator', 'details.thesis.student', 'details.thesis.pembimbing1', 'details.thesis.pembimbing2', 'details.examiner1', 'details.examiner2']);

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

        return view('seminar_schedules.index', compact('schedules', 'waves', 'selectedWaveId', 'activeWave', 'hasWaveFilter'));
    }

    public function create()
    {
        $activeWave = Wave::getCurrentActive();
        if (!$activeWave) {
            return redirect()->route('waves.index')->with('error', 'Silakan aktifkan gelombang terlebih dahulu.');
        }

        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        $scheduledThesisIds = SeminarScheduleDetail::whereNotNull('thesis_id')->pluck('thesis_id');

        $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->where('status', '!=', 'completed')
            ->where('acc_up_p1', true)
            ->where('acc_up_p2', true)
            ->whereNotIn('id', $scheduledThesisIds)
            ->whereHas('seminarApplication', function($q) use ($activeWave) {
                $q->where('wave_id', $activeWave->id);
            })
            ->get();

        return view('seminar_schedules.create', compact('dosens', 'theses'));
    }

    public function store(StoreScheduleRequest $request)
    {
        $activeWave = Wave::getCurrentActive();
        if (!$activeWave) return back()->with('error', 'Tidak ada gelombang aktif.');

        $this->scheduleService->storeSchedule(SeminarSchedule::class, SeminarScheduleDetail::class, $request->validated(), $activeWave->id);

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

    public function exportPdf(SeminarSchedule $seminarSchedule)
    {
        $user = Auth::user();
        $isAuthorized = $user->role === 'admin' 
            || $user->role === 'kaprodi'
            || ($seminarSchedule->chairman_id && $user->id === $seminarSchedule->chairman_id)
            || ($seminarSchedule->moderator_id && $user->id === $seminarSchedule->moderator_id)
            || $seminarSchedule->details()->where(function($q) use ($user) {
                $q->where('examiner1_id', $user->id)->orWhere('examiner2_id', $user->id);
            })->exists();

        if (!$isAuthorized) abort(403);

        $seminarSchedule->load(['chairman', 'moderator', 'details.thesis.student', 'details.thesis.pembimbing1', 'details.thesis.pembimbing2', 'details.examiner1', 'details.examiner2']);

        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();
        $pdf = Pdf::loadView('seminar_schedules.pdf', compact('seminarSchedule', 'kaprodi'))->setPaper('a4', 'landscape');

        return $pdf->download('Jadwal_Seminar_' . str_replace([' ', '/', '\\'], '_', $seminarSchedule->title) . '.pdf');
    }
}

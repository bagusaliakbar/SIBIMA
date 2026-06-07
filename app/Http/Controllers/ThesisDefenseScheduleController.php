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
                if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'kaprodi' && !in_array($request->route()->getName(), ['thesis-defense-schedules.export-pdf', 'thesis-defense-schedules.show'])) {
                    abort(403);
                }
                return $next($request);
            }),
        ];
    }

    public function index(Request $request)
    {
        $activeWave = Wave::getCurrentActive();
        $selectedWaveId = $request->input('wave_id', $activeWave?->id);

        $schedules = ThesisDefenseSchedule::with(['chairman', 'moderator', 'creator'])
            ->when($selectedWaveId, function($query) use ($selectedWaveId) {
                $query->where('wave_id', $selectedWaveId);
            })
            ->orderBy('date', 'desc')
            ->paginate(10)
            ->appends(['wave_id' => $selectedWaveId]);

        $waves = Wave::orderBy('created_at', 'desc')->get();

        return view('thesis_defense_schedules.index', compact('schedules', 'waves', 'selectedWaveId', 'activeWave'));
    }

    public function create()
    {
        $activeWave = Wave::getCurrentActive();
        if (!$activeWave) {
            return redirect()->route('waves.index')->with('error', 'Silakan aktifkan gelombang terlebih dahulu.');
        }

        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->where('status', '!=', 'completed')
            ->where('acc_sidang_p1', true)
            ->where('acc_sidang_p2', true)
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
        $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->where('status', '!=', 'completed')
            ->where('acc_sidang_p1', true)
            ->where('acc_sidang_p2', true)
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
            || $user->id === $thesisDefenseSchedule->chairman_id 
            || $user->id === $thesisDefenseSchedule->moderator_id
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

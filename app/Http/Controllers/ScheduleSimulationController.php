<?php

namespace App\Http\Controllers;

use App\Models\Wave;
use App\Models\User;
use App\Services\AutoScheduleSimulationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleSimulationController extends Controller
{
    protected $simulationService;

    public function __construct(AutoScheduleSimulationService $simulationService)
    {
        $this->simulationService = $simulationService;
    }

    /**
     * Display seminar schedule simulation view.
     */
    public function seminarIndex(Request $request)
    {
        $this->authorizeAdminOrKaprodi();

        $waves = Wave::orderBy('created_at', 'desc')->get();
        $activeWave = Wave::getCurrentActive();
        $selectedWaveId = $request->input('wave_id', $activeWave?->id);
        $targetWave = $waves->firstWhere('id', $selectedWaveId) ?? $activeWave ?? $waves->first();

        $candidates = $this->simulationService->getEligibleCandidates('seminar', $targetWave?->id);
        $dosens = User::where('role', 'dosen')->orderBy('name')->get();

        return view('seminar_schedules.simulation', compact(
            'waves',
            'activeWave',
            'targetWave',
            'selectedWaveId',
            'candidates',
            'dosens'
        ));
    }

    /**
     * Run seminar simulation algorithm and return JSON response.
     */
    public function seminarRun(Request $request)
    {
        $this->authorizeAdminOrKaprodi();

        $validated = $request->validate([
            'wave_id' => 'nullable|exists:waves,id',
            'date' => 'required|date',
            'room_count' => 'required|integer|min:1|max:10',
            'room_names' => 'nullable|array',
            'room_names.*' => 'nullable|string|max:100',
            'max_students_per_room' => 'required|integer|min:1|max:20',
            'start_time' => 'required|string',
            'duration_minutes' => 'required|integer|min:15|max:180',
            'break_enabled' => 'nullable|boolean',
            'break_start' => 'nullable|string',
            'break_end' => 'nullable|string',
            'thesis_ids' => 'nullable|array',
            'thesis_ids.*' => 'exists:theses,id',
        ]);

        $result = $this->simulationService->generateSimulation('seminar', $validated['wave_id'] ?? null, $validated);

        return response()->json($result);
    }

    /**
     * Apply and save seminar simulation into official SeminarSchedule database records.
     */
    public function seminarApply(Request $request)
    {
        $this->authorizeAdminOrKaprodi();

        $validated = $request->validate([
            'wave_id' => 'required|exists:waves,id',
            'date' => 'required|date',
            'rooms' => 'required|array|min:1',
            'rooms.*.title' => 'required|string|max:255',
            'rooms.*.room_name' => 'required|string|max:100',
            'rooms.*.chairman_id' => 'nullable|exists:users,id',
            'rooms.*.moderator_id' => 'nullable|exists:users,id',
            'rooms.*.meeting_link' => 'nullable|string|max:255',
            'rooms.*.sessions' => 'required|array|min:1',
            'rooms.*.sessions.*.thesis_id' => 'nullable|exists:theses,id',
            'rooms.*.sessions.*.start_time' => 'required|string',
            'rooms.*.sessions.*.end_time' => 'required|string',
            'rooms.*.sessions.*.examiner1_id' => 'nullable|exists:users,id',
            'rooms.*.sessions.*.examiner2_id' => 'nullable|exists:users,id',
        ]);

        $createdSchedules = $this->simulationService->applySimulation(
            'seminar',
            $validated['wave_id'],
            $validated['rooms'],
            $validated['date']
        );

        $roomCount = count($createdSchedules);
        $totalSessions = collect($createdSchedules)->sum(fn($s) => $s->details()->count());

        return response()->json([
            'status' => 'success',
            'message' => "Berhasil menerapkan jadwal resmi! {$roomCount} ruangan dengan total {$totalSessions} sesi telah diterbitkan.",
            'redirect_url' => route('seminar-schedules.index', ['wave_id' => $validated['wave_id']]),
        ]);
    }

    /**
     * Display defense schedule simulation view.
     */
    public function defenseIndex(Request $request)
    {
        $this->authorizeAdminOrKaprodi();

        $waves = Wave::orderBy('created_at', 'desc')->get();
        $activeWave = Wave::getCurrentActive();
        $selectedWaveId = $request->input('wave_id', $activeWave?->id);
        $targetWave = $waves->firstWhere('id', $selectedWaveId) ?? $activeWave ?? $waves->first();

        $candidates = $this->simulationService->getEligibleCandidates('defense', $targetWave?->id);
        $dosens = User::where('role', 'dosen')->orderBy('name')->get();

        return view('thesis_defense_schedules.simulation', compact(
            'waves',
            'activeWave',
            'targetWave',
            'selectedWaveId',
            'candidates',
            'dosens'
        ));
    }

    /**
     * Run defense simulation algorithm and return JSON response.
     */
    public function defenseRun(Request $request)
    {
        $this->authorizeAdminOrKaprodi();

        $validated = $request->validate([
            'wave_id' => 'nullable|exists:waves,id',
            'date' => 'required|date',
            'room_count' => 'required|integer|min:1|max:10',
            'room_names' => 'nullable|array',
            'room_names.*' => 'nullable|string|max:100',
            'max_students_per_room' => 'required|integer|min:1|max:20',
            'start_time' => 'required|string',
            'duration_minutes' => 'required|integer|min:15|max:180',
            'break_enabled' => 'nullable|boolean',
            'break_start' => 'nullable|string',
            'break_end' => 'nullable|string',
            'thesis_ids' => 'nullable|array',
            'thesis_ids.*' => 'exists:theses,id',
        ]);

        $result = $this->simulationService->generateSimulation('defense', $validated['wave_id'] ?? null, $validated);

        return response()->json($result);
    }

    /**
     * Apply and save defense simulation into official ThesisDefenseSchedule database records.
     */
    public function defenseApply(Request $request)
    {
        $this->authorizeAdminOrKaprodi();

        $validated = $request->validate([
            'wave_id' => 'required|exists:waves,id',
            'date' => 'required|date',
            'rooms' => 'required|array|min:1',
            'rooms.*.title' => 'required|string|max:255',
            'rooms.*.room_name' => 'required|string|max:100',
            'rooms.*.chairman_id' => 'nullable|exists:users,id',
            'rooms.*.moderator_id' => 'nullable|exists:users,id',
            'rooms.*.meeting_link' => 'nullable|string|max:255',
            'rooms.*.sessions' => 'required|array|min:1',
            'rooms.*.sessions.*.thesis_id' => 'nullable|exists:theses,id',
            'rooms.*.sessions.*.start_time' => 'required|string',
            'rooms.*.sessions.*.end_time' => 'required|string',
            'rooms.*.sessions.*.examiner1_id' => 'nullable|exists:users,id',
            'rooms.*.sessions.*.examiner2_id' => 'nullable|exists:users,id',
        ]);

        $createdSchedules = $this->simulationService->applySimulation(
            'defense',
            $validated['wave_id'],
            $validated['rooms'],
            $validated['date']
        );

        $roomCount = count($createdSchedules);
        $totalSessions = collect($createdSchedules)->sum(fn($s) => $s->details()->count());

        return response()->json([
            'status' => 'success',
            'message' => "Berhasil menerapkan jadwal resmi! {$roomCount} ruangan dengan total {$totalSessions} sesi sidang telah diterbitkan.",
            'redirect_url' => route('thesis-defense-schedules.index', ['wave_id' => $validated['wave_id']]),
        ]);
    }

    protected function authorizeAdminOrKaprodi()
    {
        $role = Auth::user()?->role;
        if (!in_array($role, ['admin', 'kaprodi'])) {
            abort(403, 'Akses terbatas untuk Administrator dan Koordinator Program Studi.');
        }
    }
}

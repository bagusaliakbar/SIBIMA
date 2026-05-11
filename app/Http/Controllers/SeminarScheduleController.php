<?php

namespace App\Http\Controllers;

use App\Models\SeminarSchedule;
use App\Models\SeminarScheduleDetail;
use App\Models\Thesis;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Wave;

class SeminarScheduleController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $activeWave = Wave::active() ?: Wave::where('is_active', true)->latest()->first() ?: Wave::latest()->first();
        $selectedWaveId = $request->get('wave_id', $activeWave?->id);

        $schedules = SeminarSchedule::with(['chairman', 'moderator', 'creator'])
            ->when($selectedWaveId, function($query) use ($selectedWaveId) {
                $query->where('wave_id', $selectedWaveId);
            })
            ->orderBy('date', 'desc')
            ->paginate(10)
            ->appends(['wave_id' => $selectedWaveId]);

        $waves = Wave::orderBy('created_at', 'desc')->get();

        return view('seminar_schedules.index', compact('schedules', 'waves', 'selectedWaveId', 'activeWave'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $activeWave = Wave::active();
        if (!$activeWave) {
            return redirect()->route('waves.index')->with('error', 'Silakan aktifkan gelombang terlebih dahulu sebelum membuat jadwal.');
        }

        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->where('acc_up_p1', true)
            ->where('acc_up_p2', true)
            ->whereHas('seminarApplication', function($q) use ($activeWave) {
                $q->where('wave_id', $activeWave->id);
            })
            ->get();

        return view('seminar_schedules.create', compact('dosens', 'theses'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $activeWave = Wave::active();
        if (!$activeWave) {
            return redirect()->route('waves.index')->with('error', 'Tidak ada gelombang aktif.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'chairman_id' => 'required|exists:users,id',
            'moderator_id' => 'required|exists:users,id',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url',
            'details' => 'required|array|min:1',
            'details.*.start_time' => 'required',
            'details.*.end_time' => 'required',
        ]);

        DB::transaction(function () use ($request, $activeWave) {
            $schedule = SeminarSchedule::create([
                'title' => $request->title,
                'date' => $request->date,
                'chairman_id' => $request->chairman_id,
                'moderator_id' => $request->moderator_id,
                'location' => $request->location,
                'meeting_link' => $request->meeting_link,
                'created_by' => Auth::id(),
                'wave_id' => $activeWave->id,
            ]);

            foreach ($request->details as $index => $detail) {
                SeminarScheduleDetail::create([
                    'seminar_schedule_id' => $schedule->id,
                    'thesis_id' => $detail['thesis_id'] ?? null,
                    'activity_name' => $detail['activity_name'] ?? null,
                    'start_time' => $detail['start_time'],
                    'end_time' => $detail['end_time'],
                    'examiner1_id' => $detail['examiner1_id'] ?? null,
                    'examiner2_id' => $detail['examiner2_id'] ?? null,
                    'order' => $index,
                ]);
            }
        });

        return redirect()->route('seminar-schedules.index')->with('success', 'Jadwal seminar berhasil dibuat.');
    }

    public function show(SeminarSchedule $seminarSchedule)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $seminarSchedule->load(['chairman', 'moderator', 'details.thesis.student', 'details.thesis.pembimbing1', 'details.thesis.pembimbing2', 'details.examiner1', 'details.examiner2']);

        return view('seminar_schedules.show', compact('seminarSchedule'));
    }

    public function edit(SeminarSchedule $seminarSchedule)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $seminarSchedule->load('details');
        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->where('acc_up_p1', true)
            ->where('acc_up_p2', true)
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

    public function update(Request $request, SeminarSchedule $seminarSchedule)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'chairman_id' => 'required|exists:users,id',
            'moderator_id' => 'required|exists:users,id',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url',
            'details' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request, $seminarSchedule) {
            $seminarSchedule->update([
                'title' => $request->title,
                'date' => $request->date,
                'chairman_id' => $request->chairman_id,
                'moderator_id' => $request->moderator_id,
                'location' => $request->location,
                'meeting_link' => $request->meeting_link,
            ]);

            $seminarSchedule->details()->delete();

            foreach ($request->details as $index => $detail) {
                SeminarScheduleDetail::create([
                    'seminar_schedule_id' => $seminarSchedule->id,
                    'thesis_id' => $detail['thesis_id'] ?? null,
                    'activity_name' => $detail['activity_name'] ?? null,
                    'start_time' => $detail['start_time'],
                    'end_time' => $detail['end_time'],
                    'examiner1_id' => $detail['examiner1_id'] ?? null,
                    'examiner2_id' => $detail['examiner2_id'] ?? null,
                    'order' => $index,
                ]);
            }
        });

        return redirect()->route('seminar-schedules.index')->with('success', 'Jadwal seminar berhasil diperbarui.');
    }

    public function destroy(SeminarSchedule $seminarSchedule)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $seminarSchedule->delete();

        return redirect()->route('seminar-schedules.index')->with('success', 'Jadwal seminar berhasil dihapus.');
    }

    public function exportPdf(SeminarSchedule $seminarSchedule)
    {
        $seminarSchedule->load(['chairman', 'moderator', 'details.thesis.student', 'details.thesis.pembimbing1', 'details.thesis.pembimbing2', 'details.examiner1', 'details.examiner2']);

        $pdf = Pdf::loadView('seminar_schedules.pdf', compact('seminarSchedule'))
            ->setPaper('a4', 'landscape');

        $safeTitle = str_replace([' ', '/', '\\'], '_', $seminarSchedule->title);
        return $pdf->download('Jadwal_Seminar_' . $safeTitle . '.pdf');
    }
}

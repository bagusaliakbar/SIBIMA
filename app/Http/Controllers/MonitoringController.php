<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Exports\MonitoringExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Wave;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $search = $request->get('search');
        
        $theses = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->withCount(['mentoringSessions as total_sessions' => function ($q) {
                $q->where('status', 'completed')->where('is_absent', false);
            }])
            ->withCount(['mentoringSessions as sessions_p1' => function ($q) {
                $q->where('status', 'completed')
                  ->where('is_absent', false)
                  ->whereColumn('dosen_id', 'pembimbing1_id');
            }])
            ->withCount(['mentoringSessions as sessions_p2' => function ($q) {
                $q->where('status', 'completed')
                  ->where('is_absent', false)
                  ->whereColumn('dosen_id', 'pembimbing2_id');
            }])
            ->when($search, function ($query, $search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('identifier', 'like', "%{$search}%");
                })
                ->orWhere('title', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(['search' => $search]);

        return view('monitoring.index', compact('theses', 'search'));
    }

    public function export()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        return Excel::download(new MonitoringExport, 'monitoring-acc-lulus-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function revisions(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $search = $request->get('search');
        $activeWave = Wave::active() ?: Wave::where('is_active', true)->latest()->first() ?: Wave::latest()->first();
        $selectedWaveId = $request->get('wave_id', $activeWave?->id);

        $seminarDetails = \App\Models\SeminarScheduleDetail::with(['thesis.student', 'schedule', 'examiner1', 'examiner2', 'revisions'])
            ->whereHas('thesis') // Ensure there is a thesis
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
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $search = $request->get('search');
        $activeWave = Wave::active() ?: Wave::where('is_active', true)->latest()->first() ?: Wave::latest()->first();
        $selectedWaveId = $request->get('wave_id', $activeWave?->id);

        $defenseDetails = \App\Models\ThesisDefenseScheduleDetail::with(['thesis.student', 'schedule', 'examiner1', 'examiner2', 'revisions'])
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
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $search = $request->get('search');
        $activeWave = Wave::active() ?: Wave::where('is_active', true)->latest()->first() ?: Wave::latest()->first();
        $selectedWaveId = $request->get('wave_id', $activeWave?->id);

        $defenseDetails = \App\Models\ThesisDefenseScheduleDetail::with(['thesis.student', 'thesis.pembimbing1', 'schedule', 'examiner1', 'examiner2', 'revisions'])
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
}

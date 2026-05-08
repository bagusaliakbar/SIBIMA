<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Exports\MonitoringExport;
use Maatwebsite\Excel\Facades\Excel;

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
}

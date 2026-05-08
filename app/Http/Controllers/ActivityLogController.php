<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the logs.
     */
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $search = $request->input('search');
        $module = $request->input('module');

        $logs = ActivityLog::with('user')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('activity', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($uq) use ($search) {
                          $uq->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($module, function ($query, $module) {
                return $query->where('module', $module);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $search, 'module' => $module]);

        $modules = ActivityLog::distinct()->pluck('module')->filter();

        return view('logs.index', compact('logs', 'search', 'module', 'modules'));
    }

    /**
     * Export logs to Excel.
     */
    public function export(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $search = $request->input('search');
        $module = $request->input('module');

        $filename = "log_aktivitas_" . date('Y-m-d_H-i-s') . ".xlsx";
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ActivityLogsExport($search, $module), 
            $filename
        );
    }
}

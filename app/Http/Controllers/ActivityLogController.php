<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Exports\ActivityLogsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ActivityLogController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (Auth::user()->role !== 'admin') abort(403);
                return $next($request);
            }),
        ];
    }

    public function index(Request $request)
    {
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

    public function export(Request $request)
    {
        $search = $request->input('search');
        $module = $request->input('module');

        return Excel::download(
            new ActivityLogsExport($search, $module), 
            "log_aktivitas_" . date('Y-m-d_H-i-s') . ".xlsx"
        );
    }
}

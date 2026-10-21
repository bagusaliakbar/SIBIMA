<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wave;
use App\Models\Thesis;
use App\Models\ActivityLog;
use App\Models\ThesisDefenseScheduleDetail;
use App\Services\DashboardService;
use App\Services\MonitoringService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class AdvancedReportingController extends Controller implements HasMiddleware
{
    protected $dashboardService;
    protected $monitoringService;

    public function __construct(DashboardService $dashboardService, MonitoringService $monitoringService)
    {
        $this->dashboardService = $dashboardService;
        $this->monitoringService = $monitoringService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'kaprodi') {
                    abort(403);
                }
                return $next($request);
            }),
        ];
    }

    public function index()
    {
        $data = $this->dashboardService->getAdminData();

        $dosens = User::where('role', 'dosen')->where('is_active', true)->orderBy('name')->get();
        $waves = Wave::orderBy('id', 'desc')->get();
        $entryYears = User::where('role', 'mahasiswa')
            ->whereNotNull('entry_year')
            ->distinct()
            ->orderBy('entry_year', 'desc')
            ->pluck('entry_year');

        if ($entryYears->isEmpty()) {
            $currentYear = (int) now()->year;
            $entryYears = collect(range($currentYear, $currentYear - 6));
        }

        // Live preview collections (5 top records per report)
        $previewAkademik = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->withMentoringCounts()
            ->latest()
            ->take(5)
            ->get();

        $previewNilaiSidang = ThesisDefenseScheduleDetail::with([
            'thesis.student', 
            'thesis.pembimbing1', 
            'thesis.pembimbing2', 
            'examiner1', 
            'examiner2', 
            'revisions'
        ])->whereHas('thesis')->latest()->take(5)->get();

        $previewBebanDosen = User::where('role', 'dosen')
            ->withCount(['thesesAsP1', 'thesesAsP2'])
            ->orderBy('name')
            ->take(5)
            ->get();

        $previewKepatuhan = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->where('status', 'active')
            ->withMentoringCounts()
            ->get()
            ->sortByDesc(fn($t) => $t->days_since_last_mentoring ?? 0)
            ->take(5)
            ->values();

        $previewTopik = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->whereNotNull('title')
            ->latest()
            ->take(5)
            ->get();

        $previewKelulusan = Thesis::with(['student', 'pembimbing1', 'pembimbing2', 'graduation'])
            ->graduated()
            ->latest('updated_at')
            ->take(5)
            ->get();

        $previewLogs = ActivityLog::with('user')->latest()->take(5)->get();

        return view('monitoring.advanced-reporting', array_merge($data, compact(
            'dosens',
            'waves',
            'entryYears',
            'previewAkademik',
            'previewNilaiSidang',
            'previewBebanDosen',
            'previewKepatuhan',
            'previewTopik',
            'previewKelulusan',
            'previewLogs'
        )));
    }
}

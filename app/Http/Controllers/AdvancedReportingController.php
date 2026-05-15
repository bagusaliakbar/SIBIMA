<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class AdvancedReportingController extends Controller implements HasMiddleware
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (Auth::user()->role !== 'admin') {
                    abort(403);
                }
                return $next($request);
            }),
        ];
    }

    public function index()
    {
        $data = $this->dashboardService->getAdminData();
        return view('monitoring.advanced-reporting', $data);
    }
}

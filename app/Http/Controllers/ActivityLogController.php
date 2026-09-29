<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Exports\ActivityLogsExport;
use App\Exports\UserLoginActivityExport;
use Carbon\Carbon;
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
                if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'kaprodi') abort(403);
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

    /**
     * Dashboard monitoring keaktifan login user (Admin & Kaprodi)
     */
    public function loginActivity(Request $request)
    {
        $period = $request->input('period', 'this_month');
        $role = $request->input('role', 'all');
        $search = $request->input('search');
        $tab = $request->input('tab', 'active'); // 'active' or 'inactive'

        $startDate = null;
        $endDate = Carbon::now();

        switch ($period) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                break;
            case 'last_7_days':
                $startDate = Carbon::now()->subDays(7)->startOfDay();
                break;
            case 'last_30_days':
                $startDate = Carbon::now()->subDays(30)->startOfDay();
                break;
            case 'all_time':
                $startDate = null;
                $endDate = null;
                break;
            case 'this_month':
            default:
                $period = 'this_month';
                $startDate = Carbon::now()->startOfMonth()->startOfDay();
                break;
        }

        $periodLabels = [
            'today' => 'Hari Ini (' . Carbon::today()->locale('id')->translatedFormat('d M Y') . ')',
            'last_7_days' => '7 Hari Terakhir',
            'this_month' => 'Bulan Ini (' . Carbon::now()->locale('id')->translatedFormat('F Y') . ')',
            'last_30_days' => '30 Hari Terakhir',
            'all_time' => 'Semua Waktu',
        ];
        $currentPeriodLabel = $periodLabels[$period] ?? 'Bulan Ini';

        // --- KPI METRICS ---
        $loginLogQuery = ActivityLog::where('activity', 'Login');
        if ($startDate && $endDate) {
            $loginLogQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        if ($role && $role !== 'all') {
            $loginLogQuery->whereHas('user', function ($q) use ($role) {
                $q->where('role', $role);
            });
        }

        $totalPeriodLogins = (clone $loginLogQuery)->count();
        $uniqueActiveUsersCount = (clone $loginLogQuery)->distinct('user_id')->count('user_id');

        // Most active user in this period
        $topLoginUser = (clone $loginLogQuery)
            ->whereNotNull('user_id')
            ->selectRaw('user_id, COUNT(*) as login_count')
            ->groupBy('user_id')
            ->orderByDesc('login_count')
            ->first();

        $mostActiveUser = null;
        $mostActiveCount = 0;
        if ($topLoginUser) {
            $mostActiveUser = User::find($topLoginUser->user_id);
            $mostActiveCount = $topLoginUser->login_count;
        }

        // Inactive / Never logged in users count (Never or > 30 days)
        $neverOrInactiveCount = User::whereIn('role', ['mahasiswa', 'dosen', 'kaprodi', 'admin'])
            ->when($role && $role !== 'all', fn($q) => $q->where('role', $role))
            ->where(function ($q) {
                $q->whereNull('last_login_at')
                  ->orWhere('last_login_at', '<', Carbon::now()->subDays(30));
            })
            ->count();

        // --- TAB CONTENT QUERIES ---
        if ($tab === 'inactive') {
            // Users who never logged in or haven't logged in for > 30 days
            $users = User::whereIn('role', ['mahasiswa', 'dosen', 'kaprodi', 'admin'])
                ->when($role && $role !== 'all', fn($q) => $q->where('role', $role))
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%")
                           ->orWhere('identifier', 'like', "%{$search}%");
                    });
                })
                ->where(function ($q) {
                    $q->whereNull('last_login_at')
                      ->orWhere('last_login_at', '<', Carbon::now()->subDays(30));
                })
                ->withCount([
                    'activityLogs as total_logins_count' => function ($q) {
                        $q->where('activity', 'Login');
                    }
                ])
                ->orderByRaw('last_login_at IS NULL DESC')
                ->orderBy('last_login_at', 'asc')
                ->paginate(15)
                ->withQueryString();
        } else {
            // Leaderboard / Active users
            $users = User::whereIn('role', ['mahasiswa', 'dosen', 'kaprodi', 'admin'])
                ->when($role && $role !== 'all', fn($q) => $q->where('role', $role))
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%")
                           ->orWhere('identifier', 'like', "%{$search}%");
                    });
                })
                ->withCount([
                    'activityLogs as period_logins_count' => function ($q) use ($startDate, $endDate) {
                        $q->where('activity', 'Login');
                        if ($startDate && $endDate) {
                            $q->whereBetween('created_at', [$startDate, $endDate]);
                        }
                    },
                    'activityLogs as total_logins_count' => function ($q) {
                        $q->where('activity', 'Login');
                    }
                ])
                ->orderByDesc('period_logins_count')
                ->orderByDesc('total_logins_count')
                ->orderByDesc('last_login_at')
                ->paginate(15)
                ->withQueryString();
        }

        return view('logs.login_activity', compact(
            'users',
            'period',
            'currentPeriodLabel',
            'role',
            'search',
            'tab',
            'totalPeriodLogins',
            'uniqueActiveUsersCount',
            'mostActiveUser',
            'mostActiveCount',
            'neverOrInactiveCount'
        ));
    }

    /**
     * Detail riwayat sesi login user tertentu (JSON API untuk modal)
     */
    public function userLoginHistory(User $user)
    {
        $sessions = ActivityLog::where('user_id', $user->id)
            ->where('activity', 'Login')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'created_at_formatted' => $log->created_at->locale('id')->translatedFormat('d F Y, H:i:s') . ' WIB',
                    'time_ago' => $log->created_at->locale('id')->diffForHumans(),
                    'ip_address' => $log->ip_address ?: '127.0.0.1',
                    'device' => self::parseUserAgent($log->user_agent),
                    'raw_user_agent' => $log->user_agent ?: 'Unknown',
                ];
            });

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'identifier' => $user->identifier ?: '-',
                'role' => ucfirst($user->role),
                'avatar_url' => $user->avatar_url,
                'is_online' => $user->is_online,
                'last_login_at' => $user->last_login_at ? $user->last_login_at->locale('id')->translatedFormat('d F Y, H:i') : 'Belum pernah',
                'total_logins' => ActivityLog::where('user_id', $user->id)->where('activity', 'Login')->count(),
                'month_logins' => ActivityLog::where('user_id', $user->id)->where('activity', 'Login')->where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
                'week_logins' => ActivityLog::where('user_id', $user->id)->where('activity', 'Login')->where('created_at', '>=', Carbon::now()->subDays(7))->count(),
            ],
            'sessions' => $sessions
        ]);
    }

    /**
     * Export data keaktifan login ke Excel
     */
    public function exportLoginActivity(Request $request)
    {
        $period = $request->input('period', 'this_month');
        $role = $request->input('role', 'all');
        $search = $request->input('search');
        $tab = $request->input('tab', 'active');

        $roleSlug = $role !== 'all' ? "_{$role}" : "";
        $tabSlug = $tab === 'inactive' ? 'tidak_aktif' : 'teraktif';
        $filename = "keaktifan_login{$roleSlug}_{$tabSlug}_" . date('Y-m-d_His') . ".xlsx";

        return Excel::download(
            new UserLoginActivityExport($period, $role, $search, $tab),
            $filename
        );
    }

    /**
     * Parse User Agent string to friendly human-readable format
     */
    public static function parseUserAgent(?string $ua): string
    {
        if (empty($ua)) return 'Perangkat Tidak Diketahui';

        $os = 'Perangkat Lain';
        if (str_contains($ua, 'Windows NT 10.0') || str_contains($ua, 'Windows NT 11.0') || str_contains($ua, 'Windows')) {
            $os = 'Windows';
        } elseif (str_contains($ua, 'iPhone')) {
            $os = 'iPhone (iOS)';
        } elseif (str_contains($ua, 'iPad')) {
            $os = 'iPad (iPadOS)';
        } elseif (str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS')) {
            $os = 'macOS';
        } elseif (str_contains($ua, 'Android')) {
            $os = 'Android';
        } elseif (str_contains($ua, 'Linux')) {
            $os = 'Linux';
        }

        $browser = 'Web Browser';
        if (str_contains($ua, 'Edg')) {
            $browser = 'Microsoft Edge';
        } elseif (str_contains($ua, 'Chrome') && !str_contains($ua, 'Edg')) {
            $browser = 'Google Chrome';
        } elseif (str_contains($ua, 'Safari') && !str_contains($ua, 'Chrome')) {
            $browser = 'Safari';
        } elseif (str_contains($ua, 'Firefox')) {
            $browser = 'Mozilla Firefox';
        } elseif (str_contains($ua, 'Opera') || str_contains($ua, 'OPR')) {
            $browser = 'Opera';
        }

        return "{$browser} • {$os}";
    }
}

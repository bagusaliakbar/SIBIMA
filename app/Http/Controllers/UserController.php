<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'kaprodi') abort(403);
                return $next($request);
            }, except: ['avatar']),
        ];
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'all');
        $role = $request->input('role', 'all');
        $cohortFilter = $request->input('cohort_filter', 'all');
        $entryYear = $request->input('entry_year');
        $perPage = in_array((int) $request->input('per_page'), [10, 25, 50, 100]) ? (int) $request->input('per_page') : 10;

        // Cohort threshold year calculation (following SIBIMA standard)
        $currentYear = now()->year;
        $isSecondHalf = now()->month >= 9;
        $oldCohortThresholdYear = $isSecondHalf ? ($currentYear - 4) : ($currentYear - 5);

        // Role Counts for quick filter tabs
        $rawRoleCounts = User::whereIn('role', ['dosen', 'mahasiswa', 'kaprodi'])
            ->selectRaw("
                COUNT(*) as all_count,
                SUM(CASE WHEN role = 'mahasiswa' THEN 1 ELSE 0 END) as mahasiswa,
                SUM(CASE WHEN role = 'dosen' THEN 1 ELSE 0 END) as dosen,
                SUM(CASE WHEN role = 'kaprodi' THEN 1 ELSE 0 END) as kaprodi
            ")->first();

        $roleCounts = [
            'all' => (int) ($rawRoleCounts->all_count ?? 0),
            'mahasiswa' => (int) ($rawRoleCounts->mahasiswa ?? 0),
            'dosen' => (int) ($rawRoleCounts->dosen ?? 0),
            'kaprodi' => (int) ($rawRoleCounts->kaprodi ?? 0),
        ];

        // Status Counts
        $rawStatusCounts = User::whereIn('role', ['dosen', 'mahasiswa', 'kaprodi'])
            ->when($role !== 'all', fn($q) => $q->where('role', $role))
            ->selectRaw("
                COUNT(*) as all_count,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as pending
            ")->first();

        $statusCounts = [
            'all' => (int) ($rawStatusCounts->all_count ?? 0),
            'active' => (int) ($rawStatusCounts->active ?? 0),
            'pending' => (int) ($rawStatusCounts->pending ?? 0),
        ];

        // Cohort Counts for students
        $rawCohortCounts = User::where('role', 'mahasiswa')
            ->selectRaw("
                COUNT(*) as all_count,
                SUM(CASE WHEN entry_year > ? OR entry_year IS NULL THEN 1 ELSE 0 END) as new_count,
                SUM(CASE WHEN entry_year <= ? THEN 1 ELSE 0 END) as old_count
            ", [$oldCohortThresholdYear, $oldCohortThresholdYear])->first();

        $cohortCounts = [
            'all' => (int) ($rawCohortCounts->all_count ?? 0),
            'new' => (int) ($rawCohortCounts->new_count ?? 0),
            'old' => (int) ($rawCohortCounts->old_count ?? 0),
        ];

        // Distinct available entry years for dropdown
        $availableEntryYears = User::where('role', 'mahasiswa')
            ->whereNotNull('entry_year')
            ->distinct()
            ->orderBy('entry_year', 'desc')
            ->pluck('entry_year');

        // Global KPI Stats for top interactive summary cards
        $totalSupervisedTheses = \App\Models\Thesis::where('status', 'active')
            ->where(function ($q) {
                $q->whereNotNull('pembimbing1_id')->orWhereNotNull('pembimbing2_id');
            })->count();

        $rawKpi = User::whereIn('role', ['dosen', 'mahasiswa', 'kaprodi'])
            ->selectRaw("
                COUNT(*) as total_users,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as total_active,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as total_pending,
                SUM(CASE WHEN role = 'dosen' AND is_active = 1 THEN 1 ELSE 0 END) as active_dosen,
                SUM(CASE WHEN role = 'mahasiswa' AND is_active = 1 THEN 1 ELSE 0 END) as active_mahasiswa,
                SUM(CASE WHEN role = 'kaprodi' AND is_active = 1 THEN 1 ELSE 0 END) as active_kaprodi
            ")->first();

        $kpiStats = [
            'total_users' => (int) ($rawKpi->total_users ?? 0),
            'total_active' => (int) ($rawKpi->total_active ?? 0),
            'total_pending' => (int) ($rawKpi->total_pending ?? 0),
            'active_dosen' => (int) ($rawKpi->active_dosen ?? 0),
            'total_supervised' => $totalSupervisedTheses,
            'active_mahasiswa' => (int) ($rawKpi->active_mahasiswa ?? 0),
            'active_kaprodi' => (int) ($rawKpi->active_kaprodi ?? 0),
        ];

        // Main Query
        $usersQuery = User::whereIn('role', ['dosen', 'mahasiswa', 'kaprodi'])
            ->with([
                'thesis.pembimbing1',
                'thesis.pembimbing2',
                'thesesAsP1' => fn($q) => $q->where('status', 'active')->with('student'),
                'thesesAsP2' => fn($q) => $q->where('status', 'active')->with('student'),
            ])
            ->when($role !== 'all', fn($q) => $q->where('role', $role))
            ->when($status !== 'all', function ($query) use ($status) {
                if ($status === 'active') return $query->where('is_active', true);
                if ($status === 'pending') return $query->where('is_active', false);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('identifier', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%");
                });
            });

        // Filter Cohort Type (new vs old)
        if ($cohortFilter === 'new') {
            $usersQuery->where(function ($q) use ($oldCohortThresholdYear) {
                $q->where('role', '!=', 'mahasiswa')
                  ->orWhere('entry_year', '>', $oldCohortThresholdYear)
                  ->orWhereNull('entry_year');
            });
        } elseif ($cohortFilter === 'old') {
            $usersQuery->where('role', 'mahasiswa')
                       ->where('entry_year', '<=', $oldCohortThresholdYear);
        }

        // Filter Specific Entry Year
        if (!empty($entryYear) && $entryYear !== 'all') {
            $usersQuery->where('entry_year', $entryYear);
        }

        $users = $usersQuery->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends([
                'search' => $search,
                'status' => $status,
                'role' => $role,
                'cohort_filter' => $cohortFilter,
                'entry_year' => $entryYear,
                'per_page' => $perPage,
            ]);

        return view('users.index', compact(
            'users',
            'search',
            'status',
            'role',
            'cohortFilter',
            'entryYear',
            'perPage',
            'roleCounts',
            'statusCounts',
            'cohortCounts',
            'availableEntryYears',
            'oldCohortThresholdYear',
            'kpiStats'
        ));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Password::defaults()],
            'role' => ['required', 'in:dosen,mahasiswa,kaprodi'],
            'identifier' => ['required', 'string', 'max:50', 'unique:'.User::class],
            'entry_year' => ['nullable', 'integer', 'min:2000', 'max:'.(date('Y') + 1)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $this->userService->createUser($data, $request->file('avatar'));

        return redirect()->route('users.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'Tidak dapat mengedit akun administrator.');
        }

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'Tidak dapat mengedit akun administrator.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'role' => ['required', 'in:dosen,mahasiswa,kaprodi'],
            'identifier' => ['required', 'string', 'max:50', 'unique:'.User::class.',identifier,'.$user->id],
            'entry_year' => ['nullable', 'integer', 'min:2000', 'max:'.(date('Y') + 1)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'password' => ['nullable', Password::defaults()],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $this->userService->updateUser($user, $data, $request->file('avatar'));

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'Tidak dapat menghapus akun administrator.');
        }

        $this->userService->deleteUser($user);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $data = $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['required', 'exists:users,id'],
        ]);

        $deletedCount = $this->userService->bulkDeleteUsers($data['user_ids']);

        if ($deletedCount === 0) {
            return redirect()->route('users.index')->with('error', 'Tidak ada pengguna yang dapat dihapus.');
        }

        return redirect()->route('users.index')->with('success', "Berhasil menghapus {$deletedCount} pengguna secara massal.");
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'all');
        $role = $request->input('role', 'all');
        $cohortFilter = $request->input('cohort_filter', 'all');
        $entryYear = $request->input('entry_year');

        return Excel::download(
            new UsersExport($search, $status, $role, $cohortFilter, $entryYear),
            "data_pengguna_" . date('Y-m-d_H-i-s') . ".xlsx"
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        $import = new UsersImport;
        Excel::import($import, $request->file('excel_file'));

        $message = "Berhasil mengimpor {$import->importedCount} pengguna.";
        if ($import->skippedCount > 0) {
            $message .= " ({$import->skippedCount} baris dilewati).";
        }

        if (count($import->skippedDetails) > 0) {
            return redirect()->route('users.index')
                ->with('success', $message)
                ->with('skippedDetails', $import->skippedDetails);
        }

        return redirect()->route('users.index')->with('success', $message);
    }

    public function toggleStatus(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'Tidak dapat mengubah status administrator.');
        }

        $status = $this->userService->toggleStatus($user);
        
        return redirect()->route('users.index')->with('success', "Akun pengguna berhasil {$status}.");
    }

    public function resetPassword(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'Tidak dapat mereset password administrator.');
        }

        $newPassword = filled($user->identifier) ? (string) $user->identifier : \Illuminate\Support\Str::random(10);
        $user->password = bcrypt($newPassword);
        $user->save();

        // Invalidate active database sessions for the user whose password was reset
        try {
            \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $user->id)->delete();
            \App\Models\ActivityLog::log(
                'Reset Password',
                "Admin mereset password untuk pengguna {$user->name} ({$user->identifier}).",
                'Users'
            );
        } catch (\Throwable $e) {}

        return redirect()->route('users.index')->with('success', "Password untuk pengguna {$user->name} berhasil di-reset menjadi: {$newPassword}");
    }

    /**
     * Stream user avatar as a lightweight cached HTTP image response.
     */
    public function avatar(User $user)
    {
        if ($user->avatar) {
            // If stored as base64 data URI
            if (str_starts_with($user->avatar, 'data:image')) {
                if (preg_match('/^data:([^;]+);base64,(.+)$/', $user->avatar, $matches)) {
                    $mime = $matches[1];
                    $data = base64_decode($matches[2]);
                    return response($data, 200, [
                        'Content-Type' => $mime,
                        'Cache-Control' => 'public, max-age=86400',
                        'ETag' => md5(($user->updated_at ? $user->updated_at->timestamp : $user->id) . strlen($user->avatar)),
                    ]);
                }
            }

            // If stored as storage path
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                return \Illuminate\Support\Facades\Storage::disk('public')->response($user->avatar, null, [
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }
        }

        // Fallback default SVG silhouette
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#94a3b8"><path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.6-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd"/></svg>';

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}

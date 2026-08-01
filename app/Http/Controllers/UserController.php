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
            }),
        ];
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'all');

        $users = User::whereIn('role', ['dosen', 'mahasiswa', 'kaprodi'])
            ->when($status !== 'all', function ($query) use ($status) {
                if ($status === 'active') return $query->where('is_active', true);
                if ($status === 'pending') return $query->where('is_active', false);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('identifier', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $search, 'status' => $status]);

        return view('users.index', compact('users', 'search', 'status'));
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

    public function export()
    {
        return Excel::download(new UsersExport, "data_pengguna_" . date('Y-m-d_H-i-s') . ".xlsx");
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

        $newPassword = $user->identifier;
        $user->password = bcrypt($newPassword);
        $user->save();

        return redirect()->route('users.index')->with('success', "Password untuk pengguna {$user->name} berhasil di-reset menjadi: {$newPassword}");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\ActivityLog;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $search = $request->get('search');

        // Only show dosen and mahasiswa
        $users = User::whereIn('role', ['dosen', 'mahasiswa'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('identifier', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('users.index', compact('users', 'search'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', \Illuminate\Validation\Rules\Password::defaults()],
            'role' => ['required', 'in:dosen,mahasiswa'],
            'identifier' => ['required', 'string', 'max:50', 'unique:'.User::class],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'identifier' => $request->identifier,
            'avatar' => $avatarPath,
            'is_active' => true,
        ]);

        ActivityLog::log('Tambah Pengguna', "Admin menambahkan pengguna baru: {$request->name} ({$request->role}).", 'User');

        return redirect()->route('users.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }
    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        if ($user->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'Tidak dapat mengedit akun administrator.');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        if ($user->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'Tidak dapat mengedit akun administrator.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'role' => ['required', 'in:dosen,mahasiswa'],
            'identifier' => ['required', 'string', 'max:50', 'unique:'.User::class.',identifier,'.$user->id],
            'password' => ['nullable', \Illuminate\Validation\Rules\Password::defaults()],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'identifier' => $request->identifier,
        ];

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        ActivityLog::log('Edit Pengguna', "Admin memperbarui data pengguna: {$user->name}.", 'User');

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        // Prevent admin from deleting themselves or other admins
        if ($user->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'Tidak dapat menghapus akun administrator.');
        }

        $user->delete();

        ActivityLog::log('Hapus Pengguna', "Admin menghapus pengguna: {$user->name}.", 'User');

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Export users to Excel.
     */
    public function export()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $filename = "data_pengguna_" . date('Y-m-d_H-i-s') . ".xlsx";
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UsersExport, $filename);
    }

    /**
     * Import users from Excel.
     */
    public function import(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ], [
            'excel_file.required' => 'Silakan unggah file Excel/CSV.',
            'excel_file.mimes' => 'File harus berformat .xlsx, .xls, atau .csv.',
            'excel_file.max' => 'Ukuran file tidak boleh lebih dari 2MB.'
        ]);

        $import = new \App\Imports\UsersImport;
        \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('excel_file'));

        $message = "Berhasil mengimpor {$import->importedCount} pengguna.";
        if ($import->skippedCount > 0) {
            $message .= " ({$import->skippedCount} baris dilewati karena format tidak valid atau data sudah terdaftar).";
        }

        return redirect()->route('users.index')->with('success', $message);
    }

    /**
     * Toggle the active status of a user.
     */
    public function toggleStatus(User $user)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        if ($user->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'Tidak dapat mengubah status akun administrator.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        ActivityLog::log('Toggle Status User', "Admin {$status} akun pengguna: {$user->name}.", 'User');
        
        return redirect()->route('users.index')->with('success', "Akun pengguna berhasil {$status}.");
    }
}

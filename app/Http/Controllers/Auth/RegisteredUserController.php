<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'in:mahasiswa,dosen'],
            'identifier' => ['required', 'string', 'max:50', 'unique:'.User::class.',identifier'],
            'username' => ['nullable', 'string', 'max:50', 'unique:'.User::class.',username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if ($request->role === 'mahasiswa') {
            $rules['entry_year'] = ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 1)];
        }

        $validated = $request->validate($rules);

        $username = !empty($validated['username']) ? $validated['username'] : $validated['identifier'];

        $user = User::create([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'identifier' => $validated['identifier'],
            'username' => $username,
            'email' => $validated['email'],
            'entry_year' => $validated['entry_year'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => false,
        ]);

        event(new Registered($user));

        ActivityLog::create([
            'user_id'     => $user->id,
            'activity'    => 'Registrasi Akun',
            'description' => "Pengguna baru {$user->name} ({$user->role}) mendaftar akun baru dengan Identitas {$user->identifier}.",
            'module'      => 'Auth',
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);

        return redirect()->route('login')->with('status', 'Registrasi berhasil! Akun Anda sedang menunggu validasi dari Admin.');
    }
}


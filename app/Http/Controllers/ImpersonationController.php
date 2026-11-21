<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public const SESSION_IMPERSONATOR_ID = 'impersonator_id';
    public const SESSION_IMPERSONATOR_NAME = 'impersonator_name';
    public const SESSION_IMPERSONATOR_ROLE = 'impersonator_role';
    public const SESSION_RETURN_URL = 'impersonation_return_url';

    /**
     * Check whether the current session is in Login As (impersonation) mode.
     */
    public static function isImpersonating(?Request $request = null): bool
    {
        $session = $request ? $request->session() : session();

        return $session->has(self::SESSION_IMPERSONATOR_ID);
    }

    /**
     * Start impersonating a dosen or mahasiswa account (admin & kaprodi only).
     */
    public function start(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();

        if (!$actor || !in_array($actor->role, ['admin', 'kaprodi'], true)) {
            abort(403, 'Hanya Admin atau Kaprodi yang dapat menggunakan fitur Login As.');
        }

        if ($request->session()->has(self::SESSION_IMPERSONATOR_ID)) {
            return redirect()->back()->with('error', 'Anda sedang dalam mode Login As. Kembali ke akun utama terlebih dahulu.');
        }

        if ($actor->id === $user->id) {
            return redirect()->back()->with('error', 'Tidak dapat melakukan Login As ke akun Anda sendiri.');
        }

        if (!in_array($user->role, ['dosen', 'mahasiswa'], true)) {
            abort(403, 'Fitur Login As hanya diperbolehkan untuk akun Dosen atau Mahasiswa.');
        }

        if (!$user->is_active) {
            return redirect()->back()->with('error', "Akun {$user->name} sedang nonaktif/pending. Aktifkan akun terlebih dahulu sebelum menggunakan Login As.");
        }

        // Preserve internal users.index filter query string safely if available
        $usersIndexUrl = route('users.index');
        $previousUrl = url()->previous();
        $returnUrl = str_starts_with($previousUrl, $usersIndexUrl) ? $previousUrl : $usersIndexUrl;

        // Record audit log under the real Admin/Kaprodi account BEFORE switching auth user
        $roleLabel = $user->role === 'dosen' ? 'Dosen' : 'Mahasiswa';
        ActivityLog::log(
            'Login As (Mulai)',
            "{$actor->name} (" . strtoupper($actor->role) . ") masuk sebagai {$user->name} ({$roleLabel}, {$user->identifier}) dari IP {$request->ip()}.",
            'Auth',
            $user
        );

        // Switch authenticated user without updating target's last_login_at or setting remember cookie
        $request->session()->forget('using_default_password');
        Auth::guard('web')->login($user, false);
        $request->session()->regenerate();

        $request->session()->put([
            self::SESSION_IMPERSONATOR_ID => $actor->id,
            self::SESSION_IMPERSONATOR_NAME => $actor->name,
            self::SESSION_IMPERSONATOR_ROLE => $actor->role,
            self::SESSION_RETURN_URL => $returnUrl,
            'last_activity_at' => now()->timestamp,
        ]);

        return redirect()->route('dashboard')->with(
            'info',
            "Mode Login As aktif: Anda sekarang masuk sebagai {$user->name} ({$roleLabel})."
        );
    }

    /**
     * Leave impersonation mode and restore the original Admin/Kaprodi session.
     */
    public function leave(Request $request): RedirectResponse
    {
        $impersonatorId = $request->session()->get(self::SESSION_IMPERSONATOR_ID);

        if (!$impersonatorId) {
            abort(403, 'Tidak ada sesi Login As yang aktif.');
        }

        $impersonatedUser = $request->user();
        $returnUrl = (string) $request->session()->get(self::SESSION_RETURN_URL, route('users.index'));
        $usersIndexUrl = route('users.index');
        if (!str_starts_with($returnUrl, $usersIndexUrl)) {
            $returnUrl = $usersIndexUrl;
        }

        $impersonator = User::find($impersonatorId);

        // Fail-closed: if original account is missing, deactivated, or demoted, destroy session
        if (!$impersonator || !$impersonator->is_active || !in_array($impersonator->role, ['admin', 'kaprodi'], true)) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'username' => 'Sesi akun utama tidak lagi valid. Silakan login kembali.',
            ]);
        }

        $request->session()->forget([
            self::SESSION_IMPERSONATOR_ID,
            self::SESSION_IMPERSONATOR_NAME,
            self::SESSION_IMPERSONATOR_ROLE,
            self::SESSION_RETURN_URL,
            'using_default_password',
        ]);

        Auth::guard('web')->login($impersonator, false);
        $request->session()->regenerate();
        $request->session()->put('last_activity_at', now()->timestamp);

        $targetInfo = $impersonatedUser
            ? "{$impersonatedUser->name} (" . ucfirst($impersonatedUser->role) . ", {$impersonatedUser->identifier})"
            : 'pengguna';

        ActivityLog::log(
            'Login As (Selesai)',
            "{$impersonator->name} (" . strtoupper($impersonator->role) . ") keluar dari mode Login As ({$targetInfo}) dan kembali ke akun utama dari IP {$request->ip()}.",
            'Auth',
            $impersonatedUser
        );

        return redirect()->to($returnUrl)->with(
            'success',
            "Berhasil keluar dari mode Login As dan kembali ke akun {$impersonator->name}."
        );
    }
}

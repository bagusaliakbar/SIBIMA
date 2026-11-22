<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckMaintenanceMode;
use App\Models\ActivityLog;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MaintenanceModeController extends Controller
{
    public function index(): View
    {
        $config = Setting::getMaintenanceConfig();
        $config['bypass_secret'] = Setting::getOrCreateMaintenanceBypassSecret();
        $endsAtInput = '';

        if (!empty($config['ends_at'])) {
            try {
                $endsAtInput = Carbon::parse($config['ends_at'])
                    ->timezone(config('app.timezone', 'Asia/Jakarta'))
                    ->format('Y-m-d\TH:i');
            } catch (\Throwable $e) {
                $endsAtInput = '';
            }
        }

        return view('admin.maintenance.index', compact('config', 'endsAtInput'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'mode' => ['required', 'in:banner,lock'],
            'target' => ['required', 'in:both,mahasiswa,dosen'],
            'title' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:1000'],
            'estimate' => ['nullable', 'string', 'max:120'],
            'ends_at' => ['nullable', 'date'],
            'block_guest' => ['nullable', 'boolean'],
            'bypass_secret' => ['nullable', 'string', 'min:6', 'max:64', 'alpha_dash'],
        ], [
            'title.required' => 'Judul pemberitahuan pemeliharaan wajib diisi.',
            'message.required' => 'Pesan pemberitahuan pemeliharaan wajib diisi.',
            'ends_at.date' => 'Format target waktu hitung mundur tidak valid.',
            'bypass_secret.min' => 'Kode rahasia URL akses minimal 6 karakter.',
            'bypass_secret.alpha_dash' => 'Kode rahasia URL hanya boleh berisi huruf, angka, tanda hubung (-), atau garis bawah (_).',
        ]);

        $enabled = $request->boolean('enabled');
        $blockGuest = $validated['mode'] === 'lock' && $request->boolean('block_guest');
        $endsAtIso = '';

        if (!empty($validated['ends_at'])) {
            $endsAtIso = Carbon::parse($validated['ends_at'], config('app.timezone', 'Asia/Jakarta'))
                ->toIso8601String();
        }

        $bypassSecret = trim((string) ($validated['bypass_secret'] ?? ''));
        if ($bypassSecret === '') {
            $bypassSecret = Setting::getOrCreateMaintenanceBypassSecret();
        }

        Setting::saveMaintenanceConfig([
            'enabled' => $enabled,
            'mode' => $validated['mode'],
            'target' => $validated['target'],
            'title' => trim($validated['title']),
            'message' => trim($validated['message']),
            'estimate' => trim((string) ($validated['estimate'] ?? '')),
            'ends_at' => $endsAtIso,
            'block_guest' => $blockGuest,
            'bypass_secret' => $bypassSecret,
        ]);

        $modeLabel = $validated['mode'] === 'lock' ? 'Kunci Akses (Halaman Pemeliharaan)' : 'Banner Pemberitahuan';
        $targetLabel = match ($validated['target']) {
            'mahasiswa' => 'Mahasiswa',
            'dosen' => 'Dosen',
            default => 'Mahasiswa & Dosen',
        };
        $guestSuffix = $blockGuest ? ' + Kunci Sebelum Login' : '';
        $statusText = $enabled ? "MENGAKTIFKAN ({$modeLabel}{$guestSuffix} untuk {$targetLabel})" : 'MENONAKTIFKAN';

        ActivityLog::log(
            'Pengaturan Mode Pemeliharaan',
            Auth::user()->name . " {$statusText} Mode Pemeliharaan Sistem.",
            'System'
        );

        $flash = $enabled
            ? "Mode Pemeliharaan berhasil diaktifkan ({$modeLabel}{$guestSuffix} untuk {$targetLabel})."
            : 'Mode Pemeliharaan berhasil dinonaktifkan. Seluruh pengguna kini dapat mengakses SIBIMA seperti biasa.';

        return redirect()->route('admin.maintenance.index')->with('success', $flash);
    }

    /**
     * Secret URL entry point allowing Admin & Kaprodi to reach /login during guest-locked maintenance.
     */
    public function bypass(Request $request, string $secret): RedirectResponse
    {
        $config = Setting::getMaintenanceConfig();
        $storedSecret = trim((string) ($config['bypass_secret'] ?? ''));

        if ($storedSecret === '' || !hash_equals($storedSecret, $secret)) {
            abort(404);
        }

        $request->session()->put(
            CheckMaintenanceMode::SESSION_BYPASS_KEY,
            hash('sha256', $storedSecret)
        );

        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'kaprodi'], true)) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('login');
    }
}

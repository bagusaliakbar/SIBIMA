<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MaintenanceModeController extends Controller
{
    public function index(): View
    {
        $config = Setting::getMaintenanceConfig();

        return view('admin.maintenance.index', compact('config'));
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
        ], [
            'title.required' => 'Judul pemberitahuan pemeliharaan wajib diisi.',
            'message.required' => 'Pesan pemberitahuan pemeliharaan wajib diisi.',
        ]);

        $enabled = $request->boolean('enabled');

        Setting::saveMaintenanceConfig([
            'enabled' => $enabled,
            'mode' => $validated['mode'],
            'target' => $validated['target'],
            'title' => trim($validated['title']),
            'message' => trim($validated['message']),
            'estimate' => trim((string) ($validated['estimate'] ?? '')),
        ]);

        $modeLabel = $validated['mode'] === 'lock' ? 'Kunci Akses (Halaman Pemeliharaan)' : 'Banner Pemberitahuan';
        $targetLabel = match ($validated['target']) {
            'mahasiswa' => 'Mahasiswa',
            'dosen' => 'Dosen',
            default => 'Mahasiswa & Dosen',
        };
        $statusText = $enabled ? "MENGAKTIFKAN ({$modeLabel} untuk {$targetLabel})" : 'MENONAKTIFKAN';

        ActivityLog::log(
            'Pengaturan Mode Pemeliharaan',
            Auth::user()->name . " {$statusText} Mode Pemeliharaan Sistem.",
            'System'
        );

        $flash = $enabled
            ? "Mode Pemeliharaan berhasil diaktifkan ({$modeLabel} untuk {$targetLabel})."
            : 'Mode Pemeliharaan berhasil dinonaktifkan. Seluruh pengguna kini dapat mengakses SIBIMA seperti biasa.';

        return redirect()->route('admin.maintenance.index')->with('success', $flash);
    }
}

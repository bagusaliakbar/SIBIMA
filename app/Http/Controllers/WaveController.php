<?php

namespace App\Http\Controllers;

use App\Models\Wave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class WaveController extends Controller implements HasMiddleware
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

    public function index()
    {
        $waves = Wave::orderBy('created_at', 'desc')->paginate(10);
        return view('waves.index', compact('waves'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        Wave::create($data);

        return redirect()->route('waves.index')->with('success', 'Gelombang berhasil dibuat.');
    }

    public function update(Request $request, Wave $wave)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $wave->update($data);

        return redirect()->route('waves.index')->with('success', 'Gelombang berhasil diperbarui.');
    }

    public function toggle(Wave $wave)
    {
        // Deactivate all others if activating this one
        if (!$wave->is_active) {
            Wave::where('is_active', true)->update(['is_active' => false]);
        }

        $wave->update(['is_active' => !$wave->is_active]);

        return redirect()->back()->with('success', 'Status gelombang berhasil diubah.');
    }

    public function destroy(Wave $wave)
    {
        $wave->delete();
        return redirect()->route('waves.index')->with('success', 'Gelombang berhasil dihapus.');
    }
}

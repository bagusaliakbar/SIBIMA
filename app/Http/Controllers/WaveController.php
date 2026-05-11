<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wave;

class WaveController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $waves = Wave::orderBy('created_at', 'desc')->paginate(10);
        return view('waves.index', compact('waves'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        Wave::create($request->all());

        return redirect()->route('waves.index')->with('success', 'Gelombang berhasil dibuat.');
    }

    public function update(Request $request, Wave $wave)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $wave->update($request->all());

        return redirect()->route('waves.index')->with('success', 'Gelombang berhasil diperbarui.');
    }

    public function toggle(Wave $wave)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        
        // Deactivate all others if activating this one
        if (!$wave->is_active) {
            Wave::where('is_active', true)->update(['is_active' => false]);
        }

        $wave->update(['is_active' => !$wave->is_active]);

        return redirect()->back()->with('success', 'Status gelombang berhasil diubah.');
    }

    public function destroy(Wave $wave)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $wave->delete();
        return redirect()->route('waves.index')->with('success', 'Gelombang berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\LetterSetting;
use Illuminate\Http\Request;

class LetterSettingController extends Controller
{
    public function index()
    {
        $settings = LetterSetting::all();
        return view('admin.letter_settings.index', compact('settings'));
    }

    public function update(Request $request, LetterSetting $letterSetting)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'format' => 'required|string|max:255',
            'last_number' => 'required|integer|min:0',
        ]);

        $letterSetting->update($validated);

        return redirect()->back()->with('success', 'Pengaturan nomor surat berhasil diperbarui.');
    }
}

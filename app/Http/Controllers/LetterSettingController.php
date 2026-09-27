<?php

namespace App\Http\Controllers;

use App\Models\LetterSetting;
use Illuminate\Http\Request;

class LetterSettingController extends Controller
{
    public function index()
    {
        $defaults = [
            'sk_penguji_seminar' => [
                'title' => 'SK Tim Penguji Seminar',
                'format' => '[NUMBER]/SK/UNSUB/FIK/[MONTH]/[YEAR]',
            ],
            'sk_penguji_sidang' => [
                'title' => 'SK Tim Penguji Sidang',
                'format' => '[NUMBER]/SK/UNSUB/FIK/[MONTH]/[YEAR]',
            ],
            'sk_pembimbing' => [
                'title' => 'SK Dosen Pembimbing Skripsi',
                'format' => '[NUMBER]/SK-PEMBIMBING/UNSUB/FIK/[ROMAN_MONTH]/[YEAR]',
            ],
            'surat_tugas_pembimbing' => [
                'title' => 'Surat Tugas Dosen Pembimbing (BKD)',
                'format' => '[NUMBER]/PD.1.2/FIK-US/[ROMAN_MONTH]/[YEAR]',
            ],
            'surat_keterangan_lulus' => [
                'title' => 'Surat Keterangan Lulus (SKL)',
                'format' => '[NUMBER]/SKL/UNSUB/FIK/[ROMAN_MONTH]/[YEAR]',
            ],
        ];

        foreach ($defaults as $type => $data) {
            LetterSetting::firstOrCreate(
                ['type' => $type],
                [
                    'title' => $data['title'],
                    'format' => $data['format'],
                    'last_number' => 0,
                ]
            );
        }

        $settings = LetterSetting::all();
        return view('letter_settings.index', compact('settings'));
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

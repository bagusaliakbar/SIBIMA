<?php

namespace App\Imports;

use App\Models\User;
use App\Services\ThesisService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ThesesImport implements ToCollection, WithHeadingRow
{
    protected $thesisService;

    public function __construct(ThesisService $thesisService)
    {
        $this->thesisService = $thesisService;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Cek ketersediaan data wajib
            if (empty($row['nim']) || empty($row['judul']) || empty($row['nidn_pembimbing_1']) || empty($row['nidn_pembimbing_2'])) {
                continue;
            }

            // Cari user mahasiswa berdasarkan NIM (identifier)
            $student = User::where('identifier', $row['nim'])->where('role', 'mahasiswa')->first();
            if (!$student) continue;

            // Cari dosen berdasarkan NIDN (identifier)
            $pembimbing1 = User::where('identifier', $row['nidn_pembimbing_1'])->where('role', 'dosen')->first();
            $pembimbing2 = User::where('identifier', $row['nidn_pembimbing_2'])->where('role', 'dosen')->first();
            
            if (!$pembimbing1 || !$pembimbing2) continue;

            // Pastikan mahasiswa belum punya skripsi
            if ($student->thesis) continue;

            // Set current stage (default Bimbingan Skripsi)
            $stage = $row['tahapan_saat_ini'] ?? 'Bimbingan Skripsi';
            $validStages = ['Bimbingan Skripsi', 'Selesai Seminar UP', 'Siap Sidang'];
            if (!in_array($stage, $validStages)) {
                $stage = 'Bimbingan Skripsi';
            }

            $data = [
                'student_id' => $student->id,
                'title' => $row['judul'],
                'abstract' => $row['abstrak'] ?? null,
                'pembimbing1_id' => $pembimbing1->id,
                'pembimbing2_id' => $pembimbing2->id,
                'current_stage' => $stage,
            ];

            $this->thesisService->createMigrationThesis($data);
        }
    }
}

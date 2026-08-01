<?php

namespace App\Imports;

use App\Models\ThesisRepository;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class ThesisRepositoriesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Check if essential fields are present
            if (!isset($row['nama_mahasiswa']) || !isset($row['judul_skripsi']) || !isset($row['tahun_lulus'])) {
                continue; // Skip invalid rows
            }

            try {
                ThesisRepository::create([
                    'identifier' => $row['npm'] ?? null,
                    'name' => $row['nama_mahasiswa'],
                    'year' => $row['tahun_lulus'],
                    'title' => $row['judul_skripsi'],
                    'abstract' => $row['abstrak'] ?? null,
                    'pembimbing1' => $row['pembimbing_1'] ?? null,
                    'pembimbing2' => $row['pembimbing_2'] ?? null,
                ]);
            } catch (\Exception $e) {
                Log::error('Gagal import repositori: ' . $e->getMessage(), ['row' => $row]);
            }
        }
    }
}

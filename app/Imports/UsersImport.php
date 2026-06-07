<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;

class UsersImport implements ToCollection
{
    public $importedCount = 0;
    public $skippedCount = 0;
    public $skippedDetails = []; // Tracks row number and reason for failure

    public function collection(Collection $rows)
    {
        $header = true;
        $rowNum = 0;

        foreach ($rows as $row) {
            $rowNum++;
            if ($header) {
                $header = false;
                continue; // Skip the header row
            }

            // Check if row is completely empty (common in Excel files at the bottom)
            $isEmpty = true;
            foreach ($row as $cell) {
                if ($cell !== null && trim($cell) !== '') {
                    $isEmpty = false;
                    break;
                }
            }
            if ($isEmpty) {
                // Quietly ignore completely blank rows to avoid false alarms
                continue;
            }

            // Ensure row has at least 4 columns (Nama, Email, Peran, NPM/NIDN are mandatory)
            if (!isset($row[0]) || !isset($row[1]) || !isset($row[2]) || !isset($row[3])) {
                $this->skippedCount++;
                $this->skippedDetails[] = [
                    'row' => $rowNum,
                    'identifier' => '-',
                    'reason' => 'Kolom wajib (Nama, Email, Peran, NPM/NIDN) tidak lengkap'
                ];
                continue;
            }

            $name = trim($row[0]);
            $email = trim($row[1]);
            $role = strtolower(trim($row[2]));
            $identifier = trim($row[3]);

            // Optional fields
            $entryYear = isset($row[4]) && trim($row[4]) !== '' ? intval(trim($row[4])) : null;
            $phoneNumber = isset($row[5]) && trim($row[5]) !== '' ? trim($row[5]) : null;

            // Status field
            $isActive = true; // Default to active
            if (isset($row[6]) && trim($row[6]) !== '') {
                $statusVal = strtolower(trim($row[6]));
                if ($statusVal === '0' || $statusVal === 'pending' || $statusVal === 'tidak aktif' || $statusVal === 'false') {
                    $isActive = false;
                }
            }

            if (empty($name) || empty($email) || empty($identifier) || empty($role)) {
                $this->skippedCount++;
                $this->skippedDetails[] = [
                    'row' => $rowNum,
                    'identifier' => $identifier ?: '-',
                    'reason' => 'Nama, Email, Peran, atau NPM/NIDN kosong'
                ];
                continue;
            }

            if (!in_array($role, ['dosen', 'mahasiswa'])) {
                $this->skippedCount++;
                $this->skippedDetails[] = [
                    'row' => $rowNum,
                    'identifier' => $identifier,
                    'reason' => "Peran '$role' tidak valid (harus 'dosen' atau 'mahasiswa')"
                ];
                continue;
            }

            // Check if user already exists
            $existsEmail = User::where('email', $email)->exists();
            $existsId = User::where('identifier', $identifier)->exists();
            
            if ($existsEmail || $existsId) {
                $this->skippedCount++;
                $reason = $existsEmail && $existsId 
                    ? 'Email dan NPM/NIDN sudah terdaftar' 
                    : ($existsEmail ? 'Email sudah terdaftar' : 'NPM/NIDN sudah terdaftar');

                $this->skippedDetails[] = [
                    'row' => $rowNum,
                    'identifier' => $identifier,
                    'reason' => $reason
                ];
                continue;
            }

            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($identifier), // Default password is the NPM/NIDN
                'role' => $role,
                'identifier' => $identifier,
                'entry_year' => $role === 'mahasiswa' ? $entryYear : null,
                'phone_number' => $phoneNumber,
                'is_active' => $isActive,
            ]);

            $this->importedCount++;
        }
    }
}

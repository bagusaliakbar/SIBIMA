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

    public function collection(Collection $rows)
    {
        $header = true;
        foreach ($rows as $row) {
            if ($header) {
                $header = false;
                continue; // Skip the header row
            }

            // Ensure row has at least 4 columns
            if (!isset($row[0]) || !isset($row[1]) || !isset($row[2]) || !isset($row[3])) {
                $this->skippedCount++;
                continue;
            }

            $name = trim($row[0]);
            $email = trim($row[1]);
            $role = strtolower(trim($row[2]));
            $identifier = trim($row[3]);

            if (!in_array($role, ['dosen', 'mahasiswa']) || empty($name) || empty($email) || empty($identifier)) {
                $this->skippedCount++;
                continue;
            }

            // Check if user already exists
            $exists = User::where('email', $email)->orWhere('identifier', $identifier)->exists();
            
            if ($exists) {
                $this->skippedCount++;
                continue;
            }

            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($identifier), // Default password is the NPM/NIDN
                'role' => $role,
                'identifier' => $identifier,
            ]);

            $this->importedCount++;
        }
    }
}

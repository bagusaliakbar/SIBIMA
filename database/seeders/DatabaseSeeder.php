<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Kaprodi Admin',
            'email' => 'admin@sibima.com',
            'role' => 'admin',
            'identifier' => '0000000000',
        ]);

        User::factory()->create([
            'name' => 'Dosen Pembimbing',
            'email' => 'dosen@sibima.com',
            'role' => 'dosen',
            'identifier' => '1234567890',
        ]);

        User::factory()->create([
            'name' => 'Mahasiswa Skripsi',
            'email' => 'mahasiswa@sibima.com',
            'role' => 'mahasiswa',
            'identifier' => '2000000000',
        ]);
    }
}

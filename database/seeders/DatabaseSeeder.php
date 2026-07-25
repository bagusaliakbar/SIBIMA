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
            'name' => 'Admin SIBIMA',
            'email' => 'admin@sibima.com',
            'username' => 'admin',
            'role' => 'admin',
            'identifier' => '0000000000',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        User::factory()->create([
            'name' => 'Ketua Program Studi',
            'email' => 'kaprodi@sibima.com',
            'username' => 'kaprodi',
            'role' => 'kaprodi',
            'identifier' => '0000000001',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        User::factory()->create([
            'name' => 'Dosen Pembimbing',
            'email' => 'dosen@sibima.com',
            'username' => '1234567890',
            'role' => 'dosen',
            'identifier' => '1234567890',
            'password' => \Illuminate\Support\Facades\Hash::make('1234567890'),
        ]);

        User::factory()->create([
            'name' => 'Mahasiswa Skripsi',
            'email' => 'mahasiswa@sibima.com',
            'username' => '2000000000',
            'role' => 'mahasiswa',
            'identifier' => '2000000000',
            'password' => \Illuminate\Support\Facades\Hash::make('2000000000'),
        ]);
    }
}

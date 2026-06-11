<?php

namespace Database\Seeders;

use App\Models\LetterSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LetterSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LetterSetting::create([
            'type' => 'sk_penguji_seminar',
            'title' => 'SK Tim Penguji Seminar',
            'format' => '[NUMBER]/SK/UNSUB/FIK/[MONTH]/[YEAR]',
            'last_number' => 0,
        ]);

        LetterSetting::create([
            'type' => 'sk_penguji_sidang',
            'title' => 'SK Tim Penguji Sidang',
            'format' => '[NUMBER]/SK/UNSUB/FIK/[MONTH]/[YEAR]',
            'last_number' => 0,
        ]);
    }
}

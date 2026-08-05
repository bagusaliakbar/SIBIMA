<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 1. Pengingat H-1 & H-3 Seminar UP / Sidang Akhir (Setiap hari jam 07:00 WIB)
Schedule::command('app:send-schedule-reminders')->dailyAt('07:00');

// 2. Pengingat H-1 Jadwal Bimbingan Skripsi (Setiap hari jam 07:15 WIB)
Schedule::command('app:send-mentoring-reminders')->dailyAt('07:15');

// 3. Pengingat Mahasiswa Semester Kritis (Semester 13-14+) & Laporan Kaprodi (Setiap tanggal 1 jam 08:00 WIB)
Schedule::command('app:send-critical-student-reminders')->monthlyOn(1, '08:00');

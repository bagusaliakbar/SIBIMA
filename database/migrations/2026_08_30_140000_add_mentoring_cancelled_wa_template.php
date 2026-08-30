<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\WaTemplate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        WaTemplate::updateOrCreate(
            ['code' => 'mentoring_cancelled'],
            [
                'name' => 'Pembatalan Jadwal Bimbingan (ke Mahasiswa / Dosen)',
                'category' => 'Bimbingan',
                'content' => "🚫 *PEMBATALAN JADWAL BIMBINGAN SKRIPSI*\n\nHalo *{nama_penerima}*,\n\nSesi bimbingan skripsi berikut telah *DIBATALKAN* oleh {role_pembatal} *{nama_pembatal}*:\n\n📝 *Topik*: {topik_bimbingan}\n📅 *Jadwal Awal*: {tanggal_bimbingan} WIB\n💬 *Alasan Pembatalan*: {alasan_pembatalan}\n\nSilakan ajukan atau jadwalkan kembali sesi bimbingan berikutnya melalui sistem SIBIMA:\n{link_mentoring}\n\nTerima kasih.\n_Sistem Informasi Bimbingan Skripsi (SIBIMA)_",
                'available_variables' => [
                    'nama_penerima'     => 'Nama Penerima Notifikasi',
                    'nama_pembatal'     => 'Nama Pihak yang Membatalkan',
                    'role_pembatal'     => 'Peran (Dosen Pembimbing / Mahasiswa)',
                    'tanggal_bimbingan' => 'Jadwal Bimbingan Awal',
                    'topik_bimbingan'   => 'Topik Bimbingan',
                    'alasan_pembatalan' => 'Alasan Pembatalan',
                    'link_mentoring'    => 'Tautan Halaman Bimbingan',
                ],
                'is_customized' => false,
                'is_active' => true,
            ]
        );

        WaTemplate::clearCache('mentoring_cancelled');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        WaTemplate::where('code', 'mentoring_cancelled')->delete();
        WaTemplate::clearCache('mentoring_cancelled');
    }
};

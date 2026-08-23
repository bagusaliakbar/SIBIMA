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
            ['code' => 'mentoring_rescheduled'],
            [
                'name' => 'Perubahan Jadwal Bimbingan / Reschedule (ke Mahasiswa)',
                'category' => 'Bimbingan',
                'content' => "🔔 *PERUBAHAN JADWAL BIMBINGAN (RESCHEDULE)*\n\nHalo *{nama_mahasiswa}*,\n\nJadwal bimbingan skripsi Anda telah diubah / dijadwalkan ulang oleh Dosen Pembimbing *{nama_dosen}*:\n\n📝 *Topik*: {topik_bimbingan}\n📅 *Waktu Baru*: {tanggal_bimbingan} WIB\n📍 *Jenis/Lokasi*: {jenis_bimbingan}\n\n⚠️ *Penting*: Silakan buka sistem SIBIMA untuk melakukan *Konfirmasi Ulang Kehadiran* Anda:\n{link_mentoring}\n\nTerima kasih.\n_Sistem Informasi Bimbingan Skripsi (SIBIMA)_",
                'available_variables' => [
                    'nama_mahasiswa' => 'Nama Mahasiswa',
                    'nama_dosen' => 'Nama Dosen Pembimbing',
                    'topik_bimbingan' => 'Topik Bimbingan',
                    'tanggal_bimbingan' => 'Waktu & Tanggal Bimbingan Baru',
                    'jenis_bimbingan' => 'Jenis Bimbingan (Online / Offline) & Lokasi Baru',
                    'link_mentoring' => 'Tautan ke Halaman Bimbingan',
                ],
                'is_customized' => false,
            ]
        );

        WaTemplate::clearCache('mentoring_rescheduled');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        WaTemplate::where('code', 'mentoring_rescheduled')->delete();
        WaTemplate::clearCache('mentoring_rescheduled');
    }
};

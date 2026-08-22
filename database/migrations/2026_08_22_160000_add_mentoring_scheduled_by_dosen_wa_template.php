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
            ['code' => 'mentoring_scheduled_by_dosen'],
            [
                'name' => 'Jadwal Bimbingan Baru dari Dosen (ke Mahasiswa)',
                'category' => 'Bimbingan',
                'content' => "🔔 *JADWAL BIMBINGAN SKRIPSI BARU*\n\nHalo *{nama_mahasiswa}*,\n\nDosen Pembimbing Anda, *{nama_dosen}*, telah membuat jadwal bimbingan skripsi baru:\n\n📝 *Topik*: {topik_bimbingan}\n📅 *Waktu*: {tanggal_bimbingan} WIB\n📍 *Jenis/Lokasi*: {jenis_bimbingan}\n\n⚠️ *Penting*: Silakan buka sistem SIBIMA untuk melakukan *Konfirmasi Kehadiran* (Akan Hadir / Izin):\n{link_mentoring}\n\nTerima kasih.\n_Sistem Informasi Bimbingan Skripsi (SIBIMA)_",
                'available_variables' => [
                    'nama_mahasiswa' => 'Nama Mahasiswa',
                    'nama_dosen' => 'Nama Dosen Pembimbing',
                    'topik_bimbingan' => 'Topik Bimbingan',
                    'tanggal_bimbingan' => 'Waktu & Tanggal Bimbingan',
                    'jenis_bimbingan' => 'Jenis Bimbingan (Online / Offline) & Lokasi',
                    'link_mentoring' => 'Tautan ke Halaman Bimbingan',
                ],
                'is_customized' => false,
            ]
        );

        WaTemplate::clearCache('mentoring_scheduled_by_dosen');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        WaTemplate::where('code', 'mentoring_scheduled_by_dosen')->delete();
        WaTemplate::clearCache('mentoring_scheduled_by_dosen');
    }
};

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
        // 1. Update mentoring_scheduled_by_dosen
        $tpl1 = WaTemplate::where('code', 'mentoring_scheduled_by_dosen')->first();
        if ($tpl1) {
            $vars = $tpl1->available_variables ?? [];
            $vars['catatan_bimbingan'] = 'Blok Catatan Dosen (Otomatis jika ada catatan)';
            $vars['catatan'] = 'Teks Catatan Dosen saja';

            $tpl1->available_variables = $vars;
            if (!$tpl1->is_customized) {
                $tpl1->content = "🔔 *JADWAL BIMBINGAN SKRIPSI BARU*\n\nHalo *{nama_mahasiswa}*,\n\nDosen Pembimbing Anda, *{nama_dosen}*, telah membuat jadwal bimbingan skripsi baru:\n\n📝 *Topik*: {topik_bimbingan}\n📅 *Waktu*: {tanggal_bimbingan} WIB\n📍 *Jenis/Lokasi*: {jenis_bimbingan}{catatan_bimbingan}\n\n⚠️ *Penting*: Silakan buka sistem SIBIMA untuk melakukan *Konfirmasi Kehadiran* (Akan Hadir / Izin):\n{link_mentoring}\n\nTerima kasih.\n_Sistem Informasi Bimbingan Skripsi (SIBIMA)_";
            }
            $tpl1->save();
            WaTemplate::clearCache('mentoring_scheduled_by_dosen');
        }

        // 2. Update mentoring_rescheduled
        $tpl2 = WaTemplate::where('code', 'mentoring_rescheduled')->first();
        if ($tpl2) {
            $vars = $tpl2->available_variables ?? [];
            $vars['catatan_bimbingan'] = 'Blok Catatan Dosen (Otomatis jika ada catatan)';
            $vars['catatan'] = 'Teks Catatan Dosen saja';

            $tpl2->available_variables = $vars;
            if (!$tpl2->is_customized) {
                $tpl2->content = "🔔 *PERUBAHAN JADWAL BIMBINGAN (RESCHEDULE)*\n\nHalo *{nama_mahasiswa}*,\n\nJadwal bimbingan skripsi Anda telah diubah / dijadwalkan ulang oleh Dosen Pembimbing *{nama_dosen}*:\n\n📝 *Topik*: {topik_bimbingan}\n📅 *Waktu Baru*: {tanggal_bimbingan} WIB\n📍 *Jenis/Lokasi*: {jenis_bimbingan}{catatan_bimbingan}\n\n⚠️ *Penting*: Silakan buka sistem SIBIMA untuk melakukan *Konfirmasi Ulang Kehadiran* Anda:\n{link_mentoring}\n\nTerima kasih.\n_Sistem Informasi Bimbingan Skripsi (SIBIMA)_";
            }
            $tpl2->save();
            WaTemplate::clearCache('mentoring_rescheduled');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        WaTemplate::clearCache('mentoring_scheduled_by_dosen');
        WaTemplate::clearCache('mentoring_rescheduled');
    }
};

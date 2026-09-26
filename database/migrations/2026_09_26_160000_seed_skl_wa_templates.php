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
            ['code' => 'skl_published'],
            [
                'name' => 'Penerbitan SKL Digital (ke Mahasiswa)',
                'category' => 'Yudisium',
                'content' => "🎓 *SURAT KETERANGAN LULUS (SKL) DITERBITKAN*\n\nHalo *{nama_mahasiswa}*,\n\nSelamat! Seluruh berkas bebas tanggungan pra-yudisium Anda telah diverifikasi dan disetujui oleh Program Studi.\n\n📄 *Nomor SKL:* {nomor_skl}\n📅 *Tanggal Kelulusan:* {tanggal_kelulusan}\n⭐ *Predikat:* {predikat_kelulusan}\n🎯 *IPK:* {ipk}\n\nSilakan login ke portal SIBIMA untuk mengunduh dokumen SKL digital resmi Anda:\n{link_skl}\n\n_Sistem Informasi Bimbingan Mahasiswa (SIBIMA) - FASILKOM UNSUB_",
                'available_variables' => [
                    'nama_mahasiswa'    => 'Nama Lengkap Mahasiswa',
                    'nomor_skl'         => 'Nomor Surat Keterangan Lulus',
                    'tanggal_kelulusan' => 'Tanggal Kelulusan / Yudisium',
                    'predikat_kelulusan'=> 'Predikat Kelulusan',
                    'ipk'               => 'Indeks Prestasi Kumulatif (IPK)',
                    'link_skl'          => 'Tautan Halaman Pra-Yudisium & Unduh SKL',
                ],
                'is_customized' => false,
                'is_active' => true,
            ]
        );

        WaTemplate::updateOrCreate(
            ['code' => 'graduation_rejected'],
            [
                'name' => 'Perbaikan Berkas Bebas Tanggungan (ke Mahasiswa)',
                'category' => 'Yudisium',
                'content' => "⚠️ *PERBAIKAN BERKAS BEBAS TANGGUNGAN*\n\nHalo *{nama_mahasiswa}*,\n\nPengajuan berkas bebas tanggungan pra-yudisium Anda memerlukan perbaikan dari Program Studi / Fakultas.\n\n📝 *Catatan / Alasan Perbaikan:*\n\"{alasan_penolakan}\"\n\nSilakan periksa dan perbaiki berkas persyaratan Anda melalui portal SIBIMA:\n{link_skl}\n\nTerima kasih.\n_Sistem Informasi Bimbingan Mahasiswa (SIBIMA) - FASILKOM UNSUB_",
                'available_variables' => [
                    'nama_mahasiswa'    => 'Nama Lengkap Mahasiswa',
                    'alasan_penolakan'  => 'Catatan / Alasan Perbaikan dari Petugas',
                    'link_skl'          => 'Tautan Halaman Pra-Yudisium',
                ],
                'is_customized' => false,
                'is_active' => true,
            ]
        );

        WaTemplate::clearCache();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        WaTemplate::whereIn('code', ['skl_published', 'graduation_rejected'])->delete();
        WaTemplate::clearCache();
    }
};

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
            ['code' => 'dosen_penguji_belum_revisi'],
            [
                'name' => 'Pengingat Input Catatan Revisi Penguji Seminar & Sidang (ke Dosen)',
                'category' => 'Pengingat',
                'content' => "🔔 *PENGINGAT INPUT CATATAN REVISI UJIAN*\n\nYth. Bpk/Ibu *{nama}*,\n\nBerdasarkan pantauan sistem SIBIMA, tercatat terdapat *{jumlah_mahasiswa} mahasiswa* ujian yang telah selesai melaksanakan {kategori_ujian}, namun *belum menerima butir catatan revisi* dari Bpk/Ibu selaku Dosen Penguji:\n\n{daftar_mahasiswa}\n\nMohon kesediaan Bpk/Ibu untuk menginputkan catatan/arahan revisi melalui portal SIBIMA agar mahasiswa dapat segera menindaklanjuti perbaikan naskah:\n{link_tugas}\n\nTerima kasih atas dedikasi dan kerja sama Bpk/Ibu.\n_Program Studi FASILKOM UNSUB_",
                'available_variables' => [
                    'nama' => 'Nama Dosen Penguji',
                    'nidn' => 'NIDN Dosen',
                    'jumlah_mahasiswa' => 'Jumlah Mahasiswa Belum Direvisi',
                    'daftar_mahasiswa' => 'Daftar Nama, NPM & Jenis Ujian Mahasiswa',
                    'mahasiswa_ringkas' => 'Ringkasan Nama Mahasiswa',
                    'kategori_ujian' => 'Kategori Ujian (Seminar / Sidang / Keduanya)',
                    'link_tugas' => 'Tautan Tugas Penguji di SIBIMA',
                    'link_seminar' => 'Tautan Tugas Penguji Seminar',
                    'link_sidang' => 'Tautan Tugas Penguji Sidang',
                    'link_dashboard' => 'Tautan Dashboard SIBIMA',
                    'link_login' => 'Tautan Login SIBIMA',
                ],
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
        WaTemplate::where('code', 'dosen_penguji_belum_revisi')->delete();
        WaTemplate::clearCache();
    }
};

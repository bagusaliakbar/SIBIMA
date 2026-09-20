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
            ['code' => 'birthday_student'],
            [
                'name' => 'Ucapan Selamat Ulang Tahun (ke Mahasiswa Skripsi)',
                'category' => 'Ulang Tahun',
                'content' => "🎂 *SELAMAT ULANG TAHUN, {nama_mahasiswa}!* 🎉\n\nKeluarga Besar Fakultas Ilmu Komputer (FASILKOM) UNSUB & Tim Dosen Pembimbing mengucapkan selamat bertambah usia untukmu di hari yang spesial ini! ✨\n\nSemoga senantiasa diberikan kesehatan, keberkahan, serta kelancaran dan kemudahan dalam menyelesaikan perjalanan skripsimu:\n📖 *Judul*: {judul_skripsi}\n👨‍🏫 *Pembimbing*: {nama_pembimbing1}\n\nTetap semangat dan pantang menyerah, selangkah lagi menuju toga wisuda! Kami tunggu prestasimu di ruang sidang skripsi! 🎓🚀\n\n_Salam hangat & doa terbaik,_\n*SIBIMA FASILKOM UNSUB*",
                'available_variables' => [
                    'nama_mahasiswa'    => 'Nama Lengkap Mahasiswa',
                    'umur'              => 'Usia / Umur Mahasiswa',
                    'judul_skripsi'     => 'Judul Skripsi Mahasiswa',
                    'nama_pembimbing1'  => 'Nama Dosen Pembimbing 1',
                    'hari_ini'          => 'Tanggal Hari Ini',
                ],
                'is_customized' => false,
                'is_active' => true,
            ]
        );

        WaTemplate::updateOrCreate(
            ['code' => 'birthday_lecturer'],
            [
                'name' => 'Ucapan Selamat Ulang Tahun (ke Dosen & Kaprodi)',
                'category' => 'Ulang Tahun',
                'content' => "🎂 *SELAMAT BERTAMBAH USIA, BPK/IBU {nama_dosen}!* 💐\n\nKeluarga Besar Civitas Akademika Fakultas Ilmu Komputer (FASILKOM) Universitas Subang mengucapkan selamat ulang tahun yang penuh berkah. ✨\n\nSemoga Allah SWT senantiasa melimpahkan kesehatan yang paripurna, kebahagiaan bersama keluarga tercinta, serta kelancaran dan kemudahan dalam mengemban amanah Tridharma Perguruan Tinggi.\n\nTerima kasih yang sebesar-besarnya atas bimbingan, dedikasi, dan ketulusan Bpk/Ibu dalam mendidik serta mengantarkan mahasiswa menuju masa depan yang gemilang. 👨‍🏫🎓\n\n_Salam hormat & takzim,_\n*SIBIMA FASILKOM UNSUB*",
                'available_variables' => [
                    'nama_dosen' => 'Nama Dosen / Kaprodi',
                    'umur'       => 'Usia / Umur',
                    'hari_ini'   => 'Tanggal Hari Ini',
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
        WaTemplate::whereIn('code', ['birthday_student', 'birthday_lecturer'])->delete();
        WaTemplate::clearCache();
    }
};

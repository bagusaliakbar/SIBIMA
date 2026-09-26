<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            // Bimbingan & Logbook
            [
                'category' => 'bimbingan',
                'question' => 'Berapa kali jumlah minimal bimbingan sebelum dapat mengajukan Seminar Proposal (UP) dan Sidang?',
                'answer' => 'Sesuai ketentuan akademik SIBIMA, mahasiswa wajib melaksanakan minimal **8 sesi bimbingan yang telah disetujui (status selesai)** dengan kedua Dosen Pembimbing (Pembimbing 1 dan Pembimbing 2) sebelum dapat mendaftar Seminar Proposal maupun Sidang Akhir Skripsi.',
                'target_role' => 'all',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'category' => 'bimbingan',
                'question' => 'Bagaimana alur pengajuan jadwal bimbingan dengan dosen pembimbing?',
                'answer' => "Mahasiswa dapat mengajukan jadwal bimbingan melalui menu **Jadwal Bimbingan > Buat Pengajuan**. Pilih dosen pembimbing, tentukan waktu bimbingan, jenis pertemuan (Online/Offline), lokasi/link pertemuan, serta topik pembahasan.\n\nDosen pembimbing akan menerima notifikasi dan dapat menyetujui, menolak, atau menjadwalkan ulang pertemuan tersebut.",
                'target_role' => 'mahasiswa',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'category' => 'bimbingan',
                'question' => 'Bagaimana cara dosen memberikan persetujuan (ACC) Seminar atau Sidang?',
                'answer' => "Dosen Pembimbing dapat memberikan ACC langsung melalui dua cara:\n1. Melalui halaman **Monitoring Bimbingan Mahasiswa** (`Logbook`): Di bagian header atas kartu mahasiswa, klik tombol **ACC SEMINAR** atau **ACC SIDANG**.\n2. Melalui halaman **Daftar Mahasiswa Bimbingan**: Pada kolom aksi, klik tombol ACC yang tersedia.\n\nStatus dot P1 dan P2 akan otomatis menyala hijau jika persetujuan telah tersimpan.",
                'target_role' => 'dosen',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'category' => 'bimbingan',
                'question' => 'Bagaimana cara mencatat logbook setelah sesi bimbingan berlangsung?',
                'answer' => "Setelah sesi bimbingan terlaksana, mahasiswa membuka detail sesi yang disetujui lalu mengisi catatan perkembangan skripsi, melampirkan berkas draf skripsi terbaru, dan menandai kehadiran. Dosen kemudian akan mengonfirmasi sesi menjadi **Selesai**, memberikan catatan umpan balik (feedback), serta dapat mengunggah berkas koreksi.",
                'target_role' => 'all',
                'order' => 4,
                'is_active' => true,
            ],

            // Seminar Proposal
            [
                'category' => 'seminar',
                'question' => 'Bagaimana jika salah satu Dosen Pembimbing belum memberikan ACC Seminar?',
                'answer' => 'Untuk mendaftar Seminar Proposal, sistem mensyaratkan persetujuan lengkap dari **KEDUA Dosen Pembimbing (P1 & P2)**. Jika salah satu pembimbing belum memberikan ACC, silakan hubungi dosen bersangkutan melalui fitur chat WhatsApp yang tersedia di sistem untuk menanyakan revisi atau persyaratan yang masih harus dipenuhi.',
                'target_role' => 'mahasiswa',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'category' => 'seminar',
                'question' => 'Apa saja syarat dan berkas yang wajib diunggah saat pendaftaran Seminar Proposal?',
                'answer' => "Berkas yang wajib dipersiapkan antara lain:\n- Naskah Draf Proposal Skripsi (Bab 1 s.d. Bab 3) format PDF\n- Status ACC Seminar dari Pembimbing 1 dan Pembimbing 2\n- Riwayat Logbook bimbingan minimal 8 sesi selesai\n- Formulir pendaftaran seminar yang telah terisi lengkap",
                'target_role' => 'mahasiswa',
                'order' => 6,
                'is_active' => true,
            ],

            // Sidang Akhir
            [
                'category' => 'sidang',
                'question' => 'Kapan mahasiswa berhak mendaftar Sidang Akhir Skripsi?',
                'answer' => "Mahasiswa berhak mendaftar Sidang Akhir Skripsi apabila:\n1. Telah dinyatakan lulus Seminar Proposal dan menyelesaikan seluruh revisinya.\n2. Telah menuntaskan penulisan naskah skripsi lengkap (Bab 1 s.d. Bab 5/6).\n3. Telah menyelesaikan sesi bimbingan lanjutan dengan kedua pembimbing.\n4. Telah memperoleh status **ACC SIDANG** dari Pembimbing 1 dan Pembimbing 2.",
                'target_role' => 'all',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'category' => 'sidang',
                'question' => 'Bagaimana proses penilaian ujian Sidang Skripsi?',
                'answer' => 'Penilaian dilakukan secara elektronik di SIBIMA oleh Tim Penguji (Ketua Penguji, Penguji Anggota, dan Dosen Pembimbing). Komponen nilai meliputi penilaian naskah, presentasi, penguasaan materi, serta demonstrasi aplikasi/sistem. Nilai akhir dan berita acara akan diterbitkan setelah ujian selesai.',
                'target_role' => 'all',
                'order' => 8,
                'is_active' => true,
            ],

            // Revisi & Berkas
            [
                'category' => 'revisi',
                'question' => 'Berapa batas waktu maksimal penyelesaian revisi setelah Sidang Skripsi?',
                'answer' => 'Batas waktu revisi sidang skripsi adalah **14 hari kalender** sejak tanggal sidang dilaksanakan (atau mengikuti jadwal gelombang berjalan). Mahasiswa wajib mengunggah naskah hasil revisi dan lembar catatan revisi melalui menu **Revisi Sidang** untuk disetujui oleh masing-masing dosen penguji.',
                'target_role' => 'mahasiswa',
                'order' => 9,
                'is_active' => true,
            ],
            [
                'category' => 'revisi',
                'question' => 'Bagaimana cara dosen penguji menyetujui revisi skripsi mahasiswa?',
                'answer' => 'Dosen penguji dapat membuka menu **Penugasan Penguji > Penguji Sidang**, pilih mahasiswa yang bersangkutan, tinjau naskah revisi yang diunggah, lalu klik tombol **Setujui Revisi**. Jika seluruh penguji telah menyetujui, mahasiswa dinyatakan telah menyelesaikan tahap revisi skripsi.',
                'target_role' => 'dosen',
                'order' => 10,
                'is_active' => true,
            ],

            // Akun & Teknis
            [
                'category' => 'teknis',
                'question' => 'Bagaimana jika mengalami kendala login atau gagal verifikasi CAPTCHA Cloudflare?',
                'answer' => "Pastikan koneksi internet stabil dan waktu sistem/jam perangkat Anda akurat. Jika Cloudflare Turnstile tidak muncul atau gagal:\n1. Matikan adblocker atau ekstensi VPN untuk sementara waktu.\n2. Tekan **Ctrl + F5** untuk hard reload browser.\n3. Coba akses menggunakan browser lain atau jendela Incognito (Penyamaran).\n4. Jika masalah berlanjut, hubungi Admin Program Studi.",
                'target_role' => 'all',
                'order' => 11,
                'is_active' => true,
            ],
            [
                'category' => 'teknis',
                'question' => 'Bagaimana cara memperbarui nomor WhatsApp agar notifikasi sistem dapat masuk?',
                'answer' => 'Klik nama akun Anda di pojok kanan atas, pilih **Profil**, lalu perbarui kolom **Nomor WhatsApp** dengan format aktif yang dapat dihubungi (contoh: 08123456789). Klik **Simpan** untuk memperbarui data.',
                'target_role' => 'all',
                'order' => 12,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['question' => $faq['question']],
                $faq
            );
        }
    }
}

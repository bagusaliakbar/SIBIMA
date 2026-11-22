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
            // =================================================================
            // 1. BIMBINGAN & LOGBOOK (8 FAQ)
            // =================================================================
            [
                'category' => 'bimbingan',
                'question' => 'Berapa kali jumlah minimal bimbingan sebelum dapat mengajukan Seminar Proposal (UP) dan Sidang?',
                'answer' => "Sesuai ketentuan akademik Program Studi di SIBIMA, syarat minimal sesi bimbingan yang telah disetujui dan berstatus **Selesai** adalah:\n1. **Seminar Proposal (UP)**: Minimal **4 sesi bimbingan selesai** untuk masing-masing Dosen Pembimbing (4x Pembimbing 1 dan 4x Pembimbing 2).\n2. **Sidang Akhir Skripsi**: Minimal **8 sesi bimbingan selesai** untuk masing-masing Dosen Pembimbing (8x Pembimbing 1 dan 8x Pembimbing 2).\n\nProgres bimbingan dapat dipantau secara langsung pada kartu **Progress Bimbingan** di halaman Jadwal Bimbingan mahasiswa.",
                'target_role' => 'all',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'category' => 'bimbingan',
                'question' => 'Bagaimana alur pengajuan jadwal bimbingan dengan dosen pembimbing?',
                'answer' => "Mahasiswa dapat mengajukan jadwal bimbingan melalui menu **Jadwal Bimbingan > Buat Pengajuan**.\n1. Pilih **Dosen Pembimbing** (Pembimbing 1 atau Pembimbing 2).\n2. Pilih **Tanggal**, **Jam Mulai**, dan **Durasi / Jam Selesai** (rentang waktu bimbingan, misal `09:00 - 11:00 WIB`).\n3. Pilih metode pertemuan (**Tatap Muka / Offline** atau **Daring / Online**), isi lokasi/tautan Google Meet, serta topik pembahasan.\n\nDosen pembimbing akan menerima notifikasi WhatsApp & sistem untuk menyetujui, menolak, atau menjadwalkan ulang pertemuan tersebut.",
                'target_role' => 'mahasiswa',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'category' => 'bimbingan',
                'question' => 'Bagaimana cara mahasiswa melakukan Konfirmasi Kehadiran dan memilih jam kedatangan?',
                'answer' => "Ketika dosen menjadwalkan sesi bimbingan dengan rentang waktu (misal `09:00 - 11:00 WIB`), mahasiswa wajib melakukan konfirmasi melalui menu **Jadwal Bimbingan**:\n1. Klik tombol **Akan Hadir** untuk membuka pilihan estimasi jam kedatangan (tersedia slot cepat per **30 menit** maupun **input jam manual** sesuai rentang yang disediakan dosen).\n2. Pada bimbingan bersama, jumlah mahasiswa lain yang sudah memilih slot jam yang sama juga ditampilkan agar kedatangan lebih merata.\n3. Jika berhalangan hadir, klik tombol **Izin** dan tuliskan alasan yang jelas agar tersampaikan ke dosen pembimbing.\n\nJam kedatangan masih dapat diubah sebelum sesi dimulai melalui tautan **Ubah Jam Hadir**.",
                'target_role' => 'mahasiswa',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'category' => 'bimbingan',
                'question' => 'Bagaimana cara dosen menjadwalkan ulang (reschedule) dan menambahkan mahasiswa yang terlewat?',
                'answer' => "Pada halaman **Jadwal Bimbingan**, klik tombol **Edit / Reschedule** pada sesi yang ingin diubah:\n1. Ubah tanggal, jam mulai, durasi/jam selesai, topik, metode, atau lokasi/tautan pertemuan.\n2. Jika ada mahasiswa bimbingan yang terlewat, klik **Tambah Mahasiswa Terlewat** — mahasiswa terpilih akan masuk ke daftar tunggu terlebih dahulu.\n3. Klik **Simpan Perubahan Jadwal** di bagian bawah. Sistem akan menyimpan seluruh perubahan secara bersamaan dan mengirimkan notifikasi WhatsApp & Web secara akurat (hanya 1x notifikasi dengan jadwal terbaru).\n\nJika waktu diubah, status konfirmasi kehadiran dan jam kedatangan mahasiswa otomatis di-reset agar mahasiswa melakukan konfirmasi ulang.",
                'target_role' => 'dosen',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'category' => 'bimbingan',
                'question' => 'Bagaimana cara dosen memberikan persetujuan (ACC) Seminar atau Sidang?',
                'answer' => "Dosen Pembimbing dapat memberikan ACC langsung melalui dua cara:\n1. Melalui halaman **Mahasiswa Bimbingan & Logbook**: Di bagian header kartu/detail mahasiswa, klik tombol **ACC SEMINAR** atau **ACC SIDANG**.\n2. Melalui halaman **Daftar Mahasiswa Bimbingan**: Pada kolom aksi, klik tombol ACC yang tersedia.\n\nIndikator dot P1 dan P2 akan otomatis menyala hijau jika persetujuan telah tersimpan, dan mahasiswa langsung menerima notifikasi WhatsApp & sistem.",
                'target_role' => 'dosen',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'category' => 'bimbingan',
                'question' => 'Bagaimana cara mencatat logbook, melampirkan draf skripsi, dan melihat feedback dosen?',
                'answer' => "Sebelum atau saat sesi bimbingan berlangsung, mahasiswa dapat menekan tombol **Lampirkan** pada baris sesi untuk menempelkan tautan berkas draf skripsi (misalnya link Google Drive yang sudah dibuka aksesnya).\n\nSetelah sesi selesai, dosen menandai sesi menjadi **Selesai**, menuliskan catatan arahan/feedback, serta dapat melampirkan **Dokumen Feedback / Koreksi Dosen**. Seluruh riwayat bimbingan otomatis terekam dalam **Logbook Bimbingan** dan dapat dicetak dalam format PDF.",
                'target_role' => 'all',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'category' => 'bimbingan',
                'question' => 'Bagaimana cara melihat dan mencetak Kartu Bimbingan Digital?',
                'answer' => "Mahasiswa dapat mengakses **Kartu Bimbingan Digital** melalui menu **Profil** (klik nama akun di pojok kanan atas).\n- Kartu menampilkan identitas mahasiswa, judul skripsi, nama Pembimbing 1 & 2, serta **Kartu Kendali 8 Sesi Bimbingan** yang terisi otomatis dari logbook yang telah diselesaikan.\n- Dilengkapi **QR Code Verifikasi** resmi yang dapat dipindai untuk memastikan keaslian progres bimbingan.\n- Klik tombol cetak/unduh pada kartu jika perlu dilampirkan sebagai syarat administrasi.",
                'target_role' => 'mahasiswa',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'category' => 'bimbingan',
                'question' => 'Bagaimana cara dosen memantau kehadiran mahasiswa secara langsung (Live Attendance)?',
                'answer' => "Pada halaman **Jadwal Bimbingan**, dosen dapat menekan tombol **Live Attendance** untuk memantau status konfirmasi seluruh mahasiswa (*Akan Hadir*, *Izin*, atau *Belum Respon*) secara real-time.\n- Setiap mahasiswa yang memilih **Akan Hadir** akan menampilkan estimasi jam kedatangannya (contoh: `Akan Hadir • 09:30 WIB`).\n- Tersedia tombol cepat **Chat WA** untuk mengingatkan mahasiswa yang belum merespons, serta mode tampilan **Per Sesi** dan **Semua Mahasiswa**.",
                'target_role' => 'dosen',
                'order' => 8,
                'is_active' => true,
            ],

            // =================================================================
            // 2. SEMINAR PROPOSAL / UP (4 FAQ)
            // =================================================================
            [
                'category' => 'seminar',
                'question' => 'Bagaimana jika salah satu Dosen Pembimbing belum memberikan ACC Seminar?',
                'answer' => 'Untuk mendaftar Seminar Proposal (UP), sistem mensyaratkan persetujuan lengkap dari **kedua Dosen Pembimbing (Pembimbing 1 & Pembimbing 2)**. Jika salah satu pembimbing belum memberikan ACC, silakan hubungi dosen bersangkutan melalui fitur **Live Chat** atau tautan WhatsApp yang tersedia di sistem untuk menanyakan revisi atau kelengkapan yang masih perlu dipenuhi.',
                'target_role' => 'mahasiswa',
                'order' => 9,
                'is_active' => true,
            ],
            [
                'category' => 'seminar',
                'question' => 'Apa saja syarat dan berkas yang wajib diunggah saat pendaftaran Seminar Proposal?',
                'answer' => "Berkas dan syarat yang wajib dipersiapkan sebelum mendaftar Seminar Proposal (UP) meliputi:\n- Status **ACC Seminar** dari Pembimbing 1 dan Pembimbing 2.\n- Minimal **4 sesi bimbingan selesai** per masing-masing pembimbing.\n- Gelombang pendaftaran Seminar Proposal sedang berstatus **Aktif / Dibuka** oleh Program Studi.\n- Naskah Draf Proposal Skripsi (Bab 1 s.d. Bab 3) dan seluruh berkas persyaratan administrasi (seperti bukti pembayaran, KRS/transkrip, atau berkas lain sesuai form gelombang aktif) dalam format yang ditentukan.",
                'target_role' => 'mahasiswa',
                'order' => 10,
                'is_active' => true,
            ],
            [
                'category' => 'seminar',
                'question' => 'Bagaimana cara mengetahui apakah pendaftaran Seminar Proposal saya sudah divalidasi?',
                'answer' => "Buka menu **Pendaftaran Seminar (UP)** untuk melihat status pendaftaran Anda:\n- **Menunggu Validasi**: Berkas sudah masuk dan sedang diperiksa oleh Admin/Kaprodi.\n- **Disetujui / Tervalidasi**: Berkas lengkap dan Anda akan masuk dalam penjadwalan Seminar Proposal.\n- **Perlu Perbaikan / Ditolak**: Periksa catatan dari Admin/Kaprodi, lalu unggah ulang berkas perbaikan sesuai instruksi.\n\nSetiap perubahan status validasi dan penerbitan jadwal akan dikirimkan pula melalui notifikasi WhatsApp & Web.",
                'target_role' => 'mahasiswa',
                'order' => 11,
                'is_active' => true,
            ],
            [
                'category' => 'seminar',
                'question' => 'Di mana saya dapat melihat jadwal, ruangan, dan tim penguji Seminar Proposal?',
                'answer' => "- **Mahasiswa**: Jadwal Seminar Proposal (tanggal, jam, ruangan/link, serta daftar Ketua & Anggota Penguji) langsung tampil pada **Dashboard Mahasiswa**, menu **Kalender Akademik**, dan halaman **Pendaftaran Seminar** setelah jadwal diterbitkan.\n- **Dosen Penguji**: Jadwal penugasan menguji dapat dilihat pada **Dashboard Dosen** serta menu **Penugasan Penguji > Penguji Seminar**, lengkap dengan akses naskah proposal dan ruang catatan revisi.",
                'target_role' => 'all',
                'order' => 12,
                'is_active' => true,
            ],

            // =================================================================
            // 3. SIDANG AKHIR & NILAI (5 FAQ)
            // =================================================================
            [
                'category' => 'sidang',
                'question' => 'Kapan mahasiswa berhak mendaftar Sidang Akhir Skripsi?',
                'answer' => "Mahasiswa berhak mendaftar Sidang Akhir Skripsi apabila:\n1. Telah melaksanakan Seminar Proposal (UP) dan menyelesaikan seluruh revisinya.\n2. Telah menuntaskan penulisan naskah skripsi lengkap (Bab 1 s.d. Bab 5/6) beserta luaran/aplikasi penelitian.\n3. Telah memenuhi minimal **8 sesi bimbingan selesai** untuk masing-masing pembimbing.\n4. Telah memperoleh status **ACC SIDANG** dari Pembimbing 1 dan Pembimbing 2.\n5. Gelombang pendaftaran Sidang Skripsi sedang dibuka oleh Program Studi.",
                'target_role' => 'all',
                'order' => 13,
                'is_active' => true,
            ],
            [
                'category' => 'sidang',
                'question' => 'Bagaimana proses penilaian ujian Sidang Skripsi?',
                'answer' => 'Penilaian dilakukan secara elektronik di SIBIMA oleh Tim Penguji (Ketua Penguji, Penguji Anggota, dan Dosen Pembimbing). Komponen nilai meliputi penilaian naskah, presentasi, penguasaan materi, serta demonstrasi aplikasi/sistem. Nilai akhir, grade huruf, dan Berita Acara Sidang akan terhitung otomatis dan diterbitkan setelah seluruh penguji mengisi penilaian.',
                'target_role' => 'all',
                'order' => 14,
                'is_active' => true,
            ],
            [
                'category' => 'sidang',
                'question' => 'Bagaimana cara dosen menginput nilai sidang dan mengunduh Berita Acara atau Surat Tugas?',
                'answer' => "Dosen Penguji/Pembimbing dapat membuka menu **Penugasan Penguji > Penguji Sidang**, lalu pilih mahasiswa yang diuji:\n1. Gunakan **Kartu Penilaian Penguji** untuk mengisi skor per komponen penilaian lalu simpan.\n2. Catatan revisi terstruktur dapat diinput langsung pada **Ruang Revisi** di halaman yang sama.\n3. Untuk mengunduh **Surat Tugas Pembimbing/Penguji** atau **Berita Acara**, gunakan tombol ekspor PDF/Word yang tersedia pada halaman jadwal atau detail penugasan.",
                'target_role' => 'dosen',
                'order' => 15,
                'is_active' => true,
            ],
            [
                'category' => 'sidang',
                'question' => 'Bagaimana alur pendaftaran Yudisium, Bebas Tanggungan, dan penerbitan SKL?',
                'answer' => "Setelah dinyatakan lulus Sidang Skripsi dan seluruh revisi disetujui oleh Tim Penguji:\n1. Mahasiswa membuka menu **Yudisium & SKL** untuk mengunggah berkas kelengkapan akhir (naskah final lengkap, bukti bebas pustaka/tanggungan, dan dokumen persyaratan yudisium).\n2. Admin/Kaprodi memverifikasi kelengkapan berkas tersebut.\n3. Setelah diverifikasi dan disetujui, status skripsi berubah menjadi **Lulus / Selesai** dan **Surat Keterangan Lulus (SKL)** dapat diterbitkan serta diverifikasi melalui QR Code.",
                'target_role' => 'all',
                'order' => 16,
                'is_active' => true,
            ],
            [
                'category' => 'sidang',
                'question' => 'Apa yang terjadi pada naskah skripsi setelah mahasiswa dinyatakan lulus?',
                'answer' => 'Setelah mahasiswa dinyatakan lulus/yudisium, data judul, abstrak, dan dokumen naskah skripsi (Bab 1 s.d. Bab 6) secara otomatis terarsipkan ke dalam **Repositori Digital SIBIMA** agar dapat menjadi referensi ilmiah bagi civitas akademika dengan proteksi mode baca PDF Reader.',
                'target_role' => 'all',
                'order' => 17,
                'is_active' => true,
            ],

            // =================================================================
            // 4. REVISI & BERKAS (4 FAQ)
            // =================================================================
            [
                'category' => 'revisi',
                'question' => 'Berapa batas waktu maksimal penyelesaian revisi setelah Seminar dan Sidang Skripsi?',
                'answer' => 'Batas waktu revisi pasca ujian adalah **14 hari kalender** sejak tanggal Seminar Proposal atau Sidang Skripsi dilaksanakan (atau mengikuti batas jadwal gelombang berjalan). Mahasiswa wajib mengirimkan berkas hasil perbaikan melalui **Ruang Revisi** agar segera ditinjau dan disetujui oleh masing-masing dosen penguji.',
                'target_role' => 'mahasiswa',
                'order' => 18,
                'is_active' => true,
            ],
            [
                'category' => 'revisi',
                'question' => 'Bagaimana cara menggunakan Ruang Diskusi Revisi 2 Kolom?',
                'answer' => "Pada halaman **Revisi Seminar** atau **Revisi Sidang**, mahasiswa dan dosen dapat berinteraksi langsung melalui tampilan 2 kolom:\n- **Kolom Kiri**: Daftar poin catatan revisi dari masing-masing penguji serta pratinjau dokumen naskah.\n- **Kolom Kanan**: Percakapan/balasan perbaikan per penguji. Mahasiswa dapat menuliskan penjelasan perbaikan dan melampirkan link dokumen revisi terbaru.\n- Tersedia indikator **status terbaca (centang dua)** sehingga kedua pihak mengetahui apakah pesan revisi sudah dibaca.",
                'target_role' => 'all',
                'order' => 19,
                'is_active' => true,
            ],
            [
                'category' => 'revisi',
                'question' => 'Bagaimana cara dosen penguji memberikan catatan revisi dan menyetujui revisi mahasiswa?',
                'answer' => "Dosen penguji dapat membuka menu **Penugasan Penguji > Penguji Seminar** atau **Penguji Sidang**, lalu pilih mahasiswa yang bersangkutan:\n1. Gunakan **Builder Poin Revisi** untuk menuliskan poin-poin perbaikan secara terstruktur dan kirimkan ke mahasiswa.\n2. Setelah mahasiswa mengunggah balasan/naskah perbaikan, tinjau berkasnya melalui pratinjau langsung.\n3. Jika seluruh poin telah sesuai, klik tombol **Setujui Revisi**. Mahasiswa dinyatakan tuntas revisi apabila seluruh penguji telah memberikan persetujuan.",
                'target_role' => 'dosen',
                'order' => 20,
                'is_active' => true,
            ],
            [
                'category' => 'revisi',
                'question' => 'Bagaimana cara mengunduh Lembar Catatan Revisi (PDF)?',
                'answer' => 'Pada halaman **Revisi Seminar** maupun **Revisi Sidang**, klik tombol **Cetak / Unduh Lembar Revisi (PDF)**. Dokumen PDF secara otomatis memuat seluruh catatan revisi dari penguji, status persetujuan, tanda tangan digital dosen, serta **QR Code Verifikasi** keaslian dokumen.',
                'target_role' => 'all',
                'order' => 21,
                'is_active' => true,
            ],

            // =================================================================
            // 5. AKUN & TEKNIS (6 FAQ)
            // =================================================================
            [
                'category' => 'teknis',
                'question' => 'Bagaimana jika mengalami kendala login atau gagal verifikasi CAPTCHA Cloudflare?',
                'answer' => "Pastikan koneksi internet stabil dan jam/tanggal perangkat Anda sudah sinkron otomatis. Jika Cloudflare Turnstile tidak muncul atau gagal:\n1. Matikan sementara adblocker atau ekstensi VPN pada browser.\n2. Tekan **Ctrl + F5** untuk melakukan *hard reload* halaman.\n3. Coba buka SIBIMA melalui browser lain atau jendela *Incognito* (Mode Penyamaran).\n4. Jika akun belum aktif/pending atau lupa password, hubungi Admin Program Studi.",
                'target_role' => 'all',
                'order' => 22,
                'is_active' => true,
            ],
            [
                'category' => 'teknis',
                'question' => 'Bagaimana cara memperbarui nomor WhatsApp agar notifikasi sistem dapat masuk?',
                'answer' => 'Klik nama akun Anda di pojok kanan atas, pilih **Profil**, lalu perbarui kolom **Nomor WhatsApp** menggunakan nomor WhatsApp aktif (contoh: `081234567890`). Klik **Simpan** agar notifikasi jadwal bimbingan, pengingat H-1, ACC, jadwal ujian, dan catatan revisi dapat masuk ke WhatsApp Anda.',
                'target_role' => 'all',
                'order' => 23,
                'is_active' => true,
            ],
            [
                'category' => 'teknis',
                'question' => 'Mengapa sesi saya keluar otomatis (Auto Logout) atau muncul peringatan batas waktu sesi?',
                'answer' => "Demi keamanan akun akademik, SIBIMA menerapkan batas sesi tidak aktif (**idle timeout**) selama **60 menit**.\n- Dua menit sebelum sesi berakhir, sistem akan menampilkan jendela peringatan hitung mundur dengan tombol **Perpanjang Sesi**.\n- Jika tidak ada aktivitas hingga waktu habis, akun akan keluar otomatis (*Auto Logout*).\n- Jika muncul pesan *419 Page Expired* karena halaman dibiarkan terlalu lama, cukup muat ulang halaman dan login kembali.",
                'target_role' => 'all',
                'order' => 24,
                'is_active' => true,
            ],
            [
                'category' => 'teknis',
                'question' => 'Bagaimana cara mengganti password standar (NPM/NIDN) demi keamanan akun?',
                'answer' => "Akun yang masih menggunakan password bawaan (sama dengan NPM/NIDN) akan mendapatkan peringatan keamanan saat login.\n1. Buka menu **Profil** (pojok kanan atas).\n2. Gulir ke bagian **Perbarui Password**.\n3. Masukkan password saat ini dan buat password baru yang kuat (tidak boleh sama dengan NPM/NIDN).\n4. Klik **Simpan**. Jika Anda lupa password, Admin/Kaprodi dapat melakukan reset password ke NPM/NIDN melalui Manajemen Pengguna.",
                'target_role' => 'all',
                'order' => 25,
                'is_active' => true,
            ],
            [
                'category' => 'teknis',
                'question' => 'Apakah SIBIMA bisa dipasang seperti aplikasi di HP (Android / iOS)?',
                'answer' => "Ya, SIBIMA mendukung teknologi **Progressive Web App (PWA)** sehingga dapat dipasang langsung ke layar utama HP tanpa perlu Play Store/App Store:\n- **Android (Chrome)**: Ketuk banner **Pasang SIBIMA** yang muncul di bagian bawah layar, atau ketuk menu titik tiga (⋮) > **Tambahkan ke Layar Utama / Instal Aplikasi**.\n- **iPhone/iPad (Safari)**: Ketuk tombol **Bagikan (Share)** di bilah bawah Safari, lalu pilih **Tambahkan ke Layar Utama (Add to Home Screen)**.",
                'target_role' => 'all',
                'order' => 26,
                'is_active' => true,
            ],
            [
                'category' => 'teknis',
                'question' => 'Bagaimana cara melaporkan bug, error halaman, atau mengusulkan fitur baru?',
                'answer' => 'Anda dapat menekan tombol **Lapor Kendala** di bagian footer bawah halaman (atau widget kapsul melayang jika diaktifkan). Tuliskan judul kendala, kategori, deskripsi langkah kejadian, serta lampirkan tangkapan layar (*screenshot*) bila ada. Tim pengembang dan Admin Program Studi akan menindaklanjuti tiket laporan Anda.',
                'target_role' => 'all',
                'order' => 27,
                'is_active' => true,
            ],

            // =================================================================
            // 6. UMUM (5 FAQ)
            // =================================================================
            [
                'category' => 'umum',
                'question' => 'Apa itu SIBIMA dan siapa saja penggunanya?',
                'answer' => "SIBIMA (**Sistem Informasi Bimbingan Mahasiswa**) adalah platform akademik terpadu Fakultas Ilmu Komputer (FASILKOM) Universitas Subang untuk mengelola seluruh siklus tugas akhir/skripsi secara digital.\n\nSIBIMA digunakan oleh 4 peran utama:\n- **Mahasiswa**: Pengajuan judul, penjadwalan & logbook bimbingan, pendaftaran seminar/sidang, ruang revisi, yudisium, dan repositori.\n- **Dosen**: Penjadwalan bimbingan (tunggal/massal), pemberian feedback & ACC, penilaian ujian, dan persetujuan revisi.\n- **Kaprodi & Admin**: Penetapan pembimbing, pengelolaan gelombang & jadwal ujian, monitoring progres, surat tugas/berita acara, dan analitik akademik.",
                'target_role' => 'all',
                'order' => 28,
                'is_active' => true,
            ],
            [
                'category' => 'umum',
                'question' => 'Bagaimana cara mencari referensi di Repositori Digital dan Katalog Jurnal Ilmiah?',
                'answer' => "Buka menu **Repositori & Pustaka** pada sidebar:\n- **Repositori Skripsi**: Telusuri arsip skripsi lulusan berdasarkan judul, penulis, pembimbing, atau tahun, lalu baca naskah Bab 1–6 langsung melalui **PDF Reader** internal.\n- **Katalog Jurnal Ilmiah**: Cari artikel ilmiah terintegrasi dari **Jurnal GLOBAL FASILKOM UNSUB**, **GARUDA**, dan **OpenAlex** lengkap dengan filter SINTA, ringkasan cerdas (**Smart AI TL;DR**), serta rekomendasi kata kunci.\n- **Katalog E-Book**: Akses buku referensi Sistem Informasi & Teknologi Informasi.",
                'target_role' => 'all',
                'order' => 29,
                'is_active' => true,
            ],
            [
                'category' => 'umum',
                'question' => 'Bagaimana cara menyimpan referensi ke Bookmark dan mengekspor sitasi (.RIS / Word)?',
                'answer' => "Saat menemukan jurnal atau repositori yang relevan, klik ikon **Bookmark** untuk menyimpannya ke koleksi pribadi Anda.\n1. Buka menu **Bookmark Saya** untuk mengelompokkan referensi ke dalam **Folder** (misalnya per Bab 1, Bab 2, Metodologi) dan menambahkan catatan pribadi.\n2. Klik **Ekspor Sitasi** untuk mengunduh daftar referensi dalam format `.RIS` (kompatibel dengan Mendeley/Zotero) atau menyalin format sitasi standar FASILKOM.",
                'target_role' => 'all',
                'order' => 30,
                'is_active' => true,
            ],
            [
                'category' => 'umum',
                'question' => 'Di mana saya bisa melihat Kalender Akademik dan Catatan Pembaruan (Changelog) SIBIMA?',
                'answer' => "- **Kalender Akademik**: Akses menu **Kalender** di sidebar untuk melihat seluruh jadwal gelombang pendaftaran, jadwal bimbingan, seminar proposal, dan sidang skripsi dalam tampilan kalender interaktif.\n- **Catatan Versi (Changelog)**: Klik badge nomor versi (misal `v2.6.0`) di pojok kanan bawah footer halaman untuk melihat daftar pembaruan fitur dan perbaikan terbaru SIBIMA.",
                'target_role' => 'all',
                'order' => 31,
                'is_active' => true,
            ],
            [
                'category' => 'umum',
                'question' => 'Bagaimana cara kerja Verifikasi QR Code pada dokumen resmi SIBIMA?',
                'answer' => 'Dokumen resmi yang dihasilkan SIBIMA (seperti **Kartu Bimbingan Digital**, **Lembar Revisi**, **Surat Tugas**, **Berita Acara**, dan **SKL**) dilengkapi dengan **QR Code** unik. Cukup pindai QR Code tersebut menggunakan kamera HP untuk membuka halaman verifikasi resmi SIBIMA yang memastikan dokumen benar-benar sah dan diterbitkan oleh sistem.',
                'target_role' => 'all',
                'order' => 32,
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

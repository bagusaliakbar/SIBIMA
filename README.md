# SIBIMA - Sistem Informasi Bimbingan Mahasiswa

<p align="center">
  <img src="public/img/logo.png" alt="SIBIMA Logo" width="180">
</p>

<p align="center">
  <strong>Platform Terpadu Manajemen Skripsi, Ujian Akademik, dan Katalog Ilmiah</strong><br>
  Fakultas Ilmu Komputer — Universitas Subang
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js">
  <img src="https://img.shields.io/badge/Laravel_Reverb-WebSockets-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel Reverb">
  <img src="https://img.shields.io/badge/Pest_PHP-Tested-4F46E5?style=for-the-badge&logo=php&logoColor=white" alt="Pest PHP Tested">
</p>

---

## 📖 Tentang SIBIMA

**SIBIMA (Sistem Informasi Bimbingan Mahasiswa)** adalah platform komprehensif yang mendigitalisasi dan mengotomatisasi seluruh siklus hidup penyusunan skripsi mahasiswa di Fakultas Ilmu Komputer Universitas Subang. 

Sistem ini memfasilitasi kolaborasi transparan dan efisien antara **Mahasiswa**, **Dosen Pembimbing**, **Dosen Penguji**, dan **Pihak Program Studi (Admin & Kaprodi)** — mulai dari pengajuan judul, bimbingan berkala, pelaksanaan ujian (Seminar Usulan Penelitian & Sidang Skripsi), verifikasi legalitas dokumen digital, hingga penelusuran arsip skripsi melalui katalog pustaka digital.

---

## 🌟 Fitur Utama & Modul Unggulan

### 1. 📚 Katalog Pustaka Skripsi Terpadu (*Smart Thesis Repository*)
* **Sinkronisasi Multi-Sumber Otomatis**: Menghubungkan dan menyinkronkan ribuan data skripsi dari **Website FASILKOM UNSUB** dan **Repositori Institusi UNSUB (API)** secara cerdas.
* **On-Demand Streaming (Zero Storage Footprint)**: Menggunakan arsitektur *reverse proxy streaming* yang menyajikan naskah PDF langsung ke browser tanpa menghabiskan kuota penyimpanan (*storage*) server VPS saat sinkronisasi (0 MB saat sync).
* **In-App Multi-Chapter PDF Reader (PDF.js)**: Pembaca PDF interaktif di dalam aplikasi yang mendukung pemilihan berkas naskah lengkap dari **BAB 1 s/d BAB 6** dengan fitur zoom, navigasi cepat, dan mode layar penuh.
* **Bypass CORS & Keamanan Same-Origin**: Mengeliminasi kendala pembatasan lintas domain browser saat membaca file dari repositori kampus.
* **Auto-Cache Cleanup Terjadwal**: Perintah otomatis mingguan (`php artisan repositories:clean-cache`) untuk menjaga kapasitas disk VPS tetap ramping dan optimal.
* **Smart Topic & Filter**: Kategorisasi otomatis ke dalam topik ilmiah (*Web, Mobile, AI/Data Science, SPK, UI/UX, IoT, E-Commerce*) serta pencarian cepat berdasarkan angkatan dan dosen pembimbing.
* 📄 *Dokumentasi lengkap arsitektur repositori dapat dibaca pada: [`docs/arsitektur_katalog_pustaka.md`](docs/arsitektur_katalog_pustaka.md)*.

### 2. 📝 Siklus Bimbingan Skripsi & Logbook Digital
* **Logbook Sesi Bimbingan**: Pencatatan riwayat konsultasi, pembahasan materi bimbingan, batas minimal bimbingan, dan unggah berkas naskah revisi.
* **Presensi Real-Time Bimbingan**: Mahasiswa dan dosen dapat mengonfirmasi kehadiran bimbingan secara langsung (*real-time attendance*) dengan dukungan verifikasi kode.
* **Indikator Kuota & Workload Dosen**: Pemantauan rasio mahasiswa bimbingan per dosen untuk pemerataan distribusi akademik.
* **Student Health Indicator**: Sistem deteksi dini otomatis untuk menandai mahasiswa di semester kritis (Semester 13–14+) agar mendapatkan penanganan akademik prioritas.

### 3. 🎓 Manajemen Ujian Akademik (Seminar UP & Sidang Akhir)
* **Smart Scheduling (Deteksi Bentrok Jadwal)**: Penjadwalan cerdas yang secara otomatis mencegah bentrok ruangan, waktu, maupun dosen penguji.
* **Penilaian Online Multi-Penguji**: Form penilaian digital untuk Penguji I, Penguji II, dan Pembimbing dengan kalkulasi nilai akhir otomatis dan predikat kelulusan.
* **Berita Acara Satu Halaman Presisi**: Pembangkit dokumen resmi Berita Acara berformat A4 yang memuat rekapitulasi nilai transparan dari ketiga dosen dalam tata letak yang rapi.
* **Pembuatan SK Tim Penguji Kolektif**: Generator SK kolektif per jadwal ujian guna mencegah duplikasi berkas administrasi.
* **Alur Diskusi Revisi Pasca-Ujian**: Ruang interaksi terstruktur bagi mahasiswa untuk mengunggah berkas revisi dan memperoleh persetujuan (*approval*) langsung dari masing-masing dosen penguji.

### 4. 🔏 Keamanan, Integritas & Legalitas Dokumen
* **QR Code Verification Token**: Setiap dokumen resmi (SK dan Berita Acara) dilengkapi QR Code unik yang dapat dipindai publik untuk memeriksa keaslian dokumen secara instan tanpa perlu login.
* **Digital Signature Token**: Pengamanan tanda tangan elektronik dosen dan pimpinan berbasis token terenkripsi.
* **Dynamic Letter Settings**: Sistem penomoran surat otomatis dengan format dinamis (`LetterSetting`) yang menjamin nomor surat urut dan tidak ganda.
* **Penyimpanan Privat Terisolasi**: Dokumen sensitif (draft skripsi, naskah revisi) tersimpan di storage privat yang hanya dapat diakses melalui otorisasi *Role-Based Access Control (RBAC)*.
* **Audit Trail Lengkap (Activity Logs)**: Rekaman komprehensif setiap aksi penting dengan pelacakan perbandingan data lama vs data baru.

### 5. 📲 WhatsApp Automation Gateway (Fonnte API)
* **Pengingat H-1 & H-3 Ujian**: Mengirim pesan pengingat jadwal Seminar UP dan Sidang Skripsi secara otomatis ke WhatsApp mahasiswa dan dosen penguji.
* **Pengingat H-1 Sesi Bimbingan**: Notifikasi pengingat agenda bimbingan tatap muka maupun daring.
* **Peringatan Mahasiswa Kritis**: Notifikasi berkala kepada mahasiswa semester akhir dan laporan rekap bulanan otomatis langsung ke nomor WhatsApp Kaprodi.
* **Master Toggle Switch**: Sakelar kendali di panel admin untuk mengaktifkan atau menonaktifkan pengiriman WhatsApp secara instan.

### 6. 📊 Reporting Center & Analitik Interaktif
* **Pusat Laporan Komprehensif**: Analisis tren kelulusan, statistik bidang minat, waktu pengerjaan skripsi, dan performa bimbingan dosen berbasis Chart.js.
* **Batch Export Berita Acara (ZIP)**: Kemudahan ekspor massal seluruh berkas Berita Acara ke dalam satu arsip ZIP siap cetak.
* **Multi-Format Export**: Dukungan unduh laporan dalam format Excel (`.xlsx` multi-sheet) dan PDF.

### 7. ⚡ Real-Time WebSockets & Modern UI/UX
* **Laravel Reverb**: Notifikasi instan di dalam web tanpa perlu me-refresh halaman (pemberitahuan persetujuan judul, konfirmasi jadwal, ACC bimbingan).
* **Adaptive Dark Mode & Light Mode**: Desain antarmuka modern yang nyaman di mata dengan transisi halus di mode gelap maupun terang.
* **Metronic 9 UI Kit + Alpine.js**: Antarmuka responsif berkinerja tinggi yang ringan dan ramah perangkat seluler.

---

## 🛠️ Arsitektur & Teknologi

| Lapisan | Teknologi / Library | Keterangan |
| :--- | :--- | :--- |
| **Core Framework** | [Laravel 12](https://laravel.com) | Arsitektur MVC dengan pemisahan *Dedicated Service Layer* (13+ Service Classes). |
| **Real-Time Engine** | [Laravel Reverb](https://reverb.laravel.com) & Laravel Echo | Server WebSocket mandiri berkinerja tinggi untuk live update. |
| **Frontend UI** | [Tailwind CSS](https://tailwindcss.com) & [Alpine.js](https://alpinejs.dev) | Styling utility-first modern dan interaktivitas reaktif tanpa beban framework SPA berat. |
| **PDF Viewing** | [PDF.js (Mozilla)](https://mozilla.github.io/pdf.js/) | In-app document reader dengan streaming proxy same-origin. |
| **Document Engine** | [DomPDF](https://github.com/barryvdh/laravel-dompdf) & [Laravel Excel](https://laravel-excel.com) | Pembangkit PDF server-side dan generator lembar kerja Excel multi-sheet. |
| **Database** | MySQL 8.x / MariaDB | Relasi Eloquent ORM lengkap dengan indexing dan migrasi terstruktur. |
| **Notification Gateway**| Fonnte WhatsApp API | Pengiriman notifikasi pesan WhatsApp otomatis dengan mekanisme anti-spam. |
| **Testing Framework** | [Pest PHP v3](https://pestphp.com) | Pengujian otomatis (*Automated Feature & Unit Testing*). |

---

## 🚀 Panduan Instalasi Lokal (Development)

Pastikan lingkungan komputer Anda telah terpasang:
* **PHP >= 8.2** (ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `xml`, `dom`, `curl`, `zip`, `fileinfo`)
* **Composer >= 2.x**
* **Node.js >= 18.x & NPM**
* **MySQL >= 8.0**

### Langkah-langkah:

1. **Clone Repositori:**
   ```bash
   git clone https://github.com/BagzAlz/sibima.git
   cd sibima
   ```

2. **Instal Dependensi Backend & Frontend:**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Lingkungan (`.env`):**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Sesuaikan konfigurasi database (`DB_*`), Reverb (`REVERB_*`), dan kredensial WhatsApp Fonnte di file `.env`.

4. **Migrasi Basis Data & Seeder:**
   ```bash
   php artisan migrate --seed
   ```

5. **Hubungkan Storage Publik:**
   ```bash
   php artisan storage:link
   ```

6. **Kompilasi Aset Frontend:**
   ```bash
   npm run dev
   # Atau untuk build produksi:
   npm run build
   ```

7. **Jalankan Aplikasi:**
   ```bash
   php artisan serve
   ```
   *(Opsional untuk WebSocket Real-time)*:
   ```bash
   php artisan reverb:start
   ```

---

## ⏱️ Penjadwalan Tugas Otomatis (*Cron Scheduler*)

SIBIMA mengandalkan Laravel Scheduler untuk menjalankan otomatisasi berkala. Di server produksi (VPS Linux), pasang entri cron job berikut:

```bash
* * * * * cd /path-to-sibima && php artisan schedule:run >> /dev/null 2>&1
```

### Tugas Terjadwal yang Dikelola Otomatis:
| Perintah Artisan | Jadwal Eksekusi | Deskripsi Tugas |
| :--- | :--- | :--- |
| `app:send-schedule-reminders` | Setiap Hari (07:00 WIB) | Mengirim pengingat WhatsApp H-1 & H-3 Seminar UP / Sidang Akhir ke mahasiswa dan dosen. |
| `app:send-mentoring-reminders` | Setiap Hari (07:15 WIB) | Mengirim pengingat WhatsApp H-1 agenda bimbingan skripsi. |
| `app:send-critical-student-reminders`| Tgl 1 Tiap Bulan (08:00 WIB) | Notifikasi peringatan ke mahasiswa semester 13–14+ dan laporan rekap ke Kaprodi. |
| `repositories:clean-cache --days=7` | Setiap Minggu (02:00 WIB) | Membersihkan berkas cache PDF repositori skripsi yang lebih tua dari 7 hari guna menjaga kapasitas disk VPS. |

---

## 🧪 Pengujian Otomatis (*Automated Testing*)

Sistem diuji menggunakan **Pest PHP** untuk memastikan keandalan alur bisnis:

```bash
# Menjalankan seluruh test suite:
php artisan test

# Menjalankan pengujian khusus repositori & streaming:
php artisan test --filter=UnsubRepositorySyncTest

# Menjalankan pengujian pembersihan cache:
php artisan test --filter=CleanThesisCacheTest
```

---

## 📁 Dokumentasi Tambahan

* 🏗️ **Arsitektur Teknis Katalog Pustaka & Repositori UNSUB**: [`docs/arsitektur_katalog_pustaka.md`](docs/arsitektur_katalog_pustaka.md)
* 🧰 **Daftar Lengkap Stack & Dependensi**: [`TECH_STACK.md`](TECH_STACK.md)

---

## 👨‍💻 Kontributor & Pengembang

* **Bagus Ali Akbar** — [@BagzAlz](https://github.com/BagzAlz) — Pengembang Utama SIBIMA

---

## 📄 Lisensi

Aplikasi SIBIMA dikembangkan untuk keperluan akademik Fakultas Ilmu Komputer Universitas Subang di bawah lisensi [MIT License](LICENSE).

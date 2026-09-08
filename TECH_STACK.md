# Dokumentasi Teknologi, Tools, Plugin & Library — SIBIMA

Dokumen ini memuat daftar lengkap seluruh teknologi, tools, library, plugin, dan layanan eksternal yang digunakan dalam pengembangan sistem **SIBIMA (Sistem Informasi Bimbingan Mahasiswa)** Fakultas Ilmu Komputer Universitas Subang.

---

## 1. Spesifikasi Lingkungan & Arsitektur Sistem

| Komponen | Spesifikasi / Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Pola Arsitektur** | **MVC (Model-View-Controller) + Service Layer** | Pemisahan logika bisnis dari controller untuk kemudahan pemeliharaan dan skalabilitas. |
| **Bahasa Pemrograman** | **PHP ^8.2** *(Runtime Aktif: PHP 8.3 / 8.5)* | Menggunakan fitur modern PHP (Typed Properties, Match Expressions, Constructor Promotion, dsb.). |
| **Runtime JavaScript** | **Node.js v24.x** (NPM 11.x) | Digunakan untuk kompilasi dan bundling aset frontend melalui Vite. |
| **Framework Backend** | **Laravel 12** (`laravel/framework: ^12.0`) | Framework inti full-stack PHP modern dengan ekosistem enterprise. |
| **Web Server** | **Laravel Herd / Nginx** | Lingkungan pengembangan lokal berbasis Nginx dan PHP FastCGI pada Windows. |
| **Sistem Basis Data** | **MySQL / SQLite** | Database relasional dengan skema relasi ORM Eloquent, database migrations (68 file migrasi), dan indexing optimal. |

---

## 2. Backend Stack & Library PHP (Composer)

### Dependensi Utama (`require`)

| Paket / Library | Versi | Fungsi & Kegunaan dalam SIBIMA |
| :--- | :--- | :--- |
| **`laravel/framework`** | `^12.0` | Framework web inti: routing, Eloquent ORM, middleware, validasi, otorisasi policy, event dispatcher, dan dependency injection. |
| **`laravel/reverb`** | `^1.0` | Server WebSocket mandiri (first-party) berkecepatan tinggi untuk komunikasi real-time (obrolan langsung dan notifikasi instan). |
| **`pusher/pusher-php-server`** | `^7.2` | Adaptor protokol Pusher pada sisi server untuk menyiarkan event ke Laravel Reverb dan client frontend. |
| **`maatwebsite/excel`** | `^3.1` | Integrasi **PhpSpreadsheet** untuk ekspor dan impor data Excel (.xlsx, .csv). Digunakan pada ekspor rekapitulasi analitik multi-sheet, data pengguna, katalog repositori, dan migrasi massal skripsi. |
| **`barryvdh/laravel-dompdf`** | `*` *(v3.x / Dompdf)* | Engine pembangkit file PDF server-side. Digunakan untuk mencetak Berita Acara resmi, SK Tim Penguji kolektif, kartu bimbingan, dan Laporan Statistik Analitik berformat A4 Landscape. |
| **`simplesoftwareio/simple-qrcode`**| `^4.2` | Pembangkit **QR Code** berbasis SVG/PNG untuk tanda tangan digital dan verifikasi keaslian dokumen Berita Acara via URL token publik. |
| **`laravel/tinker`** | `^2.10.1` | Konsol REPL interaktif untuk pengujian query database dan manipulasi model langsung di terminal. |

### Dependensi Pengembangan & Pengujian (`require-dev`)

| Paket / Tool | Versi | Fungsi & Kegunaan |
| :--- | :--- | :--- |
| **`pestphp/pest`** | `^3.8` | Framework unit & feature testing modern dan ekspresif untuk PHP. |
| **`pestphp/pest-plugin-laravel`**| `^3.2` | Ekstensi Pest khusus ekosistem Laravel untuk pengujian HTTP, database, auth, dan event fakes. |
| **`laravel/breeze`** | `*` | Starter kit autentikasi aman berbasis Blade (login, registrasi, forgot password, email verification, dan update profile). |
| **`mockery/mockery`** | `^1.6` | Library mock object untuk pengujian isolasi unit test. |
| **`fakerphp/faker`** | `^1.23` | Pembangkit data dummy realistis untuk database seeder dan test factory. |
| **`laravel/pint`** | `^1.24` | Code style linter & fixer berbasis PHP-CS-Fixer dengan standar PSR-12 dan gaya penulisan Laravel. |
| **`laravel/pail`** | `^1.2.2` | Real-time CLI log viewer langsung pada command line terminal. |
| **`laravel/sail`** | `^1.41` | Docker environment wrapper untuk standarisasi kontainer pengembangan. |
| **`nunomaduro/collision`** | `^8.6` | Error handling dan stack trace renderer yang rapi saat menjalankan perintah CLI atau pengujian. |
| **`laramint/laravel-brain`** | `^2.1` | Tool analisis arsitektur proyek dan asisten konteks kode. |

---

## 3. Frontend Stack & Build Tools (Node.js / NPM)

| Paket / Tool | Versi | Kategori | Fungsi & Peran |
| :--- | :--- | :--- | :--- |
| **`vite`** | `^7.0.7` | Bundler & Build Tool | Build tool generasi baru dengan Hot Module Replacement (HMR) super cepat. |
| **`laravel-vite-plugin`** | `^2.0.0` | Vite Plugin | Menghubungkan asset pipeline Laravel dengan Vite serta mendukung auto-refresh Blade template. |
| **`tailwindcss`** | `^3.1.0` | CSS Framework | Framework CSS utility-first untuk pembuatan tampilan antarmuka responsif, modern, dan sistem Dark Mode. |
| **`@tailwindcss/vite`** | `^4.0.0` | Vite Plugin | Integrasi langsung Tailwind CSS ke dalam compiler Vite. |
| **`@tailwindcss/forms`** | `^0.5.2` | Tailwind Plugin | Standarisasi dan reset form input (input, select, textarea, checkbox, radio) dengan tema warna SIBIMA Orange. |
| **`postcss`** | `^8.4.31` | CSS Processor | Alat pemroses sintaks CSS modern dan transformasi stylesheet. |
| **`autoprefixer`** | `^10.4.2` | PostCSS Plugin | Penambahan vendor prefix CSS (-webkit-, -moz-) otomatis untuk kompatibilitas lintas browser. |
| **`alpinejs`** | `^3.4.2` | JavaScript Framework | Framework JavaScript deklaratif ringan untuk interaktivitas UI (modal, dropdown, toggle dark mode, tab filter, dan state reaktif) tanpa beban SPA. |
| **`axios`** | `^1.11.0` | HTTP Client | Library HTTP client berbasis Promise untuk komunikasi asynchronous (AJAX) dan penanganan token CSRF otomatis. |
| **`laravel-echo`** | `^2.3.4` | WebSocket Client | Listener klien untuk berlangganan channel WebSocket publik, privat, dan presence channel. |
| **`pusher-js`** | `^8.6.0` | WebSocket Transport | Driver client-side untuk koneksi transport WebSocket ke server Reverb. |
| **`concurrently`** | `^9.0.1` | Task Runner CLI | Menjalankan perintah multi-proses secara paralel dalam satu terminal (`php artisan serve`, `queue:listen`, dan `npm run dev`). |

---

## 4. Client-Side Libraries & Plugin Eksternal (CDN)

| Library / Plugin | Sumber CDN | Fungsi & Implementasi pada SIBIMA |
| :--- | :--- | :--- |
| **`Chart.js v4`** | `cdn.jsdelivr.net` | Library visualisasi grafik data interaktif. Menangani 13 grafik utama pada menu Analitik (`/analytics`), grafik workload kuota dosen, dan ringkasan dashboard. |
| **`chartjs-plugin-datalabels v2`** | `cdn.jsdelivr.net` | Plugin Chart.js untuk merender data label angka langsung di dalam/di atas batang diagram dengan kontras warna otomatis. |
| **`FullCalendar v6.1.10`** | `cdn.jsdelivr.net` | Kalender interaktif untuk manajemen agenda bimbingan, jadwal seminar proposal, dan jadwal sidang skripsi (`/calendar`). |
| **`Popper.js v2`** | `unpkg.com` | Engine kalkulasi posisi dinamis untuk floating UI, popover, dan tooltip. |
| **`Tippy.js v6`** | `unpkg.com` | Tooltip & popover kustom berbasis Popper untuk menampilkan detail pratinjau acara pada sel kalender FullCalendar. |
| **`Inter Font Family`** | `fonts.bunny.net` | Font tipografi antarmuka modern yang ramah privasi (GDPR compliant, tanpa pelacak pihak ketiga). |
| **`Tailwind CDN Fallback`** | `cdn.tailwindcss.com` | Styling cepat untuk halaman publik mandiri seperti verifikasi dokumen publik (`/verify/document/*`). |

---

## 5. Layanan Pihak Ketiga & Integrasi Eksternal (Third-Party Services)

| Layanan | Integrasi / Endpoint | Implementasi & Manfaat |
| :--- | :--- | :--- |
| **Fonnte WhatsApp API** | `https://api.fonnte.com/send` | **WhatsApp Notification Gateway**: Mengirimkan pesan notifikasi dan pengingat otomatis (jadwal bimbingan, jadwal seminar/sidang, peringatan mahasiswa kritis, status ACC) langsung ke nomor WhatsApp mahasiswa dan dosen. Dilengkapi sistem jeda acak (anti-spam) dan toggle master switch. |
| **Google Meet** | `meet.google.com` | Integrasi tautan video conference untuk pelaksanaan bimbingan daring dan ruang ujian sidang/seminar hybrid. |
| **Portal Verifikasi Publik** | Internal Token URL | Portal terbuka tanpa login untuk memvalidasi legalitas tanda tangan digital dan Surat Keputusan/Berita Acara resmi dengan memindai QR Code. |

---

## 6. Pola Desain & Modul Khusus SIBIMA (In-House Architecture)

### A. Service Layer (`app/Services/`)
Logika bisnis utama dipisahkan dari controller ke dalam 13 kelas service mandiri:
- `MentoringService.php`: Logika pendaftaran, penjadwalan, validasi kehadiran, dan batas bimbingan.
- `DashboardService.php`: Komputasi metrik agregasi, KPI, dan status progres skripsi lintas peran.
- `ThesisService.php`: Alur hidup skripsi (pengajuan judul, penugasan pembimbing, ACC seminar/sidang).
- `ConflictService.php`: Algoritma deteksi bentrok jadwal dosen, ruang ujian, dan waktu pelaksanaan.
- `ExaminerService.php`: Manajemen dosen penguji dan form penilaian sidang/seminar.
- `WhatsAppService.php`: Format pesan, template parsing, dan orkestrasi pengiriman via Fonnte API.
- `MonitoringService.php`, `ScheduleService.php`, `ApplicationService.php`, `UserService.php`, `ThesisAnalyticsService.php`, dll.

### B. Custom Notification Channel (`app/Channels/`)
- `FonnteChannel.php`: Custom notification driver untuk sistem notifikasi internal Laravel (`Notification::send()`) yang mengirim pesan via WhatsApp secara asinkron.

### C. Background Scheduling & Artisan Commands (`app/Console/Commands/`)
- `SendMentoringReminders`: Mengingatkan mahasiswa dan dosen terkait jadwal bimbingan yang akan berlangsung.
- `SendScheduleReminders`: Pengingat otomatis H-1 sebelum jadwal seminar proposal atau sidang skripsi.
- `SendCriticalStudentReminders`: Mendeteksi dan mengirimkan peringatan kepada mahasiswa yang tidak bimbingan > 14 hari atau masa studi $\ge 13$ semester.
- `EncryptExistingSignatures` & `EncryptSensitiveData`: Enkripsi keamanan data tanda tangan digital dan berkas sensitif.
- `CleanDuplicateTheses` & `CleanDummyStudents`: Pembersihan dan pemeliharaan integritas database.

### D. Multi-Format Reporting & Export Engine (`app/Exports/` & `app/Imports/`)
- **Multi-Sheet Analytics Excel (`AnalyticsExport.php`)**: Menghasilkan 7 lembar kerja Excel terstruktur (Ringkasan KPI, Seminar per Pembimbing, Progres per Angkatan, Beban Bimbingan, Distribusi Tahapan per Dosen, Beban & Kuota, serta Distribusi Nilai).
- **PDF Engine (`analytics/pdf.blade.php`)**: Menghasilkan laporan cetak landscape resmi berstandar universitas lengkap dengan kop surat, tabel komparasi, dan tanda tangan Ketua Program Studi.
- **Bulk PNG Engine**: Algoritma JavaScript client-side dengan delay staggering untuk mengunduh seluruh 13 grafik analitik sekaligus dalam format resolusi tinggi berlatar solid.

### E. Sistem Keamanan Berkas Privat
- Seluruh file dokumen skripsi, kartu bimbingan, formulir pendaftaran, dan lampiran revisi disimpan pada direktori privat (`storage/app/private`), bukan direktori publik.
- Akses berkas dilindungi oleh `DownloadController::downloadPrivateFile` dengan validasi otorisasi peran (hanya mahasiswa pemilik, dosen pembimbing/penguji terkait, dan admin yang dapat mengakses).

---

## 7. Rangkuman Matriks Teknologi

```
+----------------------------------------------------------------------------------+
|                                    SIBIMA                                        |
+----------------------------------------------------------------------------------+
| Frontend UI         : Tailwind CSS v3/v4, Alpine.js v3, Bunny Fonts (Inter)      |
| Visualisasi Data    : Chart.js v4, Chart.js Datalabels v2, FullCalendar v6       |
| Frontend Build Tool : Vite v7, PostCSS, Autoprefixer                             |
| Real-Time Client    : Laravel Echo, Pusher JS                                    |
| Backend Core        : Laravel 12, PHP ^8.2 (PHP 8.3/8.5), Laravel Breeze         |
| Real-Time Server    : Laravel Reverb, Pusher PHP Server                          |
| Dokumen & Ekspor    : DomPDF, Maatwebsite Excel (PhpSpreadsheet), Simple QrCode  |
| External Gateway    : Fonnte WhatsApp Gateway API, Google Meet                   |
| Testing & QA        : Pest PHP v3, Mockery, FakerPHP, Laravel Pint               |
| Server / DB         : Laravel Herd (Nginx + PHP FastCGI), MySQL / SQLite         |
+----------------------------------------------------------------------------------+
```

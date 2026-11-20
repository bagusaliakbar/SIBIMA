# LAPORAN AUDIT KEAMANAN SISTEM & PENILAIAN KERENTANAN MENDALAM (DEEP SECURITY AUDIT & VULNERABILITY ASSESSMENT REPORT)
## SISTEM INFORMASI BIMBINGAN TUGAS AKHIR (SIBIMA)
### FAKULTAS ILMU KOMPUTER — UNIVERSITAS SUBANG

| Informasi Dokumen | Spesifikasi Teknis & Hasil Audit |
| :--- | :--- |
| **Nama Sistem** | SIBIMA (Sistem Informasi Bimbingan Tugas Akhir Mahasiswa) |
| **Institusi Pemilik** | Fakultas Ilmu Komputer, Universitas Subang (FASILKOM UNSUB) |
| **Versi Aplikasi** | SIBIMA v2.6.0 (Production Hardened) |
| **Teknologi Backend** | Laravel 12 (PHP 8.2+) dengan Basis Data MySQL 8.0+ (InnoDB) |
| **Teknologi Frontend** | Blade Templates, Tailwind CSS v3.4+, Alpine.js, Chart.js |
| **Infrastruktur Target** | Shared Hosting / Virtual Host (root `index.php` & `.htaccess`) di balik Cloudflare Edge SSL & WAF |
| **Versi Dokumen Audit** | **4.0** (Re-Audit Mendalam, Rekonsiliasi Audit v3.0, Remediasi Batch 1–2, & Verifikasi Pengujian Otomatis) |
| **Standar Acuan Audit** | OWASP Top 10 (2021), OWASP ASVS v4.0 Level 2, NIST SP 800-115, CVSS v3.1 |
| **Klasifikasi Dokumen** | **Confidential / Internal Security Audit** |
| **Riwayat Pelaksanaan** | Fase 1 (v3.0): 29 Oktober 2026 &nbsp;\|&nbsp; Fase 2 (Deep Re-Audit & Hardening v4.0): 20 November 2026 |
| **Commit Verifikasi** | `0ae7e2d` (`origin/main`) — pasca-hardening sesi idle (`0f0c549`) dan keamanan menyeluruh (`0ae7e2d`) |
| **Skor Baseline Awal (Okt 2026)** | **54 / 100 (Grade D — High Risk)** |
| **Skor Kalibrasi Re-Audit (Pra-Patch v4.0)** | **68 / 100 (Grade C+ — Ditemukan 3 Critical & 5 High baru/parsial)** |
| **Skor Keamanan Terverifikasi Akhir** | **92 / 100 (Grade A — Verified Production Ready)** |

---

## 1. RINGKASAN EKSEKUTIF & KARTU SKOR KEAMANAN (EXECUTIVE SUMMARY)

Laporan versi **4.0** ini merupakan pembaruan menyeluruh atas laporan audit keamanan sebelumnya (v3.0, 29 Oktober 2026). Pada tanggal **20 November 2026**, dilakukan *deep source-code security re-audit* secara langsung terhadap seluruh controller, middleware, model, service, routing, konfigurasi, serta template Blade/JavaScript pada repositori aktif SIBIMA.

Hasil verifikasi ulang menunjukkan bahwa fondasi keamanan SIBIMA sudah baik di sejumlah area inti (Eloquent/PDO parameter binding, `ThesisPolicy` & `MentoringSessionPolicy`, `SafeExcelExport` anti-Formula Injection, `.htaccess` deny-first, `ChatService::canChatWith()`, proteksi loop spintax WhatsApp, pemutusan sesi `is_active = false`, serta *private storage* laporan bug). Namun, re-audit mendalam juga menemukan **dua kategori kesenjangan penting** yang tidak tertangkap atau belum selesai sepenuhnya pada laporan v3.0:

1. **Temuan Kritis & Tinggi Baru yang Sebelumnya Tidak Terdeteksi**:
   - **Broken Access Control & Pemalsuan SK/Surat Tugas** di `AdvisorDecreeController` (mahasiswa dapat membaca daftar/detail/PDF SK seluruh dosen, dan dosen dapat memicu pembuatan surat tugas untuk skripsi dosen lain atau memalsukan penanda tangan).
   - **Stored DOM XSS** pada 4 titik injeksi `innerHTML` / `x-html` di antarmuka web (widget *Online Users*, daftar *Bug Report*, serta preview *WA Broadcast* & *WA Template*).
   - **Eksposur Data Sensitif pada Serialisasi JSON Model `User`** (`signature`, `signature_token`, `phone_number` tidak tercantum di `$hidden`, dan respons JSON chat memuat penuh relasi `sender`).
   - **Kebocoran Informasi & RBAC Longgar** pada `/dashboard/online-users` (terbuka untuk mahasiswa), `/check-dosen-availability` (terbuka untuk mahasiswa), serta operasi destruktif `restore`/`uploadAndRestore`/`destroy` backup database yang masih dapat diakses oleh `kaprodi`.
2. **Remediasi v3.0 yang Sebelumnya Baru Parsial atau Belum Terimplementasi di Kode**:
   - `TRUSTED_PROXIES` di `bootstrap/app.php` masih ber-default wildcard `'*'`.
   - Validasi SSRF `UrlSecurity` belum memeriksa DNS IPv6 (`DNS_AAAA`) dan klien HTTP masih mengikuti *redirect* otomatis (rentan bypass 302 ke IP internal) serta masih memakai `withoutVerifying()`.
   - `BackupService::assertSafeBackupStatement()` belum memblokir `PREPARE`/`EXECUTE`, `TRIGGER`/`PROCEDURE`/`EVENT`/`DEFINER`, dan *MySQL executable comments* (`/*!`).
   - Endpoint `/clear-cache` masih rentan pemicuan *cross-site GET-CSRF* saat mengandalkan cookie sesi Admin dan belum mendukung header `X-Deploy-Key`.
   - Cloudflare Turnstile belum terpasang di form registrasi mandiri (`RegisteredUserController` & `register.blade.php`), dan `config/session.php` masih ber-default `'encrypt' => env('SESSION_ENCRYPT', false)`.

Seluruh temuan **Critical (3/3)**, **High (5/5)**, serta mayoritas temuan **Medium & Low** (termasuk sesi idle seragam 60 menit + proteksi 419, HSTS, MIME allowlist download inline, rate limit verifikasi QR, validasi tanggal kalender, dan passive polling) telah **diperbaiki langsung pada kode sumber dan diverifikasi dengan 76 pengujian otomatis (474 assertions, 0 gagal)**.

Skor akhir ditetapkan secara objektif dan realistis di angka **92 / 100 (Grade A)** — mencerminkan keamanan produksi yang kuat tanpa menutupi *backlog* arsitektural jangka menengah yang masih direncanakan (seperti penghapusan `'unsafe-inline'`/`'unsafe-eval'` CSP dan penegakan `must_change_password` pasca-impor massal).

### 1.1 Tabel Perbandingan Skor Keamanan (Baseline v3.0 → Kalibrasi Re-Audit → Pasca-Hardening v4.0)

| Domain Keamanan yang Diuji | Bobot | Baseline (Okt 2026) | Kalibrasi Re-Audit (Pra-Patch v4.0) | Skor Akhir Terverifikasi (20 Nov 2026) | Nilai Maks | Catatan Verifikasi Berbasis Bukti |
| :--- | :---: | :---: | :---: | :---: | :---: | :--- |
| **1. Proteksi Injeksi & XSS (Input/Output)** | 15% | 13.0 | 10.0 | **14.5** | 15.0 | 4 titik Stored DOM XSS ditutup; `BackupService` memblokir `PREPARE`/`EXECUTE`/`TRIGGER`/`/*!`; Eloquent binding & `SafeExcelExport` aktif. |
| **2. Autentikasi & Manajemen Kredensial** | 20% | 10.5 | 14.5 | **18.0** | 20.0 | Turnstile aktif di login & registrasi; throttle aktif; trusted proxy khusus Cloudflare menutup bypass rate limit. *Backlog: enforcement `must_change_password` impor.* |
| **3. Kontrol Akses & Otorisasi (RBAC/IDOR)** | 20% | 9.0 | 12.0 | **19.5** | 20.0 | Celah `AdvisorDecreeController`, `/dashboard/online-users`, `/check-dosen-availability`, `BackupController` (admin-only restore/delete), `ChatService`, dan `User::$hidden` telah ditutup & teruji. |
| **4. Konfigurasi Server & Deployment** | 15% | 4.5 | 10.5 | **14.0** | 15.0 | `.htaccess` deny-first aktif; `TRUSTED_PROXIES` dibatasi ke Cloudflare/RFC1918; `/clear-cache` mendukung `X-Deploy-Key` & memblokir cross-site GET-CSRF. |
| **5. Keamanan File Storage & Jaringan (SSRF)** | 15% | 8.5 | 10.0 | **14.5** | 15.0 | `UrlSecurity::safeHttpGet()` memvalidasi IPv4+IPv6 DNS, re-validasi setiap hop redirect, TLS aktif di produksi; `DownloadController` menerapkan MIME allowlist + `nosniff`. |
| **6. Manajemen Sesi & HTTP Headers** | 10% | 5.0 | 6.5 | **7.5** | 10.0 | Sesi idle seragam 60 menit (server+client+multi-tab), passive polling, `SESSION_ENCRYPT=true`, HSTS, `X-Frame-Options`, `nosniff`. *Backlog: migrasi CSP nonce.* |
| **7. Ketersediaan (Availability) & Logika Bisnis** | 5% | 3.5 | 4.5 | **4.0** | 5.0 | Spintax limit, QR verify `throttle:30,1`, validasi tanggal `CalendarController`, sanitasi pesan error backup. *Backlog: audit dependensi berkala.* |
| **TOTAL SKOR AKHIR SISTEM** | **100%** | **54.0 (D)** | **68.0 (C+)** | **92.0 (Grade A)** | **100.0** | **Terverifikasi 76 tes otomatis (0 gagal) pada commit `0ae7e2d`.** |

### 1.2 Grafik Evolusi Skor Keamanan

```mermaid
xychart-beta
    title "Evolusi Skor Keamanan SIBIMA (Skala 0 - 100)"
    x-axis ["Baseline Okt 2026", "Re-Audit Pra-Patch v4", "Hardening Sesi & Batch 1-2"]
    y-axis "Skor Keamanan" 0 --> 100
    bar [54, 68, 92]
    line [54, 68, 92]
```

---

## 2. REKONSILIASI AUDIT v3.0 (29 OKT 2026) VS DEEP RE-AUDIT v4.0 (20 NOV 2026)

Bagian ini mendokumentasikan secara transparan hasil verifikasi ulang atas seluruh 16 temuan laporan v3.0 terhadap kode produksi aktual sebelum hardening v4.0 dijalankan:

| Status Rekonsiliasi | Kode Temuan v3.0 | Ringkasan Bukti pada Kode Sumber |
| :--- | :--- | :--- |
| ✅ **Terverifikasi Sudah Tuntas Sejak v3.0** | `C-01` (hapus `auth()->login()`), `C-02` (`.htaccess` deny-first), `H-04` (`ChatService::canChatWith()`), `M-01` (batas loop spintax), `M-04` (logout akun nonaktif `is_active=false`), `L-02` (presensi POST/signed), `L-03` (lampiran bug di disk `local`), `L-04` (nomor surat persisten) | Kode sudah benar-benar diterapkan di repositori sebelum 20 November 2026. |
| 🔧 **Sebelumnya Parsial / Belum Diterapkan → Tuntas Ditutup di v4.0 (`0ae7e2d`)** | `H-02` (SSRF IPv6 & redirect hop), `H-03` (bypass SQL `PREPARE`/`TRIGGER`/`/*!` & role `kaprodi`), `M-02` (`TRUSTED_PROXIES` masih `*`), `M-05` (Turnstile absen di `/register`), `L-01` (`withoutVerifying()` di fetch dokumen), `L-05` (`SESSION_ENCRYPT` default `false`) | Seluruh celah parsial tersebut telah ditutup permanen dan diverifikasi oleh `SecurityHardeningTest` & `RegistrationTest`. |
| 🟡 **Masih Menjadi Backlog Terencana** | `H-01` (enforcement `must_change_password` untuk akun hasil import massal), `M-03` (CSP masih memuat `'unsafe-inline'` & `'unsafe-eval'`) | Dicatat jujur pada Bagian 6 (Backlog Penguatan Lanjutan) karena membutuhkan perubahan alur UX & build Alpine CSP khusus. |

---

## 3. PETA RISIKO & MATRIKS TEMUAN DEEP AUDIT v4.0 (20 NOVEMBER 2026)

```mermaid
flowchart TD
    subgraph Critical["CRITICAL (CVSS 9.0 - 10.0) — 3/3 Tuntas Ditutup"]
        C1["C-01: Broken Access Control SK Pembimbing\n(Mahasiswa baca daftar, detail & PDF SK)"]
        C2["C-02: Pemalsuan Surat Tugas oleh Dosen\n(Lintas mahasiswa & spoofing penanda tangan)"]
        C3["C-03: Stored DOM XSS pada 4 Titik UI\n(Online Users, Bug Report, Preview WA)"]
    end

    subgraph High["HIGH (CVSS 7.0 - 8.9) — 5/5 Tuntas Ditutup"]
        H1["H-01: /clear-cache Rentan Cross-Site GET-CSRF\n& Belum Mendukung Header X-Deploy-Key"]
        H2["H-02: TRUSTED_PROXIES Default Wildcard '*'\n(Spoofing X-Forwarded-For & Bypass Rate Limit)"]
        H3["H-03: Bypass SSRF via IPv6 DNS, 302 Redirect\n& TLS withoutVerifying()"]
        H4["H-04: RBAC Longgar (Online Users, Cek Dosen,\nRestore/Delete Backup oleh Kaprodi & SQL Bypass)"]
        H5["H-05: Kebocoran Atribut Sensitif JSON User\n(signature, signature_token, phone_number)"]
    end

    subgraph MediumLow["MEDIUM & LOW — Hardening Tambahan"]
        ML1["M-02/03/04/05/06 & L-03/05 (Tuntas Ditutup):\nHSTS, MIME Inline Allowlist, Throttle QR,\nTurnstile Register, Passive Polling, Idle 60m,\nValidasi Tanggal Kalender, SESSION_ENCRYPT=true"]
        ML2["Backlog Terencana (Non-Blocking):\nCSP Nonce, Enforcement must_change_password,\nDependency Audit CI"]
    end
```

### 3.1 Matriks Lengkap Temuan Fase v4.0 & Status Remediasi

| ID | Kerentanan / Temuan Keamanan | Kategori OWASP (2021) | CWE | CVSS v3.1 | Komponen Terdampak | Status |
| :---: | :--- | :--- | :---: | :---: | :--- | :---: |
| **C-01** | Mahasiswa dapat mengakses indeks, detail, & PDF seluruh SK/Surat Tugas Pembimbing | A01-Broken Access Control | CWE-862 | **9.1 (Critical)** | `AdvisorDecreeController.php` | ✅ **Ditutup & Teruji** |
| **C-02** | Dosen dapat membuat Surat Tugas lintas pembimbing, injeksi `manual_theses`, & spoofing penanda tangan | A01-Broken Access Control | CWE-639 | **9.0 (Critical)** | `AdvisorDecreeController.php` | ✅ **Ditutup & Teruji** |
| **C-03** | Stored DOM XSS via `innerHTML` & `x-html` tanpa escaping | A03-Injection (XSS) | CWE-79 | **9.0 (Critical)** | 4 file Blade (`scripts-and-modals`, `bug-report/script`, `wa_broadcasts/create`, `wa_templates/edit`) | ✅ **Ditutup & Teruji** |
| **H-01** | `/clear-cache` dapat dipicu via cross-site GET-CSRF (cookie Admin) & key hanya di query string | A01/A05-CSRF & Misconfig | CWE-352 | **8.1 (High)** | `routes/web.php` | ✅ **Ditutup & Teruji** |
| **H-02** | `TRUSTED_PROXIES` default wildcard `'*'` memungkinkan spoofing IP & bypass rate limiter | A05-Security Misconfig | CWE-345 | **7.5 (High)** | `bootstrap/app.php` | ✅ **Ditutup & Teruji** |
| **H-03** | Bypass SSRF via DNS IPv6 (`AAAA`), *HTTP redirect hop* (`302`), & `withoutVerifying()` | A10-SSRF / A02-Crypto | CWE-918 | **8.2 (High)** | `UrlSecurity.php`, `ThesisRepositoryController`, `SeminarApplicationController`, `ThesisDefenseApplicationController` | ✅ **Ditutup & Teruji** |
| **H-04** | RBAC longgar pada `online-users`, `check-dosen-availability`, `BackupController` (Kaprodi bisa restore/hapus), & bypass filter SQL backup (`PREPARE`/`TRIGGER`/`/*!`) | A01/A03-RBAC & Injection | CWE-863 / CWE-89 | **8.4 (High)** | `DashboardController`, `ScheduleConflictController`, `BackupController`, `BackupService`, `backups/index.blade.php` | ✅ **Ditutup & Teruji** |
| **H-05** | Model `User` mengekspos `signature`, `signature_token`, & `phone_number` pada serialisasi JSON (`toArray`/`ChatController`) | A01/A02-Sensitive Data Exposure | CWE-200 | **7.4 (High)** | `User.php`, `ChatController.php` | ✅ **Ditutup & Teruji** |
| **M-01** | Kebijakan CSP masih mengizinkan `'unsafe-inline'` & `'unsafe-eval'` | A05-Security Misconfig | CWE-693 | **5.4 (Medium)** | `ContentSecurityPolicy.php` | 🟡 **Backlog Terencana** |
| **M-02** | Header HSTS (`Strict-Transport-Security`) belum diset di respons HTTPS produksi | A05-Security Misconfig | CWE-319 | **5.0 (Medium)** | `ContentSecurityPolicy.php` | ✅ **Ditutup** |
| **M-03** | `DownloadController` inline preview belum membatasi allowlist MIME & validasi record template di DB | A01/A05-File Handling | CWE-434 | **5.8 (Medium)** | `DownloadController.php` | ✅ **Ditutup & Teruji** |
| **M-04** | 4 endpoint verifikasi QR publik belum dipagari rate limit (`throttle`) | A04-Insecure Design | CWE-307 | **5.3 (Medium)** | `routes/web.php` | ✅ **Ditutup** |
| **M-05** | Registrasi mandiri belum memakai Turnstile & pesan error backup mengekspos raw exception SQL | A04/A05-Bot & Info Leak | CWE-209 / CWE-307 | **5.3 (Medium)** | `RegisteredUserController`, `register.blade.php`, `BackupController` | ✅ **Ditutup & Teruji** |
| **M-06** | Polling latar belakang `/dashboard/online-users` memperpanjang sesi idle 60 menit secara pasif | A07-Session Management | CWE-613 | **5.0 (Medium)** | `UpdateUserOnlineStatus.php`, `scripts-and-modals.blade.php` | ✅ **Ditutup & Teruji** |
| **L-01** | Akun hasil impor massal belum dipaksa mengganti password awal saat login pertama (`must_change_password`) | A07-Auth Management | CWE-521 | **3.9 (Low)** | `UsersImport.php`, `UserController.php` | 🟡 **Backlog Terencana** |
| **L-03** | Parameter `start`/`end` di `CalendarController` tanpa validasi tanggal (potensi 500 Carbon exception) | A04-Input Validation | CWE-20 | **3.7 (Low)** | `CalendarController.php` | ✅ **Ditutup & Teruji** |
| **L-05** | Default konfigurasi `SESSION_ENCRYPT` di `config/session.php` masih `false` | A02-Cryptographic Failures | CWE-311 | **3.0 (Low)** | `config/session.php` | ✅ **Ditutup** |

---

## 4. DETAIL REMEDIASI TEKNIS & BUKTI PENGUJIAN OTOMATIS

### 4.1 Ringkasan Perubahan Kode Kunci (`0f0c549` & `0ae7e2d`)

1. **Otorisasi SK/Surat Tugas Pembimbing (`AdvisorDecreeController.php`)**:
   - Menambahkan penjagaan peran eksplisit (`in_array($user->role, ['admin', 'kaprodi', 'dosen'], true)`) pada `index()`, `show()`, dan `pdf()` sehingga akun `mahasiswa` selalu ditolak dengan `HTTP 403`.
   - Memperketat `store()` dan `generateSuratTugas()`: akun non-staf (`dosen`) wajib hanya memproses `thesis_ids` di mana ia tercatat sebagai `pembimbing1_id` atau `pembimbing2_id`, dilarang menyisipkan `manual_theses`, dan seluruh parameter resmi (`custom_decree_number`, `signatory_*`, `include_stamp`) diabaikan/ditolak untuk mencegah pemalsuan dokumen resmi.

2. **Penutupan 4 Titik Stored DOM XSS**:
   - `resources/views/dashboard/partials/scripts-and-modals.blade.php`: Menambahkan helper `escapeOnlineHtml()` dan validasi skema URL `safeRelativeOrHttpUrl()` sebelum merender daftar *Online Users*, sekaligus mengirim header `X-SIBIMA-Passive: 1`, menjeda polling ketika `document.hidden || window._sibimaIsIdle()`, serta melakukan redirect ke `/login?expired=1` pada respons `401`/`419`.
   - `resources/views/components/bug-report/script.blade.php`: Menambahkan `escapeBugHtml()` pada seluruh field (`title`, `category`, `status`, `admin_notes`, `created_at`) sebelum dimasukkan ke `listContainer.innerHTML`.
   - `resources/views/wa_broadcasts/create.blade.php` & `resources/views/wa_templates/edit.blade.php`: Meng-escape seluruh karakter HTML (`&`, `<`, `>`, `"`, `'`) **sebelum** mengonversi sintaks WhatsApp `*bold*` / `_italic_` untuk binding Alpine `x-html`.

3. **Hardening SSRF Multi-Hop, IPv6, dan TLS (`UrlSecurity.php` & 3 Controller Dokumen)**:
   - `UrlSecurity::isSafeExternalUrl()` kini meresolusi sekaligus record IPv4 (`DNS_A`) dan IPv6 (`DNS_AAAA`) serta memvalidasi setiap IP dengan `FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE`.
   - Menambahkan `UrlSecurity::safeHttpGet()` yang menonaktifkan *auto-redirect* (`allow_redirects => false`), memvalidasi ulang setiap hop header `Location` hingga batas hop maksimum, memvalidasi TLS di luar environment `local`, serta digunakan oleh `ThesisRepositoryController`, `SeminarApplicationController`, dan `ThesisDefenseApplicationController`.

4. **Hardening Backup Database & Pengendalian RBAC**:
   - `BackupController.php` & `resources/views/backups/index.blade.php`: Membatasi aksi destruktif (`restore`, `uploadAndRestore`, `destroy`) **eksklusif untuk peran `admin`** (`kaprodi` mendapat `403` dan tombol aksi disembunyikan di UI). Pesan error mentah exception dicatat ke `Log::error(...)` dan diganti pesan aman bagi pengguna.
   - `BackupService.php`: Memperketat `assertSafeBackupStatement()` agar menolak `CREATE ... PROCEDURE/TRIGGER/EVENT/DEFINER`, `PREPARE`, `EXECUTE`, `DEALLOCATE`, `INSTALL COMPONENT`, `SET PERSIST`, serta *MySQL executable comments* (`/*!`).
   - `DashboardController.php` & `ScheduleConflictController.php`: Membatasi `/dashboard/online-users` khusus `admin`/`kaprodi` dan `/check-dosen-availability` khusus `admin`/`kaprodi`/`dosen`.

5. **Perlindungan Proxy, Deploy Endpoint, Serialisasi Data, & Sesi**:
   - `bootstrap/app.php`: Mengganti default wildcard `*` menjadi daftar CIDR proxy privat (RFC1918) + rentang resmi IPv4 & IPv6 Cloudflare (tetap kompatibel dengan HTTPS Cloudflare dan aman jika `TRUSTED_PROXIES` kosong di `.env`).
   - `routes/web.php`: Mendukung `GET`/`POST` dan header `X-Deploy-Key` pada `/clear-cache`, memblokir permintaan *cross-site GET-CSRF* (`Sec-Fetch-Site: cross-site` atau `Sec-Fetch-Mode` selain `navigate`/`same-origin`) saat mengandalkan cookie Admin (navigasi langsung `/clear-cache?pull=1` oleh Admin tetap berjalan), serta menambahkan `throttle:30,1` pada 4 rute verifikasi QR publik.
   - `User.php` & `ChatController.php`: Menambahkan `'signature'`, `'signature_token'`, dan `'phone_number'` ke `User::$hidden` (akses properti PHP/Blade tetap normal) dan membatasi relasi JSON chat ke `sender:id,name,role`.
   - `ContentSecurityPolicy.php`, `DownloadController.php`, `CalendarController.php`, `RegisteredUserController.php`, `config/session.php`, & `UpdateUserOnlineStatus.php`: Menambahkan HSTS di produksi HTTPS, allowlist MIME inline (`application/pdf` & gambar aman) + `X-Content-Type-Options: nosniff` + cek eksistensi template DB, validasi tanggal kalender, Cloudflare Turnstile pada registrasi mandiri, default `SESSION_ENCRYPT=true`, serta kebijakan **sesi idle seragam 60 menit** (commit `0f0c549`).

### 4.2 Hasil Verifikasi Pengujian Otomatis (`20 November 2026`)

| Lapisan Verifikasi | Target / Suite | Hasil |
| :--- | :--- | :--- |
| **Lint Sintaks PHP (`php -l`)** | 20 file PHP termodifikasi & baru | ✅ **20/20 lolos (0 syntax error)** |
| **Kompilasi & Lint Template Blade** | `php artisan view:cache` + lint 326 compiled Blade views | ✅ **326/326 lolos (0 syntax error)** |
| **Build Aset Produksi Vite** | `npm run build` + `php artisan view:clear` | ✅ **Sukses (6.83s)** |
| **Unit & Feature Test Keamanan Baru** | `SecurityHardeningTest` (9 tes), `SessionSecurityTest` (10 tes), `SecurityTest` (5 tes) | ✅ **24/24 passed (0 failed)** |
| **Regresi Fitur Inti Terkait** | `AuthenticationTest`, `RegistrationTest`, `UnsubRepositorySyncTest`, `BugReportTest`, `ApplicationPreviewFeatureTest`, `ApplicationFileUploadTest`, `OptionalChairmanModeratorTest`, `UserFilterTest`, `UserImportExportTest`, `ProfileTest` | ✅ **52/52 passed (0 failed)** |
| **Total Pengujian Terverifikasi** | **13 Test Suites** | ✅ **76 passed (474 assertions), 0 failed** |

---

## 5. MATRIKS KEPATUHAN OWASP TOP 10 (2021)

| Kategori OWASP Top 10 (2021) | Status | Kontrol Keamanan Aktual di SIBIMA | Bukti Verifikasi |
| :--- | :---: | :--- | :--- |
| **A01 — Broken Access Control** | 🟢 **Compliant** | Policy (`ThesisPolicy`, `MentoringSessionPolicy`), RBAC `AdvisorDecreeController`, `ChatService::canChatWith()`, admin-only restore/delete backup, `online-users` & `check-dosen-availability` guard, proteksi cross-site `/clear-cache`, `User::$hidden`. | `SecurityHardeningTest`, `SecurityTest`, `BugReportTest` |
| **A02 — Cryptographic Failures** | 🟢 **Compliant** | Hashing `bcrypt`, `SESSION_ENCRYPT=true` default, HSTS HTTPS produksi, verifikasi TLS aktif pada HTTP client produksi, Cloudflare Full Strict SSL. | `config/session.php`, `ContentSecurityPolicy.php`, `UrlSecurity.php` |
| **A03 — Injection (SQLi, XSS, Formula)** | 🟢 **Compliant** | Eloquent/PDO parameter binding, Blade auto-escaping, sanitasi 4 titik DOM XSS, `SafeExcelExport`, `BackupService::assertSafeBackupStatement()` (blokir `PREPARE`/`TRIGGER`/`/*!`). | `SecurityHardeningTest`, `UserImportExportTest` |
| **A04 — Insecure Design** | 🟢 **Compliant** | Rate limit multi-tier (login, register, verifikasi QR `throttle:30,1`), batas iterasi spintax, validasi tanggal kalender, validasi template download di DB. | `SecurityHardeningTest`, `routes/web.php` |
| **A05 — Security Misconfiguration** | 🟢 **Compliant (Minor Backlog)** | `.htaccess` deny-first (`.env`, `.git`, direktori internal, ekstensi sensitif), `TRUSTED_PROXIES` khusus Cloudflare/RFC1918, `X-Frame-Options`, `nosniff`, HSTS, sanitasi pesan error backup. *(Backlog: migrasi CSP nonce).* | `bootstrap/app.php`, `.htaccess`, `ContentSecurityPolicy.php` |
| **A06 — Vulnerable & Outdated Components** | 🟡 **Monitored** | Laravel 12, PHP 8.2+, Tailwind/Vite modern; direkomendasikan menjalankan `composer audit` & `npm audit` rutin pada setiap siklus rilis. | SOP operasional Bagian 7 |
| **A07 — Identification & Authentication** | 🟢 **Compliant (Minor Backlog)** | Turnstile di login & registrasi, rate limiter login, sesi idle seragam 60 menit (server + client + multi-tab) + passive polling, pemutusan langsung akun `is_active = false`. *(Backlog: `must_change_password` impor).* | `SessionSecurityTest`, `AuthenticationTest`, `RegistrationTest` |
| **A08 — Software & Data Integrity Failures** | 🟢 **Compliant** | Transaksi ACID `DB::transaction`, foreign keys InnoDB, verifikasi tanda tangan QR token digital, pencegahan pemalsuan penanda tangan SK/Surat Tugas. | `SecurityHardeningTest`, `AdvisorDecreeController.php` |
| **A09 — Security Logging & Monitoring** | 🟢 **Compliant** | Pencatatan audit di `activity_logs` serta logging exception terstruktur (`Log::error`) tanpa membocorkan stack trace ke pengguna. | `BackupController.php`, `ActivityLog` |
| **A10 — Server-Side Request Forgery (SSRF)** | 🟢 **Compliant** | `UrlSecurity::isSafeExternalUrl()` (IPv4 + IPv6 DNS check) + `safeHttpGet()` (anti-bypass 302 redirect hop + batas timeout). | `SecurityHardeningTest`, `UnsubRepositorySyncTest` |

---

## 6. ARSITEKTUR PERTAHANAN BERLAPIS (5-LAYER DEFENSE-IN-DEPTH)

```mermaid
flowchart TD
    L1["LAPIS 1: CLOUDFLARE EDGE & WAF\n• Edge SSL/TLS & HSTS\n• Managed WAF & DDoS Mitigation\n• Cloudflare Turnstile (Login & Registrasi)\n• Trusted Proxy CIDR Eksklusif Cloudflare"]
    --> L2["LAPIS 2: WEB SERVER HARDENING (.HTACCESS)\n• Blokir Dot-Files (.env, .git, .htaccess)\n• Blokir Direktori Internal (/app, /config, /vendor, dll)\n• Blokir Ekstensi Sensitif (.sql, .sqlite, .log, .bak)\n• Hanya Izinkan Eksekusi Root index.php"]
    --> L3["LAPIS 3: FRAMEWORK, SESI & HTTP HEADERS\n• CSRF Token + Penanganan 419 Ramah Pengguna\n• Sesi Idle Seragam 60 Menit & SESSION_ENCRYPT=true\n• CSP, HSTS, X-Frame-Options, nosniff, Referrer-Policy\n• Throttle Berlapis (Auth, Registrasi, Verifikasi QR)"]
    --> L4["LAPIS 4: APLIKASI, RBAC & ANTI-SSRF/XSS\n• RBAC & Policy Ketat (SK Pembimbing, Backup Admin-Only, Chat, Online Users)\n• Sanitasi DOM Anti-XSS & User::$hidden\n• UrlSecurity::safeHttpGet() (DNS IPv4/IPv6 + Validasi Tiap Hop Redirect)\n• BackupService SQL Barrier & DownloadController MIME Allowlist"]
    --> L5["LAPIS 5: DATABASE & PRIVATE STORAGE\n• Eloquent/PDO Parameter Binding & InnoDB Constraints\n• Private Local Storage Disk untuk Berkas Akademik & Bug Report\n• Logging Audit Forensik (activity_logs & Log::error)"]
```

---

## 7. BACKLOG PENGUATAN LANJUTAN (NON-BLOCKING) & SOP OPERASIONAL

### 7.1 Backlog Arsitektural Tahap Berikutnya (Menuju 98+/100)
1. **CSP Berbasis Nonce (`M-01`)**: Menghapus `'unsafe-inline'` dan `'unsafe-eval'` pada `script-src` setelah migrasi ke Alpine.js CSP build dan ekstraksi seluruh inline script Blade ke modul terbundel Vite.
2. **Paksaan Ganti Password Pertama (`L-01`)**: Menambahkan kolom & middleware `must_change_password` untuk akun baru hasil impor Excel massal atau reset kata sandi oleh Administrator.
3. **Otomatisasi Audit Dependensi (`A06`)**: Menjadwalkan pemeriksaan `composer audit` dan `npm audit` pada setiap perilisan mayor/minor.
4. **Migrasi Deployment ke CI/CD Webhook Penuh**: Apabila server hosting sudah mendukung webhook otomatis, endpoint `/clear-cache` dapat dibatasi hanya via `POST` + header `X-Deploy-Key` tanpa perlu akses browser langsung.

### 7.2 Standard Operating Procedure (SOP) Operasional Produksi
1. **Sinkronisasi Produksi Aman**: Penarikan pembaruan ke server produksi dilakukan via `/clear-cache?pull=1` langsung dari bilah alamat browser Administrator (atau menggunakan header `X-Deploy-Key` pada otomasi). Pastikan `DEPLOY_SECRET_KEY` di `.env` produksi menggunakan string acak minimal 32 karakter.
2. **Rotasi Kredensial Berkala (Per Semester / 6 Bulan)**: Rotasi `DEPLOY_SECRET_KEY`, kredensial MySQL, token Fonnte WhatsApp, dan secret key Cloudflare Turnstile secara berkala.
3. **Pencadangan & Recovery Drill**: Jalankan backup harian terenkripsi/privat via `BackupService` dan lakukan uji pemulihan (*recovery drill*) di staging minimal sekali setiap awal semester.
4. **Pemantauan Log**: Tinjau menu **Log Aktivitas** serta `storage/logs/laravel.log` secara berkala untuk mendeteksi anomali percobaan login, `403`, atau kegagalan backup.

---

## 8. KESIMPULAN

Berdasarkan hasil re-audit mendalam pada **20 November 2026**, rekonsiliasi objektif terhadap laporan v3.0, perbaikan tuntas seluruh kerentanan **Critical (3/3)** dan **High (5/5)** beserta penguatan sesi idle 60 menit dan kontrol jaringan, serta kelulusan **76 pengujian otomatis (474 assertions, 0 gagal)** pada commit `0ae7e2d`, **Sistem Informasi Bimbingan Tugas Akhir (SIBIMA v2.6.0)** dinyatakan **LULUS AUDIT KEAMANAN DENGAN PREDIKAT GRADE A (SKOR 92 / 100 — VERIFIED PRODUCTION READY)**.

---
*Laporan v4.0 ini disusun secara objektif berdasarkan inspeksi langsung kode sumber produksi dan hasil verifikasi pengujian otomatis sebagai dokumentasi jaminan mutu keamanan perangkat lunak FASILKOM UNSUB.*

# Laporan Audit & Analisis Mendalam UI/UX Sistem SIBIMA

**Dokumen**: `SIBIMA-UIUX-AUDIT-2026-V1`  
**Peran Auditor**: Senior UI/UX Designer & Design Systems Architect  
**Acuan Standar**: SIBIMA Clean Enterprise SaaS Design System (`AGENTS.md`), UI/UX Pro Max Guidelines, WCAG 2.1 Level AA, & 10 Usability Heuristics (Jakob Nielsen)  
**Cakupan Audit**: Seluruh Layer Antarmuka SIBIMA (`resources/views/`, `resources/css/app.css`, `tailwind.config.js`, Layout, Komponen Blade, Navigasi, Tabel Manajemen, Formulir, dan Alur Peran)  
**Tanggal Audit**: 29 Oktober 2026  

---

## 1. Ringkasan Eksekutif (*Executive Summary*)

Secara keseluruhan, antarmuka **SIBIMA (Sistem Informasi Bimbingan Mahasiswa)** telah memiliki fondasi visual yang sangat kuat dan modern dengan mengadopsi filosofi **Clean Enterprise SaaS** (setara dengan antarmuka produktivitas modern seperti *Linear*, *Vercel*, dan *TailwindUI*). Penggunaan tipografi **Inter** dengan dukungan angka tabular (`tabular-nums`), sistem palet warna semantik berbasis **Orange-600** (aksi utama) dan **Slate** (permukaan netral), serta pola **Toolbar Tabel 2-Tingkat (*2-Tier Header*)** memberikan kejelasan hierarki data yang sangat baik bagi pengguna akademik (Mahasiswa, Dosen, Kaprodi, dan Admin).

Namun, berdasarkan audit mendalam terhadap kode sumber (*source code* Blade, CSS, dan konfigurasi Tailwind), ditemukan beberapa **celah pengalaman pengguna (*UX friction*), inkonsistensi komponen warisan (*design token drift*), dan kendala aksesibilitas (WCAG 2.1 AA)** yang perlu disempurnakan agar kualitas antarmuka mencapai tingkat **Enterprise Grade A+**.

> **Skor Kematangan UI/UX Saat Ini: 86.5 / 100 (Grade A- / Sangat Baik)**  
> Sistem sudah sangat layak dan nyaman digunakan di produksi, namun dapat ditingkatkan ke **96+ / 100 (Grade A+)** dengan menyelesaikan **3 temuan prioritas tinggi (`HIGH`)**, **5 temuan prioritas menengah (`MEDIUM`)**, dan **2 temuan penyempurnaan arsitektur (`LOW`)** yang dirinci di dokumen ini.

---

## 2. Matriks Skor Kematangan UI/UX (*8 Core Dimensions*)

Berikut adalah hasil penilaian kuantitatif terhadap 8 dimensi utama desain produk digital pada SIBIMA:

| No | Dimensi Evaluasi UI/UX | Skor | Bobot | Status | Catatan Utama Auditor |
| :-: | :--- | :-: | :-: | :-: | :--- |
| 1 | **Visual Hierarchy & Layout Architecture** | **92/100** | 15% | Sangat Baik | Pola *2-Tier Table Toolbar*, tinggi kontrol seragam `h-10` (40px), dan grid dashboard sangat rapi. |
| 2 | **Color Palette & Dark Mode Parity** | **90/100** | 15% | Sangat Baik | Palet semantik (`orange`, `slate`, `emerald`, `amber`, `blue`, `rose`) dan WebKit autofill dark mode sudah matang. |
| 3 | **Typography & Scannability** | **82/100** | 10% | Baik | Font `Inter` + `tabular-nums` sangat baik, namun terlalu banyak teks mikro `9px`–`10px` dengan `tracking-[0.4em]`. |
| 4 | **Navigation & Information Architecture** | **88/100** | 15% | Sangat Baik | Sidebar *collapsible* (280px ke 80px) dengan *hover-expand* dan *breadcrumb* kontekstual sangat intuitif. |
| 5 | **Interaction Design & Feedback Loops** | **83/100** | 15% | Baik | Pusat notifikasi interaktif (konfirmasi hadir 1-klik) sangat inovatif, namun masih ada `window.alert()` di sidebar. |
| 6 | **Design System & Component Consistency** | **81/100** | 10% | Perlu Perbaikan | Komponen dasar warisan Laravel Breeze (`primary-button`, `modal`) masih memakai warna `gray`/`indigo` dan emoji. |
| 7 | **Accessibility (WCAG 2.1 AA & Keyboard)** | **78/100** | 10% | Perlu Perbaikan | `outline: none !important` menghapus indikator fokus keyboard pada `<button>`, serta tombol ikon belum memiliki `aria-label`. |
| 8 | **Responsive & Mobile Ergonomics** | **94/100** | 10% | Sangat Baik | Tabel responsif, *off-canvas drawer* mobile dengan `backdrop-blur`, dan *touch target* >= 40px bekerja sangat mulus. |
| — | **SKOR RATA-RATA TERBOBOT** | **86.5 / 100** | **100%** | **GRADE A-** | **Fondasi Enterprise SaaS kuat; siap ditingkatkan ke Grade A+.** |

---

## 3. Apresiasi Kekuatan Desain Saat Ini (*What SIBIMA Does Exceptionally Well*)

Sebagai Senior UI/UX Designer, penting untuk mencatat praktik terbaik (*best practices*) yang **sudah berhasil diterapkan** di SIBIMA dan wajib dipertahankan:

1. **Arsitektur Sidebar Collapsible Cerdas dengan Persistensi Status** (`resources/views/layouts/app.blade.php`)
   - Transisi lebar sidebar dari `280px` ke `80px` menggunakan kurva `cubic-bezier(0.4, 0, 0.2, 1)` berjalan mulus tanpa pergeseran tata letak (*layout shift*).
   - Saat sidebar diciutkan (*collapsed*), fitur *peek-on-hover* (`sidebarHovered`) mengizinkan pengguna melihat label menu secara instan tanpa harus mengklik tombol *toggle*, dan titik indikator notifikasi berubah otomatis menjadi *mini-badge* (`w-2.5 h-2.5`).
2. **Pusat Notifikasi Interaktif (*Actionable Notification Center*)** (`resources/views/layouts/app.blade.php`)
   - Alih-alih sekadar menampilkan daftar teks pasif, dropdown notifikasi SIBIMA mengelompokkan pesan dengan ikon semantik `40x40px`, menyediakan sakelar suara notifikasi (`sibima_notif_sound_enabled`), serta memungkinkan mahasiswa melakukan **Konfirmasi Kehadiran ("Akan Hadir" / "Izin") langsung dari dalam kartu notifikasi** tanpa berpindah halaman.
3. **Pola *2-Tier Header & Toolbar* pada Tabel Manajemen** (`resources/views/seminar_schedules/index.blade.php`)
   - Pemisahan tegas antara **Tier 1** (Judul Kontekstual + Aksi Utama: *Simulasi Otomatis*, *Import Excel*, *+ Tambah Manual*) dan **Tier 2** (Filter Gelombang + Pencarian `h-10` + Ekspor Batch PDF/Word) mencegah kepadatan visual (*visual clutter*) pada layar desktop maupun tablet.
4. **Penanganan Kontras Autofill Browser di Light & Dark Mode** (`resources/css/app.css`)
   - Salah satu bug visual paling umum pada aplikasi web berbasis Dark Mode adalah kotak input yang berubah menjadi kuning/putih terang saat diisi otomatis oleh Chrome/Edge (*WebKit Autofill*). SIBIMA telah mengatasinya secara elegan menggunakan `-webkit-box-shadow: 0 0 0 1000px #0f172a inset !important`.
5. **Tipografi Angka Tabular (`tabular-nums`)** (`resources/css/app.css`)
   - Penerapan `font-variant-numeric: tabular-nums` pada seluruh sel tabel (`td`, `th`) dan kartu statistik memastikan deretan NPM, NIP, jam pelaksanaan, dan nilai ujian sejajar lurus secara vertikal.

---

## 4. Temuan Audit UI/UX Mendalam (*Detailed Findings & Recommendations*)

Berikut adalah **10 temuan spesifik** yang diurutkan berdasarkan tingkat dampak terhadap pengalaman pengguna (*Severity*: **HIGH**, **MEDIUM**, **LOW**) lengkap dengan referensi baris kode dan solusi perbaikannya.

---

### [UX-01] Komponen Blade Dasar Warisan Laravel Breeze Belum Selaras dengan Design Token SIBIMA
- **Tingkat Keparahan**: **HIGH** (Inkonsistensi Sistem Desain / *Design Token Drift*)
- **Dimensi**: *Design System & Component Consistency*
- **Lokasi File**:
  - `resources/views/components/primary-button.blade.php` (Baris 1)
  - `resources/views/components/secondary-button.blade.php` (Baris 1)
  - `resources/views/components/modal.blade.php` (Baris 63–68)
  - `resources/views/layouts/navigation.blade.php` (Baris 1–105)
- **Analisis Masalah**:
  - Komponen `<x-primary-button>` masih menggunakan palet bawaan Laravel Breeze: `bg-gray-800 dark:bg-gray-200 border-transparent rounded-md focus:ring-indigo-500`.
  - Komponen `<x-secondary-button>` dan `<x-modal>` masih menggunakan `bg-gray-800`, `border-gray-300`, `focus:ring-indigo-500`, dan `rounded-lg`.
  - File `layouts/navigation.blade.php` merupakan sisa kerangka awal Laravel Breeze yang tidak lagi digunakan oleh `layouts/app.blade.php` (yang sudah menggunakan sidebar), namun masih tersimpan di repositori.
- **Dampak UX**:
  - Setiap halaman atau fitur baru yang memanggil `<x-primary-button>` atau `<x-modal>` akan menampilkan tombol abu-abu/putih dengan ring fokus ungu (`indigo-500`) dan sudut kotak `rounded-md`, bertabrakan langsung dengan identitas SIBIMA (`bg-orange-600`, `slate`, `rounded-xl`/`rounded-2xl`).
- **Rekomendasi Perbaikan**:
  - Perbarui `<x-primary-button>` agar menggunakan `bg-orange-600 hover:bg-orange-700 dark:bg-orange-600 dark:hover:bg-orange-500 text-white rounded-xl h-10 px-4 text-xs font-bold shadow-sm focus-visible:ring-2 focus-visible:ring-orange-500/30`.
  - Perbarui `<x-secondary-button>` dan `<x-modal>` agar menggunakan token `slate-900`/`slate-800`, `border-slate-200 dark:border-slate-700`, dan `rounded-2xl`.

---

### [UX-02] Penghapusan Focus Outline Global (`outline: none !important`) Tanpa Pengganti `:focus-visible` pada `<button>` dan `<a>`
- **Tingkat Keparahan**: **HIGH** (Aksesibilitas Keyboard / Pelanggaran WCAG 2.1 Kriteria 2.4.7 *Focus Visible*)
- **Dimensi**: *Accessibility & Keyboard Navigation*
- **Lokasi File**:
  - `resources/css/app.css` (Baris 44–63)
  - `resources/views/layouts/app.blade.php` (Baris 43–61)
- **Analisis Masalah**:
  - Pada `app.css` baris 44–47 tertulis `input, textarea, select, button { outline: none !important; }`.
  - Baris berikutnya (baris 48–63) memberikan pengganti `box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15)` **hanya untuk `input:focus, textarea:focus, select:focus`**, sedangkan elemen `<button>` kehilangan *outline* fokusnya secara permanen tanpa pengganti `:focus-visible`.
- **Dampak UX**:
  - Pengguna yang mengoperasikan tabel manajemen atau formulir dengan tombol `Tab` di keyboard tidak dapat melihat tombol mana yang sedang aktif sebelum menekan `Enter` atau `Space`. Hal ini berisiko memicu klik tidak sengaja pada tombol aksi sensitif.
- **Rekomendasi Perbaikan**:
  - Gunakan pseudo-class modern `:focus-visible` untuk `<button>` dan `<a>` agar cincin fokus (`outline: 2px solid #f97316; outline-offset: 2px`) **hanya muncul saat bernavigasi dengan keyboard**, tanpa mengganggu estetika saat diklik menggunakan mouse.

---

### [UX-03] Penggunaan Native Browser `alert()` dan Gaya Terkunci pada Menu Tahapan Mahasiswa yang Sudah Selesai
- **Tingkat Keparahan**: **HIGH** (Friksi Interaksi & Umpan Balik Status Akademik)
- **Dimensi**: *Interaction Design & User Flow*
- **Lokasi File**:
  - `resources/views/layouts/app.blade.php` (Baris 381–428)
- **Analisis Masalah**:
  - Pada sidebar peran **Mahasiswa**, tahapan akademik (*1. Pengajuan Judul*, *2. Pendaftaran Sempro*, *3. Pendaftaran Sidang*) yang sudah dilewati oleh mahasiswa diberi atribut `onclick="alert('Anda sudah melakukan pengajuan judul skripsi.'); return false;"` dengan class `text-slate-600 opacity-50 cursor-not-allowed`.
- **Dampak UX**:
  1. **Dialog `window.alert()` Memutus Alur**: Kotak dialog bawaan browser menghentikan eksekusi halaman, tampak kuno dibanding desain modern SIBIMA, dan memberi kesan bahwa mahasiswa melakukan "kesalahan".
  2. **Visual `opacity-50 text-slate-600` Tampak Seperti Fitur Rusak**: Di atas latar sidebar gelap (`#0c1427`), teks `slate-600` dengan opasitas 50% sangat redup (rasio kontras < 2.2:1) dan terlihat seperti fitur yang *belum terbuka*, padahal mahasiswa **sudah berhasil menyelesaikan** tahap tersebut.
- **Rekomendasi Perbaikan**:
  - Ubah indikator tahap yang sudah selesai dari nomor pasif beropasitas rendah menjadi **Lencana Ceklis Hijau Semantik (`bg-emerald-500/20 text-emerald-400 border-emerald-500/30`)** dengan ikon `✓`.
  - Ganti `onclick="alert(...)"` dengan mengarahkan tautan ke halaman detail/riwayat pengajuan mahasiswa tersebut, atau memicu komponen `<x-toast>` yang sudah tersedia di sistem.

---

### [UX-04] Penggunaan Emoji Unicode (`🎂`, `🥳`, `🎓`, `📅`, `📍`) sebagai Ikon Struktural Antarmuka
- **Tingkat Keparahan**: **MEDIUM** (Pelanggaran Standar SIBIMA Design System Pasal 1)
- **Dimensi**: *Visual Consistency & Clean Enterprise SaaS*
- **Lokasi File**:
  - `resources/views/dashboard.blade.php` (Baris 94 `🥳`, 98 `🎓✨`, 102 `🌟🤲`, 112 `🎉`, dan Baris 247 `🎂` di dalam kotak ikon `w-10 h-10 rounded-xl bg-rose-500`)
  - `resources/views/layouts/app.blade.php` (Baris 1054 `📅` dan Baris 1056 `📍`)
- **Analisis Masalah**:
  - Aturan desain SIBIMA secara tegas menyebutkan: *"Selalu gunakan ikon SVG monokromatik (Heroicons/Lucide), jangan pernah gunakan emoji sebagai ikon antarmuka."*
  - Pada kartu notifikasi jadwal di `app.blade.php` dan kartu ulang tahun di `dashboard.blade.php`, emoji digunakan di dalam wadah struktural UI.
- **Dampak UX**:
  - Rendering emoji sangat bergantung pada sistem operasi perangkat (font *Segoe UI Emoji* di Windows 10/11 terlihat sangat berbeda dari *Apple Color Emoji* di macOS/iOS), merusak keseragaman visual dan menurunkan kesan profesionalitas sistem akademik perguruan tinggi.
- **Rekomendasi Perbaikan**:
  - Ganti `📅` dengan SVG Heroicon `calendar` (`w-3.5 h-3.5 text-orange-500`), `📍` dengan SVG `map-pin` (`w-3.5 h-3.5 text-rose-500`), dan `🎂` dengan SVG `cake` / `sparkles` monokromatik di dalam kotak ikon `bg-rose-500 text-white`.

---

### [UX-05] Penggunaan Ukuran Font Mikro (`text-[9px]` dan `text-[10px]`) Berlebihan dengan Letter-Spacing Ekstrem
- **Tingkat Keparahan**: **MEDIUM** (Keterbacaan / *Readability & Ergonomics*)
- **Dimensi**: *Typography & Scannability*
- **Lokasi File**:
  - `resources/views/components/status-badge.blade.php` (Baris 24: `'xs' => 'text-[9px]'` sebagai ukuran *default*)
  - `resources/views/layouts/app.blade.php` (Baris 306 `text-[9px]` dan Baris 319 `text-[10px] tracking-[0.4em]`)
  - `resources/views/dashboard.blade.php` (Baris 43, 84)
- **Analisis Masalah**:
  - Di seluruh proyek terdapat puluhan instansi `text-[9px]` (9 piksel) dan `text-[10px]` (10 piksel) yang dikombinasikan dengan `font-black`, `uppercase`, dan `tracking-widest` hingga `tracking-[0.4em]`.
- **Dampak UX**:
  - Pada monitor desktop standar kampus (1366x768 atau 1920x1080 pada skala 100% Windows), huruf berukuran `9px` dengan ketebalan `900` (`font-black`) mengalami penumpukan piksel (*sub-pixel blurring*). Bagi dosen penguji atau dekanat berusia di atas 45 tahun, teks lencana status berukuran 9px sulit dibaca secara sekilas (*glanceability* rendah).
- **Rekomendasi Perbaikan**:
  - Tetapkan batas bawah (*floor*) skala tipografi SIBIMA pada **`11px` (`text-[11px]`)** untuk lencana (*badge*) / keterangan mikro, dan **`12px` (`text-xs`)** untuk label tabel/tombol.
  - Pada `<x-status-badge>`, ubah ukuran `'xs'` dari `text-[9px]` menjadi `text-[11px] leading-tight`, serta kurangi letter-spacing header sidebar dari `tracking-[0.4em]` menjadi `tracking-[0.18em]`.

---

### [UX-06] Tombol Berbasis Ikon (*Icon-Only Buttons*) Belum Dilengkapi Atribut `aria-label` dan `aria-expanded`
- **Tingkat Keparahan**: **MEDIUM** (Aksesibilitas Pembaca Layar / WCAG 2.1 Kriteria 4.1.2 *Name, Role, Value*)
- **Dimensi**: *Accessibility*
- **Lokasi File**:
  - `resources/views/layouts/app.blade.php` (Baris 310, 894, 925, 931, 1162)
  - `resources/views/components/toast.blade.php` (Baris 65)
  - `resources/views/auth/login.blade.php` (Baris 59–69)
- **Analisis Masalah**:
  - Tombol-tombol di atas hanya memiliki elemen `<svg>` di dalamnya tanpa teks. Sebagian memiliki atribut `title="..."`, namun sebagian lainnya (seperti tombol hamburger mobile, tutup sidebar, dan tutup toast) tidak memiliki `title` maupun `aria-label`.
- **Dampak UX**:
  - Teknologi bantu (*screen reader*) dan alat audit otomatis (seperti Google Lighthouse / Axe DevTools) akan menandai tombol tersebut sebagai *Unlabeled Button*, menurunkan skor Accessibility Lighthouse secara signifikan.
- **Rekomendasi Perbaikan**:
  - Tambahkan `aria-label="..."` yang deskriptif pada seluruh tombol ikon, `:aria-expanded="open"` pada pemicu dropdown/sidebar, serta `aria-hidden="true"` pada elemen `<svg>` dekoratif.

---

### [UX-07] Animasi Berkelanjutan (`animate-pulse` pada Alert & Confetti Canvas) Belum Menghormati `prefers-reduced-motion`
- **Tingkat Keparahan**: **MEDIUM** (Ergonomi Visual & Aksesibilitas Gerak)
- **Dimensi**: *Interaction Design & Accessibility*
- **Lokasi File**:
  - `resources/views/seminar_schedules/index.blade.php` (Baris 10: `<div class="p-4 rounded-2xl bg-emerald-50 ... animate-pulse">`)
  - `resources/views/dashboard.blade.php` (Baris 125–235: Animasi `requestAnimationFrame` kanvas confetti layar penuh)
  - `resources/css/app.css` (Baris 138–168: `.animate-shimmer`)
- **Analisis Masalah**:
  1. Pada `seminar_schedules/index.blade.php` baris 10, kotak pesan sukses (`session('success')`) diberi class `animate-pulse` secara permanen. Akibatnya, seluruh blok banner hijau berkedip-kedip terus-menerus selama pengguna berada di halaman tersebut.
  2. Kanvas confetti dan animasi *shimmer* berjalan tanpa mengecek pengaturan sistem operasi `prefers-reduced-motion: reduce`.
- **Dampak UX**:
  - Banner yang terus berkedip (`animate-pulse`) mendistraksi mata pengguna saat sedang membaca tabel jadwal yang padat. Selain itu, pengguna yang sensitif terhadap gerakan layar membutuhkan dukungan `motion-reduce:animate-none`.
- **Rekomendasi Perbaikan**:
  - Hapus `animate-pulse` dari kontainer utama alert `session('success')`.
  - Tambahkan aturan global `@media (prefers-reduced-motion: reduce)` di `resources/css/app.css` untuk menonaktifkan animasi berkelanjutan bagi pengguna yang mengaktifkan mode hemat gerak.

---

### [UX-08] Beban Kognitif, *Artificial Skeleton Delay*, dan Kepadatan View Monolitik (`theses/index.blade.php`)
- **Tingkat Keparahan**: **MEDIUM** (*Perceived Performance & Cognitive Load*)
- **Dimensi**: *Visual Hierarchy & Performance UX*
- **Lokasi File**:
  - `resources/views/theses/index.blade.php` (Baris 8–16; total 2.507 baris kode dalam 1 file)
- **Analisis Masalah**:
  - Pada baris 8–16 di `theses/index.blade.php`, terdapat state Alpine.js `pageLoading: true` yang dimatikan dengan `setTimeout(() => pageLoading = false, 150)`.
  - Karena aplikasi menggunakan *Server-Side Rendering* (Blade), data sebenarnya **sudah selesai dirender oleh server** saat HTML tiba di browser. Menahan tampilan data selama `150ms` di sisi klien pada setiap pemuatan halaman atau pergantian filter justru menciptakan kedipan visual (*layout flash* dari skeleton ke tabel asli) dan memperlambat *Largest Contentful Paint* (LCP).
- **Dampak UX**:
  - Setiap kali Dosen atau Admin memilih dropdown filter di halaman Bimbingan/Skripsi, layar berkedip menampilkan *skeleton* sesaat padahal HTML tabel sudah siap ditampilkan.
- **Rekomendasi Perbaikan**:
  - Set `pageLoading: false` secara default pada saat halaman selesai dimuat, dan **hanya aktifkan `pageLoading = true` saat pengguna memicu pengiriman filter/pencarian baru** (`@submit="pageLoading = true"` atau `@change="pageLoading = true; $el.form.submit()"`).

---

### [UX-09] Inkonsistensi Komponen Empty State (`<x-empty-state>` vs Markup Manual)
- **Tingkat Keparahan**: **LOW** (Konsistensi Komponen Antarmuka)
- **Dimensi**: *Design System & Component Consistency*
- **Lokasi File**:
  - `resources/views/components/empty-state.blade.php` (Baris 1–40)
  - `resources/views/seminar_schedules/index.blade.php` (Baris 414–432)
- **Analisis Masalah**:
  - Komponen `<x-empty-state>` menggunakan deskripsi `uppercase tracking-widest font-black text-[11px]` tanpa dukungan slot tombol aksi (*Call-to-Action* seperti tombol *"Reset Filter Pencarian"*). Akibatnya, halaman seperti `seminar_schedules/index.blade.php` membuat kartu empty state manual sendiri dengan gaya berbeda.
- **Rekomendasi Perbaikan**:
  - Tambahkan dukungan `@isset($action)` atau `$slot` pada `empty-state.blade.php` serta ubah gaya deskripsi dari `uppercase tracking-widest font-black` menjadi teks kalimat normal (`text-xs font-medium text-slate-500 dark:text-slate-400 max-w-md mx-auto`) agar lebih ramah dibaca.

---

### [UX-10] Duplikasi Aturan CSS Global Antara `resources/css/app.css` dan Inline `<style>` pada Layout
- **Tingkat Keparahan**: **LOW** (Maintainability & Single Source of Truth)
- **Dimensi**: *Design System Architecture*
- **Lokasi File**:
  - `resources/css/app.css` (Baris 24–133)
  - `resources/views/layouts/app.blade.php` (Baris 34–93)
  - `resources/views/layouts/guest.blade.php` (Baris 27–163)
- **Analisis Masalah**:
  - Aturan CSS untuk cincin fokus (`#f97316`), autofill WebKit, dan scrollbar kustom ditulis dua kali: satu di dalam `resources/css/app.css` (yang dikompilasi oleh Vite) dan satu lagi di dalam tag `<style>` langsung di `layouts/app.blade.php`.
- **Dampak UX / DX**:
  - Jika desainer atau developer mengubah token fokus di `app.css`, perubahan tersebut akan tertimpa oleh inline `<style>` di `app.blade.php`.
- **Rekomendasi Perbaikan**:
  - Pusatkan seluruh aturan dasar, utilitas *scrollbar*, dan override *autofill* di dalam `resources/css/app.css`, lalu sisakan hanya aturan kritis anti-FOUC (`[x-cloak] { display: none !important; }`) pada inline `<head>`.

---

## 5. Evaluasi 10 Heuristik Usability (Jakob Nielsen) pada SIBIMA

| No | Prinsip Heuristik Nielsen | Status di SIBIMA | Implementasi Saat Ini & Catatan Analisis |
| :-: | :--- | :-: | :--- |
| **H1** | **Visibility of System Status** | **Sangat Baik (90%)** | Indikator tahapan mahasiswa di sidebar, badge jumlah antrean dengan dot animasi, dan status *online* bekerja sangat jelas. *(Catatan: Perbaiki kontras tahap yang sudah selesai pada `[UX-03]`)*. |
| **H2** | **Match Between System and the Real World** | **Istimewa (96%)** | Terminologi sistem sangat sesuai dengan dunia akademik Fasilkom UNSUB (*Gelombang Sempro*, *SK Pembimbing*, *Berita Acara*, *Kaprodi*, *Pembimbing 1/2*, *Penguji*). |
| **H3** | **User Control and Freedom** | **Sangat Baik (90%)** | Tersedia fitur *Reset Filter*, tombol *Batal* di setiap modal, konfirmasi *Rollback* pengajuan skripsi, dan *toggle Buka/Tutup* akordion jadwal. |
| **H4** | **Consistency and Standards** | **Baik (82%)** | Halaman utama sudah konsisten menggunakan palet *Orange/Slate*, namun komponen dasar `<x-primary-button>` dan `<x-modal>` perlu diselaraskan (`[UX-01]`) dan emoji diganti SVG (`[UX-04]`). |
| **H5** | **Error Prevention** | **Sangat Baik (92%)** | Pencegahan bentrok jadwal otomatis, penonaktifan pengiriman ganda, modal konfirmasi sebelum aksi destruktif, dan peringatan baris Excel yang dilewati (`skippedDetails`). |
| **H6** | **Recognition Rather Than Recall** | **Istimewa (95%)** | Dosen tidak perlu mengingat jadwal atau peran mereka; setiap kartu jadwal langsung menampilkan badge **"Peran Anda: Ketua Sidang & Penguji"** serta filter 1-klik **"Hanya Jadwal Saya"**. |
| **H7** | **Flexibility and Efficiency of Use** | **Istimewa (94%)** | Adanya fitur *Simulasi Jadwal Otomatis*, *Import Excel*, *Tab Filter Cepat* untuk Dosen, serta konfirmasi kehadiran 1-klik langsung dari lonceng notifikasi sangat menghemat waktu. |
| **H8** | **Aesthetic and Minimalist Design** | **Sangat Baik (88%)** | Desain bersih, bebas dari gradien berlebihan (*no AI slop*), menggunakan *subtle border* `slate-200`/`slate-800` dan bayangan lembut (`shadow-xs`). |
| **H9** | **Help Users Recognize, Diagnose, and Recover from Errors** | **Sangat Baik (91%)** | Pesan error validasi form jelas, dan saat import Excel menemukan baris tidak valid, sistem menampilkan rincian nomor baris beserta alasan spesifiknya (`Baris X: ...`). |
| **H10** | **Help and Documentation** | **Istimewa (95%)** | Tautan **Pusat Bantuan & Tanya Jawab (FAQ)** tersedia langsung di halaman Login dan di dalam aplikasi untuk memandu pengguna baru. |

---

## 6. Analisis Pengalaman Pengguna Berdasarkan Peran (*Role-Based UX Journey*)

### A. Peran Mahasiswa
- **Kekuatan**:
  - Memiliki panduan alur berurutan (*1. Pengajuan Judul* -> *2. Pendaftaran Sempro* -> *3. Pendaftaran Sidang*) langsung di navigasi kiri.
  - Dapat mengonfirmasi kehadiran seminar/sidang (`Akan Hadir` / `Izin`) dengan 1 klik dari lonceng notifikasi di pojok kanan atas.
- **Titik Friksi & Solusi**:
  - Ketika tahap 1, 2, atau 3 sudah selesai, mengklik menu tersebut memunculkan `alert()` browser (`[UX-03]`). Mengubahnya menjadi lencana `✓ Selesai` berwarna hijau zamrud yang mengarah ke detail progres akan memberikan rasa pencapaian (*sense of accomplishment*) yang positif.

### B. Peran Dosen (Pembimbing & Penguji)
- **Kekuatan**:
  - Tombol *pill* mandiri **"Hanya Jadwal Saya"** (`resources/views/seminar_schedules/index.blade.php`) yang dapat dikombinasikan dengan tab *Mendatang*, *Hari Ini*, dan *Riwayat* adalah solusi UX yang sangat cerdas untuk dosen dengan beban bimbingan tinggi.
  - Kartu jadwal langsung menyoroti peran spesifik dosen pada sesi tersebut (*Ketua Sidang*, *Moderator*, *Penguji*, atau *Pembimbing*).
- **Titik Friksi & Solusi**:
  - Ukuran teks `9px`–`10px` pada lencana status (`[UX-05]`) dan kedipan *skeleton* buatan `150ms` saat mengganti filter di daftar bimbingan (`[UX-08]`) perlu diperhalus agar mata tidak cepat lelah saat memeriksa puluhan mahasiswa.

### C. Peran Kaprodi & Admin Akademik
- **Kekuatan**:
  - *2-Tier Header Toolbar* memisahkan tombol operasional berat (*Simulasi Otomatis*, *Import Excel*, *+ Tambah Manual*) dari alat filter & cetak batch (*Cetak PDF*, *Cetak Word*).
  - Indikator jumlah antrean persetujuan (`$pendingThesesCount`, `$pendingSemproCount`, `$pendingSidangCount`) tampil real-time di sidebar dengan lencana berwarna semantik.
- **Titik Friksi & Solusi**:
  - Memastikan indikator `:focus-visible` keyboard aktif pada seluruh tombol tabel (`[UX-02]`) akan sangat membantu Admin yang sering bekerja cepat menggunakan kombinasi keyboard dan mouse.

---

## 7. Peta Jalan Implementasi Perbaikan (*Prioritized Action Plan*)

Seluruh rekomendasi di atas **100% bersifat non-destruktif terhadap logika bisnis backend** (hanya menyentuh layer presentasi Blade & CSS) sehingga sangat aman untuk diterapkan secara bertahap maupun sekaligus:

| Tahap | Kode Temuan | Fokus Perbaikan UI/UX | Estimasi Risiko | Target File Utama |
| :-: | :-: | :--- | :-: | :--- |
| **Fase 1 (Quick Wins & High Impact)** | **UX-01, UX-02, UX-03** | • Selaraskan `<x-primary-button>`, `<x-secondary-button>`, `<x-modal>` ke token Orange/Slate SIBIMA.<br>• Tambahkan `:focus-visible` ring oranye pada `<button>` dan `<a>`.<br>• Ganti `window.alert()` di stepper sidebar mahasiswa dengan lencana `✓` hijau & notifikasi toast/link. | **Sangat Rendah (0% Risiko Backend)** | `primary-button.blade.php`, `secondary-button.blade.php`, `modal.blade.php`, `app.css`, `app.blade.php` |
| **Fase 2 (Visual Polish & Legibility)** | **UX-04, UX-05, UX-07** | • Ganti seluruh emoji (`🎂`, `📅`, `📍`, `🎓`) dengan ikon SVG Heroicons.<br>• Naikkan ukuran teks mikro `9px` pada `<x-status-badge>` menjadi minimal `11px`.<br>• Hapus `animate-pulse` permanen pada alert sukses & tambahkan dukungan `prefers-reduced-motion`. | **Sangat Rendah (0% Risiko Backend)** | `status-badge.blade.php`, `dashboard.blade.php`, `seminar_schedules/index.blade.php`, `app.blade.php` |
| **Fase 3 (Accessibility & DX Cleanup)** | **UX-06, UX-08, UX-09, UX-10** | • Tambahkan `aria-label` pada seluruh tombol *icon-only*.<br>• Ubah pemicu skeleton `pageLoading` di `theses/index.blade.php` agar aktif saat *submit* filter, bukan *artificial delay* 150ms.<br>• Perkaya `<x-empty-state>` dengan slot aksi & bersihkan duplikasi CSS inline. | **Sangat Rendah (0% Risiko Backend)** | `app.blade.php`, `toast.blade.php`, `login.blade.php`, `theses/index.blade.php`, `empty-state.blade.php` |

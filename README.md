# SIBIMA - Sistem Informasi Bimbingan Mahasiswa

![SIBIMA Logo](https://raw.githubusercontent.com/BagzAlz/sibima/main/public/img/logo.png)

**SIBIMA** adalah platform manajemen bimbingan skripsi dan tugas akhir yang dirancang untuk mempermudah koordinasi antara mahasiswa, dosen pembimbing, dosen penguji, dan pihak administrasi (admin). Sistem ini mengedepankan efisiensi, transparansi progres, dan kemudahan dalam penjadwalan serta evaluasi akademik.

---

## 🌟 Fitur Utama

### 1. Dashboard Interaktif (Admin, Dosen, Mahasiswa)
- Visualisasi statistik progres skripsi.
- Timeline bimbingan untuk mahasiswa.
- Notifikasi real-time untuk aktivitas terbaru.

### 2. Manajemen Skripsi & Pembimbing
- Pengajuan judul skripsi oleh mahasiswa.
- Penugasan dosen pembimbing oleh Admin.
*   **Smart Scheduling**: Pengecekan bentrok jadwal otomatis saat menyusun jadwal seminar/sidang.

### 3. Monitoring Bimbingan (Logbook)
- Pencatatan sesi bimbingan (online/offline).
- Unggah dokumen revisi dan feedback dosen.
- Sistem ACC bimbingan untuk syarat maju seminar/sidang.

### 4. Sistem Revisi Terintegrasi
- Antarmuka diskusi mirip chat antara mahasiswa dan penguji.
- Pelacakan riwayat perbaikan pasca seminar atau sidang.
- Finalisasi revisi dengan status yang jelas.

### 5. Monitoring & Pelaporan
- **Mahasiswa Kritis**: Identifikasi otomatis mahasiswa semester akhir (semester 13-14) untuk pengawasan khusus.
- Ekspor data skripsi, nilai sidang, dan monitoring bimbingan ke format **PDF** dan **Excel**.
- Cetak lembar pernyataan revisi otomatis.

### 6. UI/UX Modern & Responsif
- Menggunakan tema premium dengan dukungan **Dark Mode**.
- Desain responsif yang optimal di berbagai perangkat.

---

## 🚀 Teknologi yang Digunakan

- **Framework:** [Laravel 11](https://laravel.com)
- **Frontend:** [Tailwind CSS](https://tailwindcss.com), [Alpine.js](https://alpinejs.dev)
- **Database:** MySQL
- **UI Kit:** Metronic 8/9 (Customized)
- **Export Tools:** [Laravel Excel](https://laravel-excel.com), [DomPDF](https://github.com/barryvdh/laravel-dompdf)
- **Icons:** Heroicons & FontAwesome

---

## 🛠️ Instalasi

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek secara lokal:

1.  **Clone repositori:**
    ```bash
    git clone https://github.com/BagzAlz/sibima.git
    cd sibima
    ```

2.  **Instal dependensi PHP:**
    ```bash
    composer install
    ```

3.  **Instal dependensi Node.js:**
    ```bash
    npm install
    ```

4.  **Konfigurasi Environment:**
    Salin file `.env.example` menjadi `.env` dan sesuaikan pengaturan database Anda.
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5.  **Migrasi Database & Seeding:**
    ```bash
    php artisan migrate --seed
    ```

6.  **Jalankan Server:**
    ```bash
    php artisan serve
    npm run dev
    ```

---

## 🔒 Keamanan

- Proteksi Middleware pada setiap role (Admin, Dosen, Mahasiswa).
- Validasi kepemilikan data (IDOR Protection) pada semua modul bimbingan dan revisi.
- Proteksi CSRF dan XSS secara menyeluruh.

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---

## 👨‍💻 Kontributor

- **Bagus Ali Akbar** - [@BagzAlz](https://github.com/BagzAlz)

---
*Dibuat dengan ❤️ untuk kemajuan akademik.*

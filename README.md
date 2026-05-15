# SIBIMA - Sistem Informasi Bimbingan Mahasiswa

![SIBIMA Logo](https://raw.githubusercontent.com/BagzAlz/sibima/main/public/img/logo.png)

**SIBIMA** adalah platform manajemen bimbingan skripsi dan tugas akhir yang dirancang untuk mempermudah koordinasi antara mahasiswa, dosen pembimbing, dosen penguji, dan pihak administrasi (admin). Sistem ini mengedepankan efisiensi, transparansi progres, dan kemudahan dalam penjadwalan serta evaluasi akademik.

---

## 🌟 Fitur Utama

### 1. Dashboard Interaktif & Analytics (Admin, Dosen, Mahasiswa)

- **Real-Time Analytics**: Visualisasi statistik kelulusan, beban kerja dosen, dan durasi pengerjaan skripsi menggunakan Chart.js.
- **Dosen Workload**: Pemantauan rasio bimbingan mahasiswa per dosen untuk pemerataan kuota.
- **Student Health Indicator**: Identifikasi otomatis mahasiswa "kritis" (semester akhir) untuk intervensi akademik.
- **Timeline Progres**: Pelacakan visual tahapan skripsi bagi mahasiswa.

### 2. Monitoring & Pelaporan (Reporting Center)

- **Advanced Reporting**: Laporan komprehensif dalam berbagai kategori (Akademik, Mahasiswa, Dosen, Kelulusan, Waktu).
- **Batch Export Berita Acara**: Fitur ekspor massal dokumen resmi (Berita Acara) ke dalam format **ZIP** untuk efisiensi admin.
- **Multi-Format Export**: Dukungan ekspor data ke format **Excel** dan **PDF** yang rapi dan profesional.

### 3. Validasi & Integritas Data

- **QR Code Verification**: Verifikasi keaslian dokumen Berita Acara melalui QR Code terintegrasi.
- **Audit Trail (Activity Logs)**: Pencatatan setiap perubahan data penting (nilai, status) untuk transparansi dan keamanan data.
- **Digital Signature Token**: Penggunaan token unik untuk validasi tanda tangan elektronik pada dokumen resmi.

### 4. Manajemen Skripsi & Bimbingan

- **Smart Scheduling**: Deteksi bentrok jadwal otomatis untuk seminar dan sidang.
- **Integrated Logbook**: Pencatatan sesi bimbingan dengan dukungan unggah dokumen revisi.
- **Revision Workflow**: Antarmuka diskusi interaktif antara mahasiswa dan penguji pasca seminar/sidang.

### 5. UI/UX Modern & Responsif

- **Premium Design**: Menggunakan UI Kit Metronic 9 dengan kustomisasi mendalam.
- **Full Dark Mode**: Dukungan mode gelap yang nyaman untuk penggunaan jangka panjang.
- **High Performance**: Optimasi query dan penggunaan Alpine.js untuk interaksi yang responsif.

---

## 🚀 Teknologi yang Digunakan

- **Framework:** [Laravel 12](https://laravel.com)
- **Real-time:** [Laravel Reverb](https://reverb.laravel.com) 
- **Frontend:** [Tailwind CSS](https://tailwindcss.com), [Alpine.js](https://alpinejs.dev)
- **Database:** MySQL
- **Visualization:** [Chart.js](https://www.chartjs.org/)
- **Document Engine:** [Laravel Excel](https://laravel-excel.com), [DomPDF](https://github.com/barryvdh/laravel-dompdf)
- **QR Engine:** [Simple Software IO QrCode](https://www.simplesoftware.io/docs/simple-qrcode)

---

## 🛠️ Instalasi

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek secara lokal:

1.  **Clone repositori:**

    ```bash
    git clone https://github.com/BagzAlz/sibima.git
    cd sibima
    ```

2.  **Instal dependensi:**

    ```bash
    composer install
    npm install
    ```

3.  **Konfigurasi Environment:**
    Salin file `.env.example` menjadi `.env` dan sesuaikan pengaturan database serta Reverb.

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Setup Database:**

    ```bash
    php artisan migrate --seed
    ```

5.  **Jalankan Server & Assets:**
    ```bash
    php artisan serve
    npm run dev
    # Untuk Real-time Notifications:
    php artisan reverb:start
    ```

---

## 🔒 Keamanan & Integritas

- **IDOR Protection**: Validasi ketat kepemilikan data pada setiap request.
- **Role-Based Access Control (RBAC)**: Pemisahan hak akses antara Admin, Dosen, dan Mahasiswa.
- **Secure Document**: Enkripsi token verifikasi pada QR Code untuk mencegah manipulasi dokumen.

---

## 👨‍💻 Kontributor

- **Bagus Ali Akbar** - [@BagzAlz](https://github.com/BagzAlz)

---

_Dibuat dengan ❤️_

# PANDUAN PENGGUNAAN SISTEM INFORMASI BIMBINGAN TUGAS AKHIR (SIBIMA)
## FAKULTAS ILMU KOMPUTER - UNIVERSITAS SUBANG (FASILKOM UNSUB)

| Metadata Dokumen | Deskripsi Spesifikasi |
| :--- | :--- |
| **Nama Sistem** | SIBIMA (Sistem Informasi Bimbingan Tugas Akhir Mahasiswa) |
| **Institusi** | Program Studi Sistem Informasi & Informatika, Fakultas Ilmu Komputer, Universitas Subang |
| **Versi Dokumen** | 3.0 (User Manual Lengkap Berbasis Multi-Role & Alur Sistematis) |
| **Target Pengguna** | Mahasiswa, Dosen (Pembimbing & Penguji), Ketua Program Studi (Kaprodi), Dekan, dan Administrator / Staf TU |
| **Teknologi Sistem** | Laravel 11 • Tailwind CSS v3.4 • Alpine.js • Inter Font • QR Code Dynamic Token • In-App PDF Streamer |
| **Klasifikasi Dokumen** | Dokumen Panduan Operasional Resmi (*Standard Operating Procedure & User Manual*) |
| **Tanggal Pembaruan** | 29 Oktober 2026 |
| **Status Dokumen** | Resmi & Siap Digunakan (*Production Guide*) |

---

## 1. PENDAHULUAN & PANDUAN AKSES DASAR SISTEM

### 1.1 Persyaratan Perangkat & Peramban (*Browser*) yang Didukung
SIBIMA dirancang berbasis web (*web-based responsive*) dan dapat diakses dengan optimal melalui komputer desktop, laptop, tablet, maupun ponsel pintar (*smartphone*).

* **Peramban yang Didukung Penuh**:
  * Google Chrome (versi 100+)
  * Mozilla Firefox (versi 100+)
  * Microsoft Edge (versi 100+)
  * Apple Safari (iOS 15+ / macOS 12+)
* **Fitur Perangkat Keras yang Dibutuhkan**:
  * **Kamera Ponsel / Webcam**: Wajib memiliki izin akses kamera (*camera permission*) pada peramban untuk memindai kode QR presensi bimbingan tugas akhir.
  * **Resolusi Layar**: Responsif dari layar ponsel (360px) hingga layar monitor lebar (1920px+).

---

### 1.2 Alamat Akses & Alur Masuk Akun (*Login*)
1. Buka peramban internet Anda dan ketikkan alamat resmi sistem SIBIMA:
   `https://sibima.fasilkom.unsub.ac.id` (atau alamat URL server yang telah ditentukan institusi).
2. Anda akan diarahkan ke halaman utama (*Welcome Page*). Klik tombol **Masuk ke SIBIMA** atau langsung menuju rute login:
   `https://sibima.fasilkom.unsub.ac.id/login`
3. Masukkan kredensial akun Anda:
   * **NPM / NIDN / Email**: Masukkan Nomor Pokok Mahasiswa (untuk mahasiswa) atau NIDN/NIP/Email resmi (untuk dosen dan staf).
   * **Kata Sandi (*Password*)**: Masukkan kata sandi rahasia Anda.
   * **Ingat Saya (*Remember Me*)**: Centang opsi ini jika Anda mengakses dari perangkat pribadi.
4. Klik tombol **Masuk Sekarang** (`bg-orange-600`).
5. Sistem akan melakukan autentikasi dan secara otomatis mengarahkan Anda ke **Dasbor Utama** sesuai peran (*role*) akun Anda.

> **Catatan Keamanan**: Jangan pernah membagikan kata sandi Anda kepada siapa pun. Jika Anda mengakses dari komputer laboratorium atau komputer bersama di kampus, pastikan selalu menekan menu **Keluar (Log Out)** sebelum meninggalkan komputer.

---

### 1.3 Navigasi Global & Pengalihan Mode Tampilan (*Dark & Light Mode*)
* **Sidebar Menu**: Terletak di sisi kiri layar. Pada tampilan desktop, Anda dapat mengecilkan sidebar (*collapse*) dengan menekan tombol **Minimize** di pojok kiri atas untuk memberikan area kerja yang lebih luas.
* **Mode Terang / Gelap (*Theme Toggle*)**: Ikon matahari/bulan pada bilah atas memungkinkan Anda berganti antara *Light Mode* (latar putih bersih) dan *Dark Mode* (latar gelap ramah mata) dengan rasio kontras tinggi berstandar WCAG AA.
* **Notifikasi Lonceng & Suara**: Menampilkan pemberitahuan jadwal baru, persetujuan judul, dan komentar revisi secara waktu nyata (*real-time*). Anda dapat mengaktifkan atau menonaktifkan efek suara notifikasi melalui ikon lonceng di bilah atas.

---

### 1.4 Pengaturan Profil Mandiri & Penggantian Kata Sandi
1. Klik foto profil atau nama Anda di sudut kanan atas layar, lalu pilih menu **Profil Pengguna**.
2. **Memperbarui Data Diri**: Anda dapat memperbarui Nama Lengkap, Nomor WhatsApp aktif (sangat penting untuk menerima notifikasi otomatis), dan mengunggah foto profil resmi.
3. **Mengganti Kata Sandi**:
   * Masukkan kata sandi saat ini (*Current Password*).
   * Masukkan kata sandi baru minimal 8 karakter (disarankan kombinasi huruf kapital, angka, dan simbol).
   * Masukkan ulang kata sandi baru untuk konfirmasi.
   * Klik tombol **Simpan Kata Sandi**.

---

## 2. PANDUAN PENGGUNAAN PERAN MAHASISWA

Siklus Tugas Akhir di SIBIMA dirancang secara linear melalui menu bernomor urut **1 s/d 6** di bilah menu samping (*sidebar*), memudahkan mahasiswa memantau progres kelulusan langkah demi langkah:

```
[1. Pengajuan Judul] ➔ [Bimbingan & Logbook] ➔ [2. Pendaftaran Sempro] ➔ [3. Revisi Sempro] 
         ➔ [4. Pendaftaran Sidang] ➔ [5. Revisi Sidang] ➔ [6. Yudisium & SKL]
```

---

### 2.1 Langkah 1: Pengajuan Usulan Judul Tugas Akhir
Menu: **Tahapan Skripsi ➔ 1. Pengajuan Judul**

1. Klik menu **1. Pengajuan Judul** di sidebar.
2. Anda akan disajikan formulir pengajuan judul skripsi:
   * **Judul Utama (Alternatif 1)**: Tuliskan judul tugas akhir yang paling Anda prioritaskan menggunakan huruf kapital di awal kata (*Title Case*).
   * **Judul Alternatif 2 & 3**: Tuliskan cadangan judul lain yang relevan sebagai opsi pertimbangan bagi Kaprodi jika judul utama memerlukan penyesuaian.
   * **Rumusan Masalah & Ruang Lingkup**: Jelaskan secara ringkas latar belakang permasalahan, tujuan penelitian, dan batasan sistem yang akan dibangun.
   * **Berkas Proposal Awal (PDF)**: Unggah dokumen draf proposal awal (format `.pdf`, ukuran maksimal 5 MB).
3. Periksa kembali seluruh isian formulir, lalu klik **Kirim Pengajuan Judul**.
4. **Status Judul**:
   * *Pending / Menunggu Persetujuan*: Judul sedang ditinjau oleh Ketua Program Studi.
   * *Disetujui*: Judul diterima, dan sistem akan menampilkan nama **Dosen Pembimbing 1** dan **Dosen Pembimbing 2**.
   * *Ditolak*: Tinjau catatan alasan penolakan dari Kaprodi, lalu lakukan revisi judul dengan menekan tombol **Ajukan Ulang**.

---

### 2.2 Langkah 2: Pelaksanaan Sesi Bimbingan & Konfirmasi Kehadiran Digital
Menu: **Aktivitas & Bimbingan ➔ Jadwal Bimbingan**

Sesi bimbingan tugas akhir di SIBIMA dapat dijadwalkan oleh Dosen Pembimbing maupun diajukan oleh Mahasiswa melalui menu Jadwal Bimbingan:

1. **Melihat Jadwal Bimbingan**: Mahasiswa membuka menu **Jadwal Bimbingan** untuk melihat agenda bimbingan yang telah ditentukan oleh dosen (topik diskusi, waktu pelaksanaan, serta lokasi tatap muka atau tautan konsultasi daring).
2. **Konfirmasi Kehadiran Mahasiswa**:
   * Pada kartu jadwal bimbingan yang berstatus aktif, klik tombol **Konfirmasi Kehadiran**.
   * Pilih status: **Hadir** (jika siap mengikuti sesi) atau **Izin** (sertakan alasan keterangan jika berhalangan).
   * Klik **Kirim Konfirmasi**. Waktu konfirmasi Anda (`student_confirmed_at`) otomatis tercatat di sistem.
3. **Pelaksanaan & Verifikasi Selesai oleh Dosen**:
   * Selama atau setelah sesi bimbingan berlangsung, dosen pembimbing meninjau progres naskah mahasiswa.
   * Dosen mencatat arahan dan catatan perbaikan pada kolom **Catatan Pembimbing (*Notes*)**, serta dapat mengunggah dokumen revisi (*Feedback Document*).
   * Dosen mengubah status sesi menjadi **Selesai (*Completed*)**.
4. **Pencatatan Resmi di Kartu Bimbingan**:
   Sesi yang telah berstatus *Completed* oleh dosen pembimbing otomatis dihitung sebagai 1 sesi bimbingan resmi dan masuk ke rekapitulasi Kartu Bimbingan Mahasiswa.

---

### 2.3 Langkah 3: Pengisian Logbook Kemajuan Harian & Cetak Kartu Bimbingan
Menu: **Aktivitas & Bimbingan ➔ Logbook Bimbingan**

Mahasiswa diwajibkan mencatat setiap aktivitas kemajuan penulisan skripsi secara berkala ke dalam logbook sistem:

1. Buka menu **Logbook Bimbingan**.
2. Klik tombol **+ Tambah Logbook Baru**.
3. Isi data catatan harian:
   * **Tanggal Aktivitas**: Pilih tanggal pengerjaan tugas akhir.
   * **Bab yang Dikerjakan**: Pilih tahapan bab (BAB I, BAB II, BAB III, BAB IV, BAB V, BAB VI, atau Perancangan Sistem).
   * **Uraian Kemajuan**: Tuliskan secara deskriptif hal-hal yang telah diselesaikan (misalnya: *Menyelesaikan diagram use case dan activity diagram modul pembayaran*).
   * **Kendala yang Dihadapi**: Tuliskan hambatan teknis atau kesulitan referensi (jika ada).
   * **Unggah Lampiran Berkas (Opsional)**: Unggah draf dokumen bab terbaru (format `.pdf` atau `.docx`).
4. Klik **Simpan Logbook**.
5. Dosen pembimbing akan meninjau dan memberikan catatan persetujuan pada logbook Anda.
6. **Cetak Kartu Bimbingan Resmi**:
   * Setelah sesi bimbingan mencukupi syarat minimal (minimal 8 kali bimbingan tervalidasi), klik tombol **Cetak Kartu Bimbingan (PDF)** di bagian atas halaman logbook.
   * Dokumen PDF kartu bimbingan akan terunduh otomatis, lengkap dengan rekapitulasi tanggal, materi bimbingan, dan paraf digital dosen pembimbing.

---

### 2.4 Langkah 4: Pendaftaran Ujian Seminar Proposal (Sempro)
Menu: **Tahapan Skripsi ➔ 2. Pendaftaran Seminar**

Setelah draf proposal tuntas dan disetujui oleh kedua dosen pembimbing, mahasiswa mendaftar ujian seminar proposal:

1. Buka menu **2. Pendaftaran Seminar**.
2. **Pemeriksaan Prasyarat Otomatis**:
   Sistem akan secara otomatis memeriksa:
   * Jumlah bimbingan minimal tercapai ($\ge 8$ sesi).
   * Status persetujuan (*ACC*) dari Pembimbing 1 dan Pembimbing 2 aktif.
3. **Pilih Gelombang Ujian**: Pilih gelombang pelaksanaan seminar proposal yang sedang dibuka oleh program studi.
4. **Unggah Dokumen Persyaratan**:
   * Berkas Naskah Proposal Skripsi Lengkap (format `.pdf`).
   * Lembar Persetujuan Seminar Proposal yang telah ditandatangani pembimbing.
   * Transkrip Nilai Sementara terbaru.
   * Kartu Bimbingan Tugas Akhir.
5. Klik **Kirim Pendaftaran Seminar**.
6. Pantau status pendaftaran. Setelah Kaprodi menerbitkan jadwal ujian, kartu jadwal seminar proposal (tanggal, jam, ruang ujian, dan nama dosen penguji) akan muncul pada dasbor Anda.

---

### 2.5 Langkah 5: Pengelolaan Revisi Seminar Proposal
Menu: **Tahapan Skripsi ➔ 3. Revisi Seminar**

Setelah seminar proposal selesai dilaksanakan, mahasiswa wajib menyelesaikan catatan revisi dari dewan penguji dalam batas waktu **maksimal 14 hari kerja**:

1. Buka menu **3. Revisi Seminar**.
2. Pada halaman ini, Anda dapat membaca:
   * **Berita Acara Seminar Proposal**: Nilai akhir seminar dan keputusan status.
   * **Poin Catatan Revisi**: Rincian perbaikan dari Penguji 1, Penguji 2, dan Dosen Pembimbing.
   * **Sisa Batas Waktu (*Deadline*)**: Penghitung waktu mundur (*countdown timer*) 14 hari kerja.
3. Lakukan perbaikan pada naskah proposal Anda sesuai masukan para penguji.
4. Klik tombol **Unggah Naskah Revisi**:
   * Lampirkan berkas naskah proposal yang telah diperbaiki (`.pdf` / `.docx`).
   * Tuliskan ringkasan poin-poin yang telah diperbaiki pada kolom keterangan matriks revisi.
5. **Diskusi Dua Arah (*Revision Thread*)**:
   Gunakan fitur percakapan revisi di bawah halaman untuk berkonsultasi langsung dengan dosen penguji jika terdapat catatan yang memerlukan klarifikasi.
6. Setelah seluruh dosen penguji menekan tombol **ACC Revisi**, status revisi seminar Anda akan berubah menjadi **Selesai / Lulus Penuh**, dan menu *4. Pendaftaran Sidang* akan terbuka secara otomatis.

---

### 2.6 Langkah 6: Pendaftaran Ujian Sidang Skripsi (Sidang Akhir)
Menu: **Tahapan Skripsi ➔ 4. Pendaftaran Sidang**

Mahasiswa yang telah menuntaskan seluruh naskah BAB I s/d BAB VI dan telah memperoleh persetujuan pembimbing dapat mendaftar Sidang Skripsi:

1. Buka menu **4. Pendaftaran Sidang**.
2. Pilih gelombang sidang skripsi yang aktif.
3. **Unggah Berkas Persyaratan Sidang**:
   * Naskah Skripsi Lengkap (BAB I s/d BAB VI) format PDF.
   * Formulir Persetujuan Sidang Skripsi dari Pembimbing 1 dan 2.
   * Bukti Hasil Cek Plagiarisme (*Turnitin*) resmi dari perpustakaan (Similarity Index $\le 25\%$).
   * Bukti Bebas Tunggakan Biaya Kuliah dari Bagian Keuangan.
   * Sertifikat Kemampuan Bahasa Inggris (TOEFL / sejenis) dan SKPI.
4. Klik **Kirim Berkas Pendaftaran Sidang**.
5. Tunggu proses validasi oleh program studi. Jadwal sidang resmi (susunan majelis ketua sidang, sekretaris, dan penguji) akan tampil di dasbor setelah dijadwalkan oleh Kaprodi.

---

### 2.7 Langkah 7: Pengelolaan Revisi Sidang Skripsi
Menu: **Tahapan Skripsi ➔ 5. Revisi Sidang**

1. Setelah pelaksanaan sidang majelis, buka menu **5. Revisi Sidang**.
2. Periksa status keputusan dewan penguji (*Lulus Murni*, *Lulus dengan Revisi*, atau *Mengulang*).
3. Jika berstatus *Lulus dengan Revisi*, pelajari seluruh catatan perbaikan dari Ketua Sidang, Penguji Utama, dan Pembimbing.
4. Lakukan revisi pada naskah skripsi final dan unggah berkas naskah final yang telah diperbaiki.
5. Pantau status persetujuan dari masing-masing dewan penguji. Seluruh dewan penguji wajib memberikan persetujuan revisi agar Anda berhak melangkah ke tahap Yudisium.

---

### 2.8 Langkah 8: Pengajuan Yudisium & Surat Keterangan Lulus (SKL) Digital
Menu: **Tahapan Skripsi ➔ 6. Yudisium & SKL**

Tahap pamungkas siklus tugas akhir untuk pengesahan kelulusan akademik:

1. Buka menu **6. Yudisium & SKL**.
2. Lengkapi formulir pendaftaran yudisium:
   * **Biodata Alumni Lengkap**: Nama lengkap tanpa singkatan, NIK KTP, tempat tanggal lahir (sesuai ijazah SMA), dan alamat tinggal tetap.
   * **Unggah Bukti Penyerahan Naskah Jilid / CD**: Bukti tanda terima penyerahan berkas tugas akhir ke perpustakaan universitas.
   * **Unggah Naskah Publikasi / Artikel Jurnal**: Draf artikel ilmiah tugas akhir.
3. Klik tombol **Ajukan Permohonan Yudisium**.
4. Setelah disetujui oleh Dekan FASILKOM, tombol **Unduh Surat Keterangan Lulus (SKL)** akan aktif.
5. SKL digital dilengkapi stempel elektronik dan **Kode QR Verifikasi Keabsahan Ijazah/SKL** yang dapat dipindai oleh perusahaan/instansi luar untuk memverifikasi keaslian dokumen kelulusan Anda.

---

### 2.9 Fitur Penunjang Mahasiswa: Pustaka Skripsi & Jurnal Ilmiah
Menu: **Repositori & Referensi ➔ Katalog Skripsi / Jurnal Ilmiah**

* **Membaca Skripsi Alumni (In-App PDF Reader)**:
  * Masuk ke menu **Katalog Skripsi**.
  * Gunakan kotak pencarian untuk mencari topik skripsi (misal: *Sistem Pakar*, *Machine Learning*, *Laravel*, *Flutter*).
  * Klik tombol **Baca Naskah** untuk membuka *In-App Multi-Chapter PDF Reader*. Anda dapat membaca per bab (BAB 1 s/d BAB 6) langsung di browser tanpa perlu mengunduh file besar.
* **Eksplorasi Jurnal Ilmiah Multi-Pangkalan Data**:
  * Masuk ke menu **Jurnal Ilmiah**.
  * Cari artikel referensi dari 5 pangkalan data bereputasi: *Jurnal GLOBAL FASILKOM*, *GARUDA Kemdiktisaintek (SINTA)*, *DOAJ (Directory of Open Access Journals)*, *Crossref*, dan *OpenAlex*.
  * **Salin Sitasi Instan**: Klik tombol **Kutip** pada artikel yang diinginkan, pilih format sitasi (**APA 7th**, **IEEE**, atau **BibTeX**), lalu tempelkan (*paste*) langsung ke Daftar Pustaka naskah skripsi Anda.
  * **Simpan ke Bookmark**: Simpan artikel penting ke dalam folder pustaka pribadi di SIBIMA untuk dibaca kembali sewaktu-waktu.

---

## 3. PANDUAN PENGGUNAAN PERAN DOSEN (PEMBIMBING & PENGUJI)

Akun Dosen di SIBIMA memiliki dua fungsi utama: sebagai **Dosen Pembimbing** yang memandu mahasiswa dari awal hingga akhir, dan sebagai **Dewan Penguji** pada seminar proposal dan sidang skripsi.

---

### 3.1 Sebagai Pembimbing: Meninjau & Menyetujui Usulan Judul Mahasiswa
Menu: **Dokumentasi & Bimbingan ➔ Mahasiswa Bimbingan**

1. Masuk ke menu **Mahasiswa Bimbingan**.
2. Pada tab **Pengajuan Judul**, sistem menampilkan daftar mahasiswa yang mengusulkan judul di mana Anda ditugaskan sebagai Pembimbing 1 atau Pembimbing 2.
3. Klik nama mahasiswa atau tombol **Tinjau Judul** untuk membaca draf proposal awal dan 3 usulan judul yang diajukan.
4. Pilih tindakan:
   * **Setujui Usulan Judul**: Jika topik sesuai dengan bidang keahlian dan memenuhi standar ilmiah tugas akhir.
   * **Beri Catatan / Koreksi Judul**: Jika rumusan kalimat judul perlu disempurnakan.
   * **Tolak Usulan Judul**: Berikan alasan penolakan secara jelas pada kolom catatan agar mahasiswa dapat mengajukan topik alternatif.

---

### 3.2 Sebagai Pembimbing: Membuat Jadwal Bimbingan & Verifikasi Kehadiran Selesai
Menu: **Dokumentasi & Bimbingan ➔ Jadwal Bimbingan**

Dosen Pembimbing dapat menjadwalkan sesi konsultasi mandiri maupun kelompok dengan alur terstruktur:

1. Buka menu **Jadwal Bimbingan**, lalu klik tombol **+ Buat Sesi Bimbingan**.
2. Tentukan rincian sesi:
   * **Tipe Bimbingan**: Pilih *Bimbingan Mandiri (1 Mahasiswa)* atau *Bimbingan Kelompok (Banyak Mahasiswa Sekaligus)*.
   * **Mahasiswa Sasaran**: Pilih mahasiswa bimbingan yang ditugaskan.
   * **Topik Diskusi**: Masukkan pokok bahasan (misalnya: *Review Bab 3 Metodologi Penelitian & Desain Basis Data*).
   * **Tanggal, Waktu & Lokasi**: Tentukan waktu dan tempat (ruang dosen, lab, atau tautan video conference).
3. Klik **Simpan Jadwal Bimbingan**. Sistem otomatis mengirimkan notifikasi agenda ke akun mahasiswa.
4. **Memeriksa Konfirmasi Mahasiswa**: Dosen dapat melihat status respon mahasiswa (*Hadir / Izin*).
5. **Menyelesaikan Sesi (*Mark as Completed*)**:
   * Setelah diskusi bimbingan tuntas, dosen mengisi catatan evaluasi (*Notes* / *Feedback*).
   * Dosen dapat melampirkan file naskah coretan hasil koreksi (*Feedback Document*).
   * Klik tombol **Selesaikan Sesi (*Completed*)**.
6. **Legalisasi Tanda Tangan Digital Ber-QR Code**:
   Saat kartu bimbingan PDF dicetak oleh mahasiswa, sistem otomatis menyematkan **Kode QR Tanda Tangan Digital Resmi** dosen yang merujuk pada token keaslian tanda tangan di basis data (`/verify-signature/{signature_token}`).

---

### 3.3 Sebagai Pembimbing: Memeriksa Logbook & Rekomendasi Kelayakan Ujian (ACC)
Menu: **Dokumentasi & Bimbingan ➔ Mahasiswa Bimbingan ➔ Detail Bimbingan**

1. Pada halaman detail bimbingan mahasiswa, pilih tab **Logbook Aktivitas**.
2. Periksa catatan harian yang dikirim mahasiswa beserta dokumen bab yang dilampirkan.
3. Anda dapat memberikan umpan balik langsung pada setiap catatan logbook.
4. **Memberikan Persetujuan Kelayakan Ujian (ACC)**:
   * **ACC Seminar Proposal**: Jika naskah proposal mahasiswa telah siap diuji di hadapan dewan penguji dan sesi bimbingan telah mencapai syarat minimal, klik tombol **Beri Rekomendasi ACC Sempro** (`bg-emerald-600`).
   * **ACC Sidang Skripsi**: Jika naskah BAB I s/d BAB VI telah sempurna dan hasil cek Turnitin telah memenuhi syarat, klik tombol **Beri Rekomendasi ACC Sidang Skripsi**.

---

### 3.4 Sebagai Pembimbing: Mengunduh Surat Tugas Pembimbing (BKD / SISTER)
Menu: **Dokumentasi & Bimbingan ➔ Surat Tugas**

1. Buka menu **Surat Tugas**.
2. Sistem menyajikan daftar seluruh Surat Tugas dan Surat Keputusan (SK) Dekan yang menetapkan Anda sebagai Dosen Pembimbing untuk setiap semester/gelombang akademik.
3. Klik tombol **Unduh PDF** pada nomor SK yang diinginkan. Berkas PDF resmi ber-QR Code dapat langsung dilampirkan untuk pelaporan Beban Kerja Dosen (BKD) dan portofolio SISTER Kemdiktisaintek.

---

### 3.5 Sebagai Dewan Penguji: Melaksanakan Ujian Seminar Proposal
Menu: **Tugas Penguji ➔ Penguji Seminar**

1. Buka menu **Penguji Seminar**. Anda akan melihat daftar jadwal ujian seminar proposal di mana Anda ditugaskan sebagai Penguji 1 atau Penguji 2.
2. Klik tombol **Buka Ruang Ujian / Nilai**.
3. **Membaca Berkas Naskah**: Klik tautan berkas proposal mahasiswa untuk membaca dokumen langsung di penampil naskah.
4. **Pengisian Rubrik Penilaian**:
   Isi nilai angka (skala 0–100) pada masing-masing komponen penilaian objektif:
   * *Latar Belakang & Perumusan Masalah* (Bobot 25%)
   * *Tinjauan Pustaka & Landasan Teori* (Bobot 25%)
   * *Metodologi Penelitian & Perancangan* (Bobot 30%)
   * *Penguasaan Materi & Presentasi Tanya Jawab* (Bobot 20%)
5. **Catatan Perbaikan / Revisi**:
   Tuliskan poin-poin wajib revisi yang harus dikerjakan mahasiswa pada kolom Berita Acara (misal: *Perbaiki diagram relasi tabel pada halaman 42*).
6. Klik **Simpan & Kunci Nilai Seminar**. Sistem akan secara otomatis menghitung rekapitulasi nilai akhir seminar proposal bersama dewan penguji lainnya.

---

### 3.6 Sebagai Dewan Penguji: Melaksanakan Sidang Skripsi & Penetapan Nilai
Menu: **Tugas Penguji ➔ Penguji Sidang**

1. Buka menu **Penguji Sidang**.
2. Pilih jadwal sidang mahasiswa yang sedang berlangsung, lalu klik **Form Penilaian Sidang**.
3. Masukkan nilai rubrik komprehensif:
   * *Nilai Penulisan Naskah Skripsi*
   * *Kualitas Produk Perangkat Lunak / Sistem yang Dibangun*
   * *Penguasaan Landasan Teori & Pemecahan Masalah*
   * *Kemampuan Menjawab Pertanyaan Dewan Penguji*
4. **Keputusan Kelulusan**: Bersama Ketua Sidang dan Pembimbing, tetapkan keputusan:
   * *Lulus Murni (Tanpa Revisi)*
   * *Lulus dengan Revisi (Wajib Tuntas Maksimal 14 Hari)*
   * *Tidak Lulus / Mengulang Sidang di Gelombang Berikutnya*
5. Klik **Kirim Penilaian Sidang**.

---

### 3.7 Sebagai Penguji: Validasi & Persetujuan (ACC) Revisi Pasca-Ujian
Menu: **Tugas Penguji ➔ Penguji Seminar / Penguji Sidang ➔ Tab Revisi Masuk**

1. Buka tab **Revisi Mahasiswa**.
2. Klik nama mahasiswa yang telah mengunggah berkas revisi naskah.
3. Unduh dan periksa berkas naskah revisi yang dilampirkan, lalu bandingkan dengan catatan berita acara sebelumnya.
4. Jika perbaikan telah sesuai dan tuntas, klik tombol **Setujui Revisi (ACC Revisi)**.
5. Mahasiswa tidak dapat mengesahkan kelulusan/yudisium sebelum Anda dan seluruh dewan penguji memberikan persetujuan revisi ini.

---

## 4. PANDUAN PENGGUNAAN PERAN KETUA PROGRAM STUDI (KAPRODI)

Ketua Program Studi memegang kendali operasional atas alur tugas akhir, alokasi pembimbing, penjadwalan cerdas, dan pengawasan masa studi mahasiswa.

---

### 4.1 Verifikasi Judul & Penetapan Dosen Pembimbing
Menu: **Pelaksanaan Skripsi ➔ Pengajuan & Bimbingan ➔ Pengajuan Judul**

1. Masuk ke menu **Pengajuan Judul**.
2. Anda akan melihat daftar seluruh mahasiswa yang mengajukan usulan judul tugas akhir.
3. Klik tombol **Review & Disposisi** pada mahasiswa yang berstatus *Pending*.
4. Pelajari draf proposal dan usulan judul yang diajukan.
5. **Menetapkan Dosen Pembimbing**:
   * Pilih **Dosen Pembimbing 1** (Pembimbing Utama).
   * Pilih **Dosen Pembimbing 2** (Pembimbing Pendamping).
   * Sistem secara otomatis menampilkan indikator **Beban Kuota Dosen** (misal: *Dr. Budi [5/8 Mahasiswa]*). Pastikan tidak menetapkan dosen yang telah melebihi batas kuota bimbingan aktif.
6. Klik **Setujui & Terbitkan Surat Bimbingan**. Sistem akan mengirimkan notifikasi instan kepada mahasiswa dan kedua dosen yang ditugaskan.

---

### 4.2 Validasi Pendaftaran Seminar & Sidang Mahasiswa
Menu: **Pelaksanaan Skripsi ➔ Seminar Proposal / Sidang Skripsi ➔ Validasi Pendaftaran**

1. Buka menu **Validasi Pendaftaran**.
2. Tinjau kelengkapan berkas yang diunggah mahasiswa (bukti bimbingan, turnitin, bebas keuangan).
3. Klik **Validasi (ACC Pendaftaran)** untuk memasukkan mahasiswa ke dalam antrean kandidat yang siap dijadwalkan pada gelombang aktif.

---

### 4.3 Menjalankan Mesin Simulasi Penjadwalan Otomatis (*AutoSchedule*)
Menu: **Pelaksanaan Skripsi ➔ Jadwal Seminar / Jadwal Sidang**

SIBIMA dilengkapi **AutoSchedule Simulation Engine** untuk menyusun jadwal puluhan mahasiswa secara otomatis tanpa tabrakan waktu dosen maupun ruangan:

1. Buka menu **Jadwal Seminar** atau **Jadwal Sidang**.
2. Pilih Gelombang Ujian aktif.
3. Klik tombol **Simulasi Penjadwalan Otomatis** (`bg-orange-600`).
4. Tentukan parameter simulasi:
   * **Rentang Tanggal Pelaksanaan**: Tentukan tanggal mulai dan selesai ujian.
   * **Pilihan Ruang Sidang**: Centang ruangan fisik yang tersedia (misal: *Lab Komputer 1, Ruang Sidang Dekanat, Ruang 204*).
   * **Durasi per Sesi Ujian**: Default 60 menit per mahasiswa.
5. Klik **Jalankan Mesin Simulasi**.
6. **Evaluasi Matriks Hasil Simulasi**:
   * Sistem memproses seluruh kandidat, memetakan jam kosong dosen, dan mendeteksi bentrok secara simultan.
   * Jika terdapat dosen yang memiliki agenda menguji di ruangan lain pada jam yang sama, sistem secara otomatis menandainya dengan **Lencana Bentrok Merah** beserta keterangan konfliknya.
   * Anda dapat melakukan penyesuaian manual (*drag-and-drop* atau ubah slot) secara fleksibel pada tabel pratinjau.
7. Jika susunan jadwal telah optimal dan bebas bentrok, klik tombol **Terapkan & Terbitkan Jadwal Resmi**.

---

### 4.4 Mengekspor Rekapitulasi Jadwal ke Microsoft Word (`.docx`) & PDF
Menu: **Pelaksanaan Skripsi ➔ Jadwal Seminar / Jadwal Sidang ➔ Toolbar Ekspor**

1. Pada halaman daftar jadwal resmi, klik tombol **Ekspor Word (.docx)** (`bg-blue-600`) atau **Cetak PDF**.
2. Sistem akan menyusun dokumen resmi lengkap dengan Kop Surat FASILKOM UNSUB, susunan tabel ruangan/jam/mahasiswa/penguji, serta tanda tangan digital Kaprodi lengkap dengan QR Code keabsahan berkas.
3. Dokumen Word siap dicetak atau ditempelkan di papan pengumuman fakultas.

---

### 4.5 Monitoring Mahasiswa Pasif, Revisi, & Masa Studi Kritis (*DO Alert*)
Menu: **Monitoring & Laporan ➔ Monitoring Mahasiswa**

* **Monitoring Bimbingan Pasif**: Menampilkan daftar mahasiswa yang tidak melakukan bimbingan selama lebih dari 30 hari. Kaprodi dapat menekan tombol **Kirim Teguran WhatsApp** untuk mengirimkan pesan pengingat langsung ke nomor mahasiswa.
* **Monitoring Revisi Ujian**: Memantau mahasiswa yang masa revisinya mendekati batas 14 hari kerja untuk mencegah keterlambatan penerbitan nilai.
* **Monitoring Masa Studi Kritis (*Drop Out Alert*)**:
  * Menampilkan mahasiswa angkatan tua (semester 12–14).
  * Sistem memberikan peringatan dini (*early warning system*) agar Kaprodi dan dosen pembimbing dapat melakukan intervensi percepatan skripsi sebelum batas masa studi habis.
* **Belum Mengajukan Judul**:
  * Masuk ke menu **Belum Mengajukan Judul**.
  * Melihat seluruh mahasiswa angkatan aktif yang belum mengambil mata kuliah/pengajuan tugas akhir.
  * Klik **Ekspor Excel** untuk rekapitulasi data akademik program studi.

---

## 5. PANDUAN PENGGUNAAN PERAN ADMINISTRATOR / STAF TU & DEKAN

Peran Administrator dan Pimpinan Fakultas mengelola master data institusi, penomoran surat, komunikasi siaran massal, dan pemeliharaan teknis sistem.

---

### 5.1 Manajemen Data Pengguna (*User Management*)
Menu: **Sistem & Konfigurasi ➔ Data Pengguna**

1. Buka menu **Data Pengguna**.
2. **Menambah Pengguna Baru**:
   * Klik tombol **+ Tambah Pengguna**.
   * Isi Nama Lengkap, Email, Nomor Identitas (NPM untuk mahasiswa, NIDN untuk dosen), Nomor WhatsApp, dan tentukan Peran (*Mahasiswa / Dosen / Kaprodi / Admin*).
   * Klik **Simpan Pengguna**.
3. **Impor Massal Pengguna via Excel**:
   * Klik tombol **Import Excel**.
   * Unduh template file Excel resmi SIBIMA, isi data mahasiswa/dosen baru secara massal, lalu unggah kembali berkas tersebut.
4. **Reset Kata Sandi Pengguna**:
   * Cari nama pengguna melalui kotak pencarian.
   * Klik menu aksi **Reset Password** untuk mengembalikan kata sandi ke standar awal atau mengirimkan tautan reset kata sandi ke email pengguna.

---

### 5.2 Pengelolaan Gelombang Periode Ujian (*Waves*)
Menu: **Sistem & Konfigurasi ➔ Gelombang Pelaksanaan**

1. Buka menu **Gelombang Pelaksanaan**.
2. Klik **+ Tambah Gelombang Baru**.
3. Isi rincian gelombang:
   * **Nama Gelombang**: Contoh: *Gelombang I Semester Genap 2026/2027*.
   * **Jenis Pelaksanaan**: *Seminar Proposal* atau *Sidang Skripsi*.
   * **Batas Tanggal Pendaftaran**: Tentukan tanggal buka dan tanggal tutup pendaftaran.
   * **Status Keaktifan**: Centang opsi *Aktif*.
4. Klik **Simpan Gelombang**. Hanya gelombang aktif yang akan muncul pada menu pendaftaran mahasiswa.

---

### 5.3 Pengaturan Format Penomoran Surat Resmi (*Letter Settings*)
Menu: **Sistem & Konfigurasi ➔ Pengaturan Nomor Surat**

1. Buka menu **Pengaturan Nomor Surat**.
2. Anda dapat mengatur pola penomoran otomatis untuk:
   * *Berita Acara Seminar Proposal*
   * *Berita Acara Sidang Skripsi*
   * *Surat Keputusan (SK) Dosen Pembimbing*
   * *Surat Keputusan (SK) Dewan Penguji*
   * *Surat Keterangan Lulus (SKL) Digital*
3. Format mendukung tag dinamis: `{NOMOR}/{KODE_FAKULTAS}/{BULAN_ROMAWI}/{TAHUN}`.
   Contoh hasil nomor otomatis: `042/BA-SKR/FASILKOM/X/2026`.

---

### 5.4 Penerbitan & Pengarsipan SK Dekan
Menu: **Pelaksanaan Skripsi ➔ SK Pembimbing**

1. Masuk ke menu **SK Pembimbing**.
2. Klik tombol **Generate SK Dekan**.
3. Pilih gelombang akademik yang telah disetujui Kaprodi.
4. Sistem menyusun draf SK Dekan lengkap dengan konsiderans pertimbangan hukum universitas dan lampiran daftar nama mahasiswa serta pembimbingnya.
5. **Penandatanganan Dekan**: Dekan meninjau berkas dan menekan tombol **Sahkan & Terbitkan SK**.
6. Dokumen resmi PDF otomatis terbit dengan stempel digital dan tersimpan di arsip dokumen fakultas.

---

### 5.5 Penyiaran Pesan WhatsApp Massal (*WaBroadcast with Spintax*)
Menu: **Sistem & Konfigurasi ➔ Broadcast WA**

Fitur broadcast WhatsApp digunakan untuk menyampaikan pengumuman penting langsung ke ponsel mahasiswa dan dosen:

1. Buka menu **Broadcast WA**, lalu klik **+ Buat Broadcast Baru**.
2. **Pilih Target Penerima**:
   * *Semua Mahasiswa Bimbingan Aktif*
   * *Mahasiswa Belum Seminar Proposal*
   * *Mahasiswa Bimbingan Pasif (>30 Hari)*
   * *Mahasiswa Masa Studi Kritis*
   * *Seluruh Dosen Pembimbing & Penguji*
3. **Menuliskan Pesan dengan Fitur Spintax**:
   Gunakan format kurung kurawal `{kata1|kata2|kata3}` agar setiap nomor menerima susunan kata yang berbeda untuk menghindari pemblokiran (*anti-ban*) oleh WhatsApp:
   ```text
   {Halo|Selamat pagi|Yth.} {Sdr|Mahasiswa} {name},
   
   Diberitahukan bahwa pendaftaran Seminar Proposal Gelombang 2 akan {ditutup|berakhir} pada tanggal {deadline}. {Mohon segera|Harap segera} mengunggah naskah dan melengkapi persyaratan di sistem SIBIMA.
   
   Terima kasih.
   Admin SIBIMA FASILKOM UNSUB
   ```
4. Klik **Kirim Broadcast Sekarang**. Pantau status pengiriman (*Terkirim, Gagal, Tertunda*) pada log riwayat pengiriman.

---

### 5.6 Pemeliharaan Sistem: Backup Database & Pembersihan Cache
Menu: **Sistem & Konfigurasi ➔ Pemeliharaan Sistem**

* **Pencadangan Basis Data (*Database Backup*)**:
  * Masuk ke menu **Backup & Restore**.
  * Klik tombol **Buat Cadangan Baru (Backup Now)**.
  * Sistem akan mengeksekusi dump SQL terkompresi `.sql.gz` ke penyimpanan privat aman `storage/app/private/backups/`.
  * Anda dapat mengunduh berkas arsip dump ke komputer lokal untuk arsip offline.
* **Pembersihan Cache Sistem Produksi**:
  * Klik menu **Bersihkan Cache** pada dropdown pemeliharaan.
  * Sistem secara otomatis membersihkan cache tampilan Blade, cache rute URL, dan konfigurasi framework, serta menyinkronkan migrasi database tanpa henti layanan (*zero-downtime*).

---

## 6. PANDUAN PENYELESAIAN MASALAH (TROUBLESHOOTING & FAQ)

| Gejala / Kendala | Kemungkinan Penyebab | Langkah Solusi Praktis |
| :--- | :--- | :--- |
| **Bagaimana cara mengonfirmasi kehadiran bimbingan?** | Mahasiswa perlu merespons jadwal bimbingan yang telah dibuat dosen. | Buka menu **Jadwal Bimbingan**, temukan kartu sesi bimbingan yang sedang aktif, lalu klik tombol **Konfirmasi Kehadiran** (pilih Hadir atau Izin dengan alasan). Setelah sesi terlaksana, dosen pembimbing akan menandai sesi sebagai *Selesai (Completed)*. |
| **Di mana letak fitur QR Code di SIBIMA?** | QR Code di SIBIMA bukan untuk pemindaian presensi harian, melainkan untuk **Verifikasi Keabsahan Tanda Tangan Digital & Dokumen Resmi**. | QR Code resmi tercetak pada: (1) **Kartu Bimbingan PDF** sebagai legalitas tanda tangan pembimbing; (2) **Berita Acara Ujian PDF** sebagai legalitas penguji; (3) **Jadwal Ujian Word (.docx)**; dan (4) **Surat Keterangan Lulus (SKL) Digital** yang dapat dipindai oleh pihak eksternal untuk membuktikan keaslian dokumen kelulusan. |
| **Naskah PDF di In-App Reader tidak muncul / blank** | Kendala jaringan saat proses streaming awal dari server Google Drive UNSUB. | Tekan tombol **Muat Ulang Berkas** pada bilah reader PDF. Jika tetap berkendala, gunakan tombol **Unduh Salinan Asli** untuk membuka file di penampil PDF eksternal gawai Anda. |
| **Tidak menerima pesan notifikasi WhatsApp** | Nomor WhatsApp pada profil pengguna salah atau tidak menggunakan kode negara `62`. | Periksa menu **Profil Pengguna**. Pastikan nomor telepon diawali format internasional resmi Indonesia (contoh: `081234567890` atau `6281234567890`), bukan tanda plus atau tanda strip. |
| **Menu "Pendaftaran Seminar" terkunci / tidak bisa diklik** | Prasyarat jumlah sesi bimbingan belum terpenuhi (< 8 sesi) atau belum di-ACC dosen. | Buka menu **Logbook Bimbingan**, pastikan telah memiliki minimal 8 sesi bimbingan yang berstatus *Selesai/Tervalidasi* dan mintalah kedua dosen pembimbing menekan tombol ACC Sempro di akun mereka. |
| **Waktu revisi 14 hari telah habis (*Expired*)** | Mahasiswa terlambat mengunggah perbaikan naskah setelah batas waktu seminar/sidang. | Hubungi Ketua Program Studi atau Administrator. Kaprodi memiliki wewenang untuk memberikan perpanjangan dispensasi waktu revisi pada sistem setelah mengevaluasi kendala mahasiswa. |
| **Lupa Kata Sandi Akun SIBIMA** | Pengguna lupa kombinasi password akun. | Hubungi Staf Tata Usaha FASILKOM atau Administrator SIBIMA dengan menunjukkan kartu identitas mahasiswa (KTM) atau kartu pegawai untuk dilakukan reset kata sandi instan. |

---

*Dokumen Panduan Operasional Sistem SIBIMA Versi 3.0 ini diterbitkan secara resmi oleh Fakultas Ilmu Komputer Universitas Subang sebagai acuan tata kelola baku tugas akhir mahasiswa.*
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WaTemplate;

class WaTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'code' => 'mentoring_requested',
                'name' => 'Pengajuan Bimbingan Baru (ke Dosen)',
                'category' => 'Bimbingan',
                'content' => "Halo Bpk/Ibu *{nama_dosen}*,\n\nMahasiswa bimbingan Anda, *{nama_mahasiswa}*, telah mengajukan jadwal bimbingan skripsi.\n\nWaktu: {tanggal_bimbingan} WIB\nTopik: {topik_bimbingan}\n\nSilakan cek dan konfirmasi jadwal tersebut di dashboard SIBIMA:\n{link_mentoring}",
                'available_variables' => [
                    'nama_dosen' => 'Nama Dosen Pembimbing',
                    'nama_mahasiswa' => 'Nama Mahasiswa',
                    'tanggal_bimbingan' => 'Waktu & Tanggal Bimbingan',
                    'topik_bimbingan' => 'Topik/Bahasan Bimbingan',
                    'link_mentoring' => 'Tautan ke Halaman Bimbingan',
                ],
            ],
            [
                'code' => 'mentoring_scheduled_by_dosen',
                'name' => 'Jadwal Bimbingan Baru dari Dosen (ke Mahasiswa)',
                'category' => 'Bimbingan',
                'content' => "🔔 *JADWAL BIMBINGAN SKRIPSI BARU*\n\nHalo *{nama_mahasiswa}*,\n\nDosen Pembimbing Anda, *{nama_dosen}*, telah membuat jadwal bimbingan skripsi baru:\n\n📝 *Topik*: {topik_bimbingan}\n📅 *Waktu*: {tanggal_bimbingan} WIB\n📍 *Jenis/Lokasi*: {jenis_bimbingan}{catatan_bimbingan}\n\n⚠️ *Penting*: Silakan buka sistem SIBIMA untuk melakukan *Konfirmasi Kehadiran* (Akan Hadir / Izin):\n{link_mentoring}\n\nTerima kasih.\n_Sistem Informasi Bimbingan Skripsi (SIBIMA)_",
                'available_variables' => [
                    'nama_mahasiswa' => 'Nama Mahasiswa',
                    'nama_dosen' => 'Nama Dosen Pembimbing',
                    'topik_bimbingan' => 'Topik Bimbingan',
                    'tanggal_bimbingan' => 'Waktu & Tanggal Bimbingan',
                    'jenis_bimbingan' => 'Jenis Bimbingan (Online / Offline) & Lokasi',
                    'catatan_bimbingan' => 'Blok Catatan Dosen (Otomatis jika ada catatan)',
                    'catatan' => 'Teks Catatan Dosen saja',
                    'link_mentoring' => 'Tautan ke Halaman Bimbingan',
                ],
            ],
            [
                'code' => 'mentoring_rescheduled',
                'name' => 'Perubahan Jadwal Bimbingan / Reschedule (ke Mahasiswa)',
                'category' => 'Bimbingan',
                'content' => "🔔 *PERUBAHAN JADWAL BIMBINGAN (RESCHEDULE)*\n\nHalo *{nama_mahasiswa}*,\n\nJadwal bimbingan skripsi Anda telah diubah / dijadwalkan ulang oleh Dosen Pembimbing *{nama_dosen}*:\n\n📝 *Topik*: {topik_bimbingan}\n📅 *Waktu Baru*: {tanggal_bimbingan} WIB\n📍 *Jenis/Lokasi*: {jenis_bimbingan}{catatan_bimbingan}\n\n⚠️ *Penting*: Silakan buka sistem SIBIMA untuk melakukan *Konfirmasi Ulang Kehadiran* Anda:\n{link_mentoring}\n\nTerima kasih.\n_Sistem Informasi Bimbingan Skripsi (SIBIMA)_",
                'available_variables' => [
                    'nama_mahasiswa' => 'Nama Mahasiswa',
                    'nama_dosen' => 'Nama Dosen Pembimbing',
                    'topik_bimbingan' => 'Topik Bimbingan',
                    'tanggal_bimbingan' => 'Waktu & Tanggal Bimbingan Baru',
                    'jenis_bimbingan' => 'Jenis Bimbingan (Online / Offline) & Lokasi Baru',
                    'catatan_bimbingan' => 'Blok Catatan Dosen (Otomatis jika ada catatan)',
                    'catatan' => 'Teks Catatan Dosen saja',
                    'link_mentoring' => 'Tautan ke Halaman Bimbingan',
                ],
            ],
            [
                'code' => 'mentoring_status_updated',
                'name' => 'Persetujuan / Penolakan Bimbingan (ke Mahasiswa)',
                'category' => 'Bimbingan',
                'content' => "{Halo|Hai|Salam|Yth.} *{nama_mahasiswa}*,\n\n{Pemberitahuan bahwa status|Menginformasikan bahwa status|Status} pengajuan bimbingan skripsi Anda (Topik: *{topik_bimbingan}*) bersama *{nama_dosen}* telah **{status_bimbingan}**.\n\n{Catatan / Evaluasi Dosen:|Catatan dari Dosen:|Arahan Dosen:}\n\"{catatan_dosen}\"\n\n{Silakan periksa detailnya di dashboard SIBIMA:|Detail selengkapnya dapat dicek di SIBIMA:|Cek selengkapnya melalui tautan:}\n{link_mentoring}",
                'available_variables' => [
                    'nama_mahasiswa' => 'Nama Mahasiswa',
                    'nama_dosen' => 'Nama Dosen Pembimbing',
                    'topik_bimbingan' => 'Topik Bimbingan',
                    'status_bimbingan' => 'Status (DISETUJUI / DITOLAK / TIDAK HADIR / SELESAI)',
                    'catatan_dosen' => 'Catatan / Alasan dari Dosen',
                    'link_mentoring' => 'Tautan ke Halaman Bimbingan',
                ],
            ],
            [
                'code' => 'mentoring_reminder',
                'name' => 'Pengingat H-1 Jadwal Bimbingan',
                'category' => 'Bimbingan',
                'content' => "🔔 *REMINDER BIMBINGAN BESOK (H-1)*\n\nHalo *{nama_penerima}*,\n\nJangan lupa jadwal bimbingan skripsi besok antara *{nama_mahasiswa}* dan *{nama_dosen}*.\n\n📅 Waktu: {tanggal_bimbingan} WIB\n📍 Tempat/Media: {lokasi_bimbingan}\n📝 Topik: {topik_bimbingan}\n\nCek detail bimbingan di SIBIMA:\n{link_mentoring}",
                'available_variables' => [
                    'nama_penerima' => 'Nama Penerima Notifikasi',
                    'nama_mahasiswa' => 'Nama Mahasiswa',
                    'nama_dosen' => 'Nama Dosen',
                    'tanggal_bimbingan' => 'Waktu Bimbingan',
                    'lokasi_bimbingan' => 'Lokasi / Ruangan / Link',
                    'topik_bimbingan' => 'Topik Bimbingan',
                    'link_mentoring' => 'Tautan Bimbingan',
                ],
            ],
            [
                'code' => 'supervisor_assigned',
                'name' => 'Penugasan Pembimbing Baru (ke Dosen)',
                'category' => 'Skripsi',
                'content' => "Halo Bpk/Ibu *{nama_dosen}*,\n\nAnda telah ditugaskan sebagai *{peran_pembimbing}* untuk mahasiswa:\nNama: *{nama_mahasiswa}*\nJudul Skripsi: {judul_skripsi}\n\nSilakan cek dashboard SIBIMA untuk melihat detail lebih lanjut.\n{link_login}",
                'available_variables' => [
                    'nama_dosen' => 'Nama Dosen',
                    'peran_pembimbing' => 'Pembimbing 1 / Pembimbing 2',
                    'nama_mahasiswa' => 'Nama Mahasiswa',
                    'judul_skripsi' => 'Judul Skripsi',
                    'link_login' => 'Tautan Login SIBIMA',
                ],
            ],
            [
                'code' => 'thesis_accepted',
                'name' => 'Judul Skripsi Diterima & Pembimbing Ditetapkan (ke Mahasiswa)',
                'category' => 'Skripsi',
                'content' => "Halo *{nama_mahasiswa}*,\n\nPengajuan judul skripsi Anda **BISA DILANJUTKAN**\n\nBerikut adalah dosen pembimbing yang ditugaskan untuk Anda:\nPembimbing 1: {pembimbing_1}\nPembimbing 2: {pembimbing_2}\n\nSilakan segera menghubungi dosen pembimbing Anda, diskusikan konsep/gambaran rencana penelitiannya dan memulai proses bimbingan melalui dashboard SIBIMA:\n{link_login}",
                'available_variables' => [
                    'nama_mahasiswa' => 'Nama Mahasiswa',
                    'pembimbing_1' => 'Nama Pembimbing 1',
                    'pembimbing_2' => 'Nama Pembimbing 2',
                    'link_login' => 'Tautan Login SIBIMA',
                ],
            ],
            [
                'code' => 'acc_given',
                'name' => 'ACC Maju Seminar / Sidang (ke Mahasiswa)',
                'category' => 'Skripsi',
                'content' => "Halo *{nama_mahasiswa}*,\n\nSelamat! Anda telah mendapatkan **ACC {jenis_acc}** dari *{nama_pemberi_acc}*.\n\nSilakan cek status kelengkapan ACC dan segera lakukan pendaftaran gelombang melalui dashboard SIBIMA:\n{link_login}",
                'available_variables' => [
                    'nama_mahasiswa' => 'Nama Mahasiswa',
                    'jenis_acc' => 'Seminar UP / Sidang Akhir',
                    'nama_pemberi_acc' => 'Nama Dosen / Kaprodi Pemberi ACC',
                    'link_login' => 'Tautan Login SIBIMA',
                ],
            ],
            [
                'code' => 'thesis_completed',
                'name' => 'Skripsi Selesai / Lulus (ke Mahasiswa)',
                'category' => 'Skripsi',
                'content' => "Halo *{nama_mahasiswa}*,\n\nSelamat! Seluruh revisi sidang skripsi Anda telah disetujui oleh para penguji.\n\nSkripsi Anda kini berstatus **SELESAI / LULUS**.\n\nSilakan cek dashboard SIBIMA untuk langkah selanjutnya (seperti pemberkasan yudisium/wisuda).\n{link_login}",
                'available_variables' => [
                    'nama_mahasiswa' => 'Nama Mahasiswa',
                    'link_login' => 'Tautan Login SIBIMA',
                ],
            ],
            [
                'code' => 'revision_requested',
                'name' => 'Catatan Revisi Baru dari Penguji (ke Mahasiswa)',
                'category' => 'Ujian',
                'content' => "Halo *{nama_mahasiswa}*,\n\nDosen penguji telah memberikan **Revisi {jenis_ujian}** untuk skripsi Anda.\n\nSilakan login ke dashboard SIBIMA untuk melihat detail revisi yang harus dikerjakan dan segera perbaiki sesuai tenggat waktu yang diberikan.\n\n{link_login}",
                'available_variables' => [
                    'nama_mahasiswa' => 'Nama Mahasiswa',
                    'jenis_ujian' => 'Seminar UP / Sidang Akhir',
                    'link_login' => 'Tautan Login SIBIMA',
                ],
            ],
            [
                'code' => 'revision_submitted',
                'name' => 'Tanggapan Revisi di-Upload Mahasiswa (ke Dosen Penguji)',
                'category' => 'Ujian',
                'content' => "Halo Bpk/Ibu *{nama_dosen}*,\n\nMahasiswa *{nama_mahasiswa}* telah mengunggah tanggapan/perbaikan **Revisi {jenis_ujian}**.\n\nPesan/Catatan: \"{catatan_mahasiswa}\"\n\nSilakan periksa dokumen perbaikan dan berikan persetujuan di dashboard SIBIMA:\n{link_login}",
                'available_variables' => [
                    'nama_dosen' => 'Nama Dosen Penguji',
                    'nama_mahasiswa' => 'Nama Mahasiswa',
                    'jenis_ujian' => 'Seminar UP / Sidang Akhir',
                    'catatan_mahasiswa' => 'Pesan/Catatan Perbaikan dari Mahasiswa',
                    'link_login' => 'Tautan Login SIBIMA',
                ],
            ],
            [
                'code' => 'schedule_published',
                'name' => 'Jadwal Seminar / Sidang Terbit',
                'category' => 'Ujian',
                'content' => "Halo *{nama_penerima}*,\n\nJadwal *{jenis_ujian}* Anda telah dirilis!\n\nTanggal: {tanggal_ujian}\nRuangan: {lokasi_ujian}\n\nMohon hadir tepat waktu dan persiapkan segala dokumen yang diperlukan. Cek detail selengkapnya di dashboard SIBIMA:\n{link_login}",
                'available_variables' => [
                    'nama_penerima' => 'Nama Penerima (Dosen / Mahasiswa)',
                    'jenis_ujian' => 'Seminar UP / Sidang Akhir',
                    'tanggal_ujian' => 'Tanggal Ujian',
                    'lokasi_ujian' => 'Ruangan / Lokasi Ujian',
                    'link_login' => 'Tautan Login SIBIMA',
                ],
            ],
            [
                'code' => 'schedule_reminder',
                'name' => 'Pengingat H-1 / H-3 Seminar & Sidang',
                'category' => 'Pengingat',
                'content' => "🔔 *REMINDER JADWAL SIBIMA ({label_waktu})*\n\nHalo, *{nama_penerima}*!\n\n{pesan_pengingat}\n\n📅 *Detail Jadwal:*\n• Jenis: {jenis_ujian}\n• Tanggal: {tanggal_ujian}\n• Waktu: {jam_ujian}\n• Ruangan: {lokasi_ujian}\n• Mahasiswa: {nama_mahasiswa}\n\nHarap hadir tepat waktu dan mempersiapkan dokumen yang diperlukan. Cek detail di dashboard SIBIMA:\n{link_login}",
                'available_variables' => [
                    'label_waktu' => 'H-1 / H-3',
                    'nama_penerima' => 'Nama Penerima',
                    'pesan_pengingat' => 'Pesan pengingat umum',
                    'jenis_ujian' => 'Seminar UP / Sidang Skripsi',
                    'tanggal_ujian' => 'Tanggal Ujian',
                    'jam_ujian' => 'Jam Ujian',
                    'lokasi_ujian' => 'Ruangan Ujian',
                    'nama_mahasiswa' => 'Nama Mahasiswa',
                    'link_login' => 'Tautan Login SIBIMA',
                ],
            ],
            [
                'code' => 'critical_student_reminder',
                'name' => 'Pengingat Mahasiswa Semester Kritis (Sem 13-14+)',
                'category' => 'Pengingat',
                'content' => "⚠️ *PERINGATAN MASA STUDI SIBIMA*\n\nHalo *{nama_mahasiswa}*,\n\nSaat ini Anda berada di **Semester {semester_ke}** (Semester Kritis). Mari manfaatkan waktu yang ada untuk segera menyelesaikan proses penyusunan skripsi Anda.\n\n💡 *Langkah yang disarankan:*\n1. Segera jadwalkan bimbingan rutin dengan Dosen Pembimbing.\n2. Konsultasikan kendala atau hambatan penelitian Anda ke Prodi.\n\nMari selesaikan studi Anda tepat waktu! Cek progres Anda di dashboard SIBIMA:\n{link_login}",
                'available_variables' => [
                    'nama_mahasiswa' => 'Nama Mahasiswa',
                    'semester_ke' => 'Angka Semester Saat Ini',
                    'link_login' => 'Tautan Login SIBIMA',
                ],
            ],
            [
                'code' => 'kaprodi_critical_summary',
                'name' => 'Laporan Mahasiswa Kritis (ke Kaprodi/Admin)',
                'category' => 'Pengingat',
                'content' => "📊 *LAPORAN MAHASISWA SEMESTER KRITIS SIBIMA*\n\nHalo Bpk/Ibu *{nama_kaprodi}*,\n\nSaat ini terdapat **{jumlah_mahasiswa} mahasiswa** yang berada pada semester kritis (Semester 13-14+):\n\n{daftar_mahasiswa}\n\nSilakan periksa daftar selengkapnya dan lakukan pemantauan pada menu Monitoring Kritis SIBIMA:\n{link_monitoring}",
                'available_variables' => [
                    'nama_kaprodi' => 'Nama Kaprodi / Admin',
                    'jumlah_mahasiswa' => 'Jumlah Mahasiswa Kritis',
                    'daftar_mahasiswa' => 'Daftar Nama Mahasiswa Singkat',
                    'link_monitoring' => 'Tautan Menu Monitoring Kritis',
                ],
            ],
            [
                'code' => 'dosen_belum_selesai_bimbingan',
                'name' => 'Pengingat Sesi Bimbingan Belum Selesai (ke Dosen)',
                'category' => 'Pengingat',
                'content' => "🔔 *PENGINGAT PENYELESAIAN SESI BIMBINGAN SKRIPSI*\n\nYth. Bpk/Ibu *{nama}*,\n\nBerdasarkan pantauan sistem SIBIMA, tercatat terdapat *{jumlah_sesi} sesi bimbingan* yang statusnya belum diselesaikan atau belum diinput catatan/feedback hasil bimbingan:\n\n{daftar_mahasiswa}\n\nMohon kesediaan Bpk/Ibu untuk memperbarui status dan menginput hasil bimbingan mahasiswa melalui tautan berikut:\n{link_bimbingan}\n\nTerima kasih atas kerja sama dan dedikasi Bpk/Ibu.\n_Program Studi FASILKOM UNSUB_",
                'available_variables' => [
                    'nama' => 'Nama Dosen Pembimbing',
                    'nidn' => 'NIDN Dosen',
                    'jumlah_sesi' => 'Jumlah Sesi Belum Selesai',
                    'jumlah_lewat_jadwal' => 'Jumlah Sesi Melewati Jadwal',
                    'daftar_mahasiswa' => 'Daftar Nama Mahasiswa & Topik',
                    'sesi_terlama' => 'Tanggal Sesi Tertua yang Menggantung',
                    'link_bimbingan' => 'Tautan Halaman Bimbingan',
                    'link_dashboard' => 'Tautan Dashboard',
                ],
            ],
            [
                'code' => 'mahasiswa_bimbingan_pasif',
                'name' => 'Teguran & Evaluasi Bimbingan Skripsi Pasif (ke Mahasiswa)',
                'category' => 'Pengingat',
                'content' => "⚠️ *EVALUASI KEAKTIFAN BIMBINGAN SKRIPSI*\n\nHalo *{nama}* (NPM: {npm}),\n\nSistem SIBIMA mendeteksi Anda telah {hari_tanpa_bimbingan} tidak melakukan sesi bimbingan skripsi (Aktivitas terakhir: {terakhir_bimbingan}).\n\nJudul: \"_{judul}_\"\nPembimbing Utama: {pembimbing_1}\n\nMohon segera mengajukan jadwal bimbingan kembali dengan dosen pembimbing Anda untuk mencegah keterlambatan studi:\n{link_bimbingan}\n\nTerima kasih.\n_SIBIMA FASILKOM UNSUB_",
                'available_variables' => [
                    'nama' => 'Nama Mahasiswa',
                    'npm' => 'NPM Mahasiswa',
                    'angkatan' => 'Tahun Angkatan',
                    'judul' => 'Judul Skripsi',
                    'pembimbing_1' => 'Nama Pembimbing 1',
                    'hari_tanpa_bimbingan' => 'Hari Mangkir/Tanpa Bimbingan',
                    'terakhir_bimbingan' => 'Tanggal Bimbingan Terakhir',
                    'link_bimbingan' => 'Tautan Halaman Bimbingan',
                ],
            ],
            [
                'code' => 'mahasiswa_belum_seminar',
                'name' => 'Pengingat Pendaftaran Seminar Proposal (ke Mahasiswa)',
                'category' => 'Pengingat',
                'content' => "🔔 *PENGINGAT PENDAFTARAN SEMINAR PROPOSAL*\n\nHalo *{nama}* (NPM: {npm}),\n\nBerdasarkan data sistem SIBIMA, judul skripsi Anda \"_{judul}_\" telah disetujui, namun Anda *belum mendaftar seminar proposal*.\n\nMari segera tuntaskan naskah proposal Anda bersama Dosen Pembimbing:\n1. {pembimbing_1}\n2. {pembimbing_2}\n\nPendaftaran seminar gelombang terbaru dapat dilakukan melalui tautan berikut:\n{link_seminar}\n\nTetap semangat! 🎓\n_Program Studi FASILKOM UNSUB_",
                'available_variables' => [
                    'nama' => 'Nama Mahasiswa',
                    'npm' => 'NPM Mahasiswa',
                    'angkatan' => 'Tahun Angkatan',
                    'judul' => 'Judul Skripsi',
                    'pembimbing_1' => 'Nama Pembimbing 1',
                    'pembimbing_2' => 'Nama Pembimbing 2',
                    'link_seminar' => 'Tautan Pendaftaran Seminar',
                ],
            ],
            [
                'code' => 'mahasiswa_belum_sidang',
                'name' => 'Pemberitahuan Pendaftaran Sidang Skripsi (ke Mahasiswa)',
                'category' => 'Pengingat',
                'content' => "🎓 *INFORMASI PENDAFTARAN SIDANG SKRIPSI*\n\nHalo *{nama}*,\n\nSelamat atas penyelesaian revisi seminar proposal Anda! Segera lengkapi naskah final skripsi Anda dan ajukan pendaftaran sidang skripsi melalui sistem SIBIMA:\n{link_sidang}\n\nJangan menunda, selangkah lagi menuju toga wisuda! 🚀\n_SIBIMA FASILKOM UNSUB_",
                'available_variables' => [
                    'nama' => 'Nama Mahasiswa',
                    'npm' => 'NPM Mahasiswa',
                    'angkatan' => 'Tahun Angkatan',
                    'judul' => 'Judul Skripsi',
                    'link_sidang' => 'Tautan Pendaftaran Sidang Skripsi',
                ],
            ],
            [
                'code' => 'mahasiswa_belum_skripsi',
                'name' => 'Himbauan Pengajuan Usulan Judul Skripsi (ke Mahasiswa)',
                'category' => 'Pengingat',
                'content' => "📋 *HIMBAUAN PENGAJUAN JUDUL SKRIPSI*\n\nHalo *{nama}* (NPM: {npm}), Mahasiswa Angkatan {angkatan},\n\nBerdasarkan data akademik pada sistem SIBIMA, Anda tercatat telah memenuhi persyaratan untuk memprogram tugas akhir/skripsi, namun *belum mengajukan usulan judul skripsi*.\n\nKami menghimbau Anda untuk segera mempersiapkan topik/minat penelitian dan mengajukan judul skripsi melalui portal SIBIMA agar masa studi Anda dapat selesai tepat waktu:\n{link_pengajuan}\n\nMari segera mulai langkah skripsi Anda! 🎓\n_Program Studi FASILKOM UNSUB_",
                'available_variables' => [
                    'nama' => 'Nama Mahasiswa',
                    'npm' => 'NPM Mahasiswa',
                    'angkatan' => 'Tahun Angkatan',
                    'link_pengajuan' => 'Tautan Pengajuan Judul Skripsi',
                ],
            ],
            [
                'code' => 'dosen_penguji_gelombang',
                'name' => 'Pemberitahuan Tugas Penguji Ujian Skripsi (ke Dosen)',
                'category' => 'Pengingat',
                'content' => "⚖️ *PEMBERITAHUAN TUGAS PENGUJI UJIAN SKRIPSI*\n\nYth. Bpk/Ibu *{nama}*,\n\nDengan hormat kami sampaikan bahwa jadwal penguji untuk *{gelombang}* telah diterbitkan pada sistem SIBIMA.\n\nBpk/Ibu terdaftar sebagai *Dosen Penguji* pada gelombang tersebut. Mohon kesediaan Bpk/Ibu untuk meninjau jadwal, berkas naskah mahasiswa, dan menghadiri sesi ujian sesuai waktu yang telah diagendakan.\n\nDetail jadwal ujian dan berkas skripsi dapat diakses melalui Dashboard SIBIMA:\n{link_dashboard}\n\nTerima kasih atas dedikasi dan kerja sama Bpk/Ibu dalam mengawal mutu kelulusan mahasiswa.\n_Program Studi FASILKOM UNSUB_",
                'available_variables' => [
                    'nama' => 'Nama Dosen Penguji',
                    'nidn' => 'NIDN Dosen',
                    'gelombang' => 'Nama Gelombang Ujian',
                    'link_dashboard' => 'Tautan Dashboard',
                ],
            ],
            [
                'code' => 'dosen_pembimbing_aktif',
                'name' => 'Laporan Singkat Monitoring Bimbingan Mahasiswa (ke Dosen)',
                'category' => 'Pengingat',
                'content' => "Yth. Bpk/Ibu *{nama}*,\n\nTerima kasih atas dedikasi Bpk/Ibu dalam membimbing {jumlah_bimbingan} mahasiswa skripsi aktif di SIBIMA.\n\nMohon bantuannya untuk mengecek kemajuan naskah dan logbook bimbingan mahasiswa bimbingan melalui dashboard SIBIMA:\n{link_dashboard}\n\nSalam takzim,\n_Program Studi FASILKOM UNSUB_",
                'available_variables' => [
                    'nama' => 'Nama Dosen Pembimbing',
                    'nidn' => 'NIDN Dosen',
                    'jumlah_bimbingan' => 'Jumlah Mahasiswa Bimbingan Aktif',
                    'link_dashboard' => 'Tautan Dashboard',
                ],
            ],
            [
                'code' => 'all_mahasiswa_aktif',
                'name' => 'Pengumuman Akademik Skripsi Massal (ke Mahasiswa)',
                'category' => 'Pengingat',
                'content' => "📢 *PENGUMUMAN AKADEMIK SKRIPSI*\n\nHalo *{nama}* (NPM: {npm}),\n\nBerikut kami sampaikan pengumuman penting terkait progres skripsi, jadwal gelombang seminar & sidang, serta ketertiban administrasi bimbingan di lingkungan FASILKOM UNSUB.\n\nPastikan Anda selalu memantau perkembangan status skripsi dan melengkapi logbook bimbingan secara berkala di portal SIBIMA:\n{link_dashboard}\n\nTetap semangat dalam menyelesaikan tugas akhir Anda! 🎓✨\n_Program Studi FASILKOM UNSUB_",
                'available_variables' => [
                    'nama' => 'Nama Mahasiswa',
                    'npm' => 'NPM Mahasiswa',
                    'angkatan' => 'Tahun Angkatan',
                    'link_dashboard' => 'Tautan Dashboard',
                ],
            ],
            [
                'code' => 'mentoring_cancelled',
                'name' => 'Pembatalan Jadwal Bimbingan (ke Mahasiswa / Dosen)',
                'category' => 'Bimbingan',
                'content' => "🚫 *PEMBATALAN JADWAL BIMBINGAN SKRIPSI*\n\nHalo *{nama_penerima}*,\n\nSesi bimbingan skripsi berikut telah *DIBATALKAN* oleh {role_pembatal} *{nama_pembatal}*:\n\n📝 *Topik*: {topik_bimbingan}\n📅 *Jadwal Awal*: {tanggal_bimbingan} WIB\n💬 *Alasan Pembatalan*: {alasan_pembatalan}\n\nSilakan ajukan atau jadwalkan kembali sesi bimbingan berikutnya melalui sistem SIBIMA:\n{link_mentoring}\n\nTerima kasih.\n_Sistem Informasi Bimbingan Skripsi (SIBIMA)_",
                'available_variables' => [
                    'nama_penerima'     => 'Nama Penerima Notifikasi',
                    'nama_pembatal'     => 'Nama Pihak yang Membatalkan',
                    'role_pembatal'     => 'Peran (Dosen Pembimbing / Mahasiswa)',
                    'tanggal_bimbingan' => 'Jadwal Bimbingan Awal',
                    'topik_bimbingan'   => 'Topik Bimbingan',
                    'alasan_pembatalan' => 'Alasan Pembatalan',
                    'link_mentoring'    => 'Tautan Halaman Bimbingan',
                ],
            ],
            [
                'code' => 'birthday_student',
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
            ],
            [
                'code' => 'birthday_lecturer',
                'name' => 'Ucapan Selamat Ulang Tahun (ke Dosen & Kaprodi)',
                'category' => 'Ulang Tahun',
                'content' => "🎂 *SELAMAT BERTAMBAH USIA, BPK/IBU {nama_dosen}!* 💐\n\nKeluarga Besar Civitas Akademika Fakultas Ilmu Komputer (FASILKOM) Universitas Subang mengucapkan selamat ulang tahun yang penuh berkah. ✨\n\nSemoga Allah SWT senantiasa melimpahkan kesehatan yang paripurna, kebahagiaan bersama keluarga tercinta, serta kelancaran dan kemudahan dalam mengemban amanah Tridharma Perguruan Tinggi.\n\nTerima kasih yang sebesar-besarnya atas bimbingan, dedikasi, dan ketulusan Bpk/Ibu dalam mendidik serta mengantarkan mahasiswa menuju masa depan yang gemilang. 👨‍🏫🎓\n\n_Salam hormat & takzim,_\n*SIBIMA FASILKOM UNSUB*",
                'available_variables' => [
                    'nama_dosen' => 'Nama Dosen / Kaprodi',
                    'umur'       => 'Usia / Umur',
                    'hari_ini'   => 'Tanggal Hari Ini',
                ],
            ],
            [
                'code' => 'skl_published',
                'name' => 'Penerbitan SKL Digital (ke Mahasiswa)',
                'category' => 'Yudisium',
                'content' => "🎓 *SURAT KETERANGAN LULUS (SKL) DITERBITKAN*\n\nHalo *{nama_mahasiswa}*,\n\nSelamat! Seluruh berkas bebas tanggungan pra-yudisium Anda telah diverifikasi dan disetujui oleh Program Studi.\n\n📄 *Nomor SKL:* {nomor_skl}\n📅 *Tanggal Kelulusan:* {tanggal_kelulusan}\n⭐ *Predikat:* {predikat_kelulusan}\n🎯 *IPK:* {ipk}\n\nSilakan login ke portal SIBIMA untuk mengunduh dokumen SKL digital resmi Anda:\n{link_skl}\n\n_Sistem Informasi Bimbingan Mahasiswa (SIBIMA) - FASILKOM UNSUB_",
                'available_variables' => [
                    'nama_mahasiswa'    => 'Nama Lengkap Mahasiswa',
                    'nomor_skl'         => 'Nomor Surat Keterangan Lulus',
                    'tanggal_kelulusan' => 'Tanggal Kelulusan / Yudisium',
                    'predikat_kelulusan'=> 'Predikat Kelulusan',
                    'ipk'               => 'Indeks Prestasi Kumulatif (IPK)',
                    'link_skl'          => 'Tautan Halaman Pra-Yudisium & Unduh SKL',
                ],
            ],
            [
                'code' => 'graduation_rejected',
                'name' => 'Perbaikan Berkas Bebas Tanggungan (ke Mahasiswa)',
                'category' => 'Yudisium',
                'content' => "⚠️ *PERBAIKAN BERKAS BEBAS TANGGUNGAN*\n\nHalo *{nama_mahasiswa}*,\n\nPengajuan berkas bebas tanggungan pra-yudisium Anda memerlukan perbaikan dari Program Studi / Fakultas.\n\n📝 *Catatan / Alasan Perbaikan:*\n\"{alasan_penolakan}\"\n\nSilakan periksa dan perbaiki berkas persyaratan Anda melalui portal SIBIMA:\n{link_skl}\n\nTerima kasih.\n_Sistem Informasi Bimbingan Mahasiswa (SIBIMA) - FASILKOM UNSUB_",
                'available_variables' => [
                    'nama_mahasiswa'    => 'Nama Mahasiswa',
                    'alasan_penolakan'  => 'Catatan / Alasan Perbaikan dari Petugas',
                    'link_skl'          => 'Tautan Halaman Pra-Yudisium',
                ],
            ],
        ];

        foreach ($templates as $data) {
            WaTemplate::updateOrCreate(['code' => $data['code']], $data);
        }

        WaTemplate::clearCache();
    }
}

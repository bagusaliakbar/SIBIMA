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
                'content' => "🔔 *JADWAL BIMBINGAN SKRIPSI BARU*\n\nHalo *{nama_mahasiswa}*,\n\nDosen Pembimbing Anda, *{nama_dosen}*, telah membuat jadwal bimbingan skripsi baru:\n\n📝 *Topik*: {topik_bimbingan}\n📅 *Waktu*: {tanggal_bimbingan} WIB\n📍 *Jenis/Lokasi*: {jenis_bimbingan}\n\n⚠️ *Penting*: Silakan buka sistem SIBIMA untuk melakukan *Konfirmasi Kehadiran* (Akan Hadir / Izin):\n{link_mentoring}\n\nTerima kasih.\n_Sistem Informasi Bimbingan Skripsi (SIBIMA)_",
                'available_variables' => [
                    'nama_mahasiswa' => 'Nama Mahasiswa',
                    'nama_dosen' => 'Nama Dosen Pembimbing',
                    'topik_bimbingan' => 'Topik Bimbingan',
                    'tanggal_bimbingan' => 'Waktu & Tanggal Bimbingan',
                    'jenis_bimbingan' => 'Jenis Bimbingan (Online / Offline) & Lokasi',
                    'link_mentoring' => 'Tautan ke Halaman Bimbingan',
                ],
            ],
            [
                'code' => 'mentoring_status_updated',
                'name' => 'Persetujuan / Penolakan Bimbingan (ke Mahasiswa)',
                'category' => 'Bimbingan',
                'content' => "Halo *{nama_mahasiswa}*,\n\nStatus pengajuan bimbingan skripsi Anda (Topik: *{topik_bimbingan}*) bersama *{nama_dosen}* telah **{status_bimbingan}**.\n\nCatatan/Alasan Dosen:\n\"{catatan_dosen}\"\n\nSilakan cek detail di dashboard SIBIMA:\n{link_mentoring}",
                'available_variables' => [
                    'nama_mahasiswa' => 'Nama Mahasiswa',
                    'nama_dosen' => 'Nama Dosen Pembimbing',
                    'topik_bimbingan' => 'Topik Bimbingan',
                    'status_bimbingan' => 'Status (DISETUJUI / DITOLAK / TIDAK HADIR)',
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
        ];

        foreach ($templates as $data) {
            WaTemplate::updateOrCreate(['code' => $data['code']], $data);
        }

        WaTemplate::clearCache();
    }
}

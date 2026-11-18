<?php

return [
    'current_version' => 'v2.5.0',

    'releases' => [
        [
            'version' => 'v2.5.0',
            'date' => 'November 2026',
            'badge' => 'Terbaru',
            'badge_type' => 'emerald',
            'title' => 'Kartu Bimbingan Digital, Footer & Penyempurnaan UX Bimbingan',
            'items' => [
                'Penambahan Kartu Bimbingan Digital bergaya kartu pengenal/KTM (sisi depan, kartu kendali 8 sesi, QR verifikasi, & cetak kartu) pada halaman Profil.',
                'Penyempurnaan tampilan jadwal bimbingan dosen & mahasiswa, logbook bimbingan, serta halaman daftar mahasiswa bimbingan.',
                'Penambahan opsi On/Off oleh Kaprodi & Admin untuk widget kapsul melayang (Floating Lapor Bug) pada manajemen Laporan Bug.',
                'Penyesuaian label pemberi persetujuan "Kaprodi (Bypass ACC)" pada notifikasi WhatsApp & in-app saat Kaprodi/Admin melakukan bypass ACC.',
                'Penambahan footer tipis non-intrusif lengkap dengan catatan versi interaktif.',
            ],
        ],
        [
            'version' => 'v2.4.0',
            'date' => 'Oktober 2026',
            'badge' => 'Rilis Fitur',
            'badge_type' => 'blue',
            'title' => 'Revisi Terstruktur, Penilaian Penguji & Sinkronisasi Repositori',
            'items' => [
                'Peningkatan alur revisi Seminar Proposal dan Sidang Skripsi berbasis poin beserta pratinjau berkas dan cetak berita acara.',
                'Penyempurnaan jadwal penguji seminar & sidang (status langsung, filter, serta ekspor PDF/Word).',
                'Integrasi katalog ilmiah, bookmark bacaan terorganisir, dan sinkronisasi arsip repositori universitas.',
            ],
        ],
        [
            'version' => 'v2.3.0',
            'date' => 'September 2026',
            'badge' => 'Stabilitas',
            'badge_type' => 'amber',
            'title' => 'Notifikasi WhatsApp Terjadwal, Monitoring Aktivitas & SK Pembimbing',
            'items' => [
                'Pengelolaan template WhatsApp dengan saklar global, broadcast terjadwal, dan proteksi anti-ban / circuit breaker.',
                'Pemantauan aktivitas bimbingan mingguan, mahasiswa pasif/kritis, serta penerbitan SK Pembimbing dengan tanda tangan QR digital.',
                'Penambahan widget papan ucapan ulang tahun civitas akademika dan sistem tiket Laporan Bug & Usulan Fitur.',
            ],
        ],
    ],
];

@php
    // Multi-path fallback resolution for stamp image
    if (empty($stampBase64)) {
        $stampCandidates = [
            public_path('images/stempel_fasilkom.png'),
            public_path('stempel_fasilkom.png'),
            base_path('public/images/stempel_fasilkom.png'),
            base_path('public/stempel_fasilkom.png'),
            resource_path('images/stempel_fasilkom.png'),
        ];
        foreach ($stampCandidates as $cand) {
            if (file_exists($cand) && is_readable($cand)) {
                $stampBase64 = base64_encode(file_get_contents($cand));
                break;
            }
        }
    }

    // Multi-path fallback resolution for logo image
    if (empty($logoBase64)) {
        $logoCandidates = [
            public_path('logo_unsub.png'),
            base_path('public/logo_unsub.png'),
            public_path('images/logo_unsub.png'),
            base_path('public/images/logo_unsub.png'),
        ];
        foreach ($logoCandidates as $cand) {
            if (file_exists($cand) && is_readable($cand)) {
                $logoBase64 = base64_encode(file_get_contents($cand));
                break;
            }
        }
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SK Dosen Pembimbing Skripsi - {{ $advisorDecree->decree_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.8cm 1.5cm 0.8cm 1.5cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.15;
            color: #000;
            margin: 0;
            padding: 0;
            font-size: 8pt;
        }

        .page-break {
            page-break-before: always;
        }

        /* ================= KOP SURAT RESMI ================= */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        .kop-table td {
            border: none !important;
            padding: 0 !important;
            vertical-align: middle;
        }

        .logo-cell {
            width: 70px;
            text-align: left;
        }

        .logo-img {
            width: 65px;
            height: auto;
            display: block;
        }

        .info-cell {
            text-align: center;
            padding-right: 70px !important;
        }

        .univ-title {
            font-size: 11.5pt;
            font-weight: normal;
            letter-spacing: 0.5px;
            margin: 0;
            padding: 0;
        }

        .fac-title {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 1px 0;
            padding: 0;
        }

        .akred-title {
            font-size: 7.5pt;
            font-weight: bold;
            margin: 1px 0;
        }

        .address-line {
            font-size: 7.5pt;
            margin: 1px 0;
        }

        .email-line {
            font-size: 7.5pt;
            margin: 1px 0;
        }

        .email-link {
            color: #0000ee;
            text-decoration: underline;
        }

        .kop-line {
            width: 100%;
            border-top: 2.5px solid #000;
            margin-top: 3px;
            margin-bottom: 8px;
        }

        /* ================= JUDUL DOKUMEN ================= */
        .doc-header {
            text-align: center;
            margin-bottom: 8px;
        }

        .header-bold {
            font-weight: bold;
            font-size: 8.5pt;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .header-title-lg {
            font-weight: bold;
            font-size: 9pt;
            line-height: 1.2;
            text-transform: uppercase;
        }

        /* ================= KONSIDERANS & DIKTUM ================= */
        .legal-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }

        .legal-table td {
            border: none !important;
            padding: 0.5px 0;
            vertical-align: top;
        }

        .label-col {
            width: 65px;
            font-weight: normal;
        }

        .sep-col {
            width: 10px;
            text-align: center;
        }

        .content-col {
            text-align: justify;
            line-height: 1.15;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
        }

        .item-table td {
            border: none !important;
            padding: 0.5px 0;
            vertical-align: top;
        }

        .item-char {
            width: 18px;
            text-align: left;
            vertical-align: top;
        }

        .item-text {
            text-align: justify;
            line-height: 1.15;
        }

        .memutuskan-title {
            text-align: center;
            font-weight: bold;
            font-size: 8.5pt;
            margin: 6px 0 4px 0;
        }

        /* ================= TANDA TANGAN ================= */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            border: none !important;
            padding: 0;
            vertical-align: top;
        }

        .signature-space {
            position: relative;
            height: 48px;
            margin: 2px 0;
        }

        .stamp-img {
            position: absolute;
            left: 15px;
            top: -8px;
            width: 85px;
            height: auto;
            opacity: 0.88;
            z-index: 10;
        }

        .signer-img {
            position: absolute;
            left: 40px;
            top: 0px;
            width: 100px;
            height: auto;
            z-index: 5;
        }

        /* SIBIMA Security QR Card */
        .qr-box {
            display: inline-block;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            border-radius: 4px;
            padding: 3px 5px;
            text-align: left;
        }

        /* ================= LAMPIRAN ================= */
        .lampiran-header {
            margin-bottom: 8px;
            font-size: 8.5pt;
        }

        .lampiran-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            line-height: 1.15;
        }

        .lampiran-table thead {
            display: table-header-group;
        }

        .lampiran-table tr {
            page-break-inside: avoid;
        }

        .lampiran-table th,
        .lampiran-table td {
            border: 1px solid #000;
            padding: 3px 5px;
        }

        .lampiran-table th {
            background-color: #cbd5e1;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }

        .footer-system {
            margin-top: 10px;
            font-size: 6pt;
            color: #64748b;
            text-align: center;
            border-top: 1px dashed #cbd5e1;
            padding-top: 2px;
        }
    </style>
</head>
<body>

    <!-- ================= HALAMAN 1: KOP SURAT, KONSIDERANS, DIKTUM, TTD ================= -->
    <div class="kop-surat">
        <table class="kop-table">
            <tr>
                <td class="logo-cell">
                    @if(!empty($logoBase64))
                        <img src="data:image/png;base64,{{ $logoBase64 }}" class="logo-img" alt="Logo UNSUB">
                    @elseif(file_exists(public_path('logo_unsub.png')))
                        <img src="{{ public_path('logo_unsub.png') }}" class="logo-img" alt="Logo UNSUB">
                    @endif
                </td>
                <td class="info-cell">
                    <div class="univ-title">UNIVERSITAS SUBANG</div>
                    <div class="fac-title">FAKULTAS ILMU KOMPUTER</div>
                    <div class="akred-title">Akreditasi: B SK BAN PT No: 6453/SK/BAN-PT/Akred/S/X/2020</div>
                    <div class="address-line">Jalan R.A Kartini KM 3 Telp (0260) 411415 Subang</div>
                    <div class="email-line">E-Mail : <span class="email-link">fasilkom@unsub.ac.id</span></div>
                </td>
            </tr>
        </table>
        <div class="kop-line"></div>
    </div>

    <!-- Judul & Perihal SK -->
    <div class="doc-header">
        <div class="header-bold">SURAT KEPUTUSAN DEKAN</div>
        <div class="header-bold">FAKULTAS ILMU KOMPUTER UNIVERSITAS SUBANG</div>
        <div class="header-bold">NOMOR : {{ $advisorDecree->decree_number }}</div>

        <div class="header-bold" style="margin: 4px 0;">TENTANG</div>

        <div class="header-title-lg">DOSEN PEMBIMBING TUGAS AKHIR</div>
        <div class="header-title-lg">FAKULTAS ILMU KOMPUTER UNIVERSITAS SUBANG</div>
        <div class="header-title-lg">SEMESTER {{ strtoupper($advisorDecree->semester) }} TAHUN AKADEMIK {{ $advisorDecree->academic_year }}</div>

        <div class="header-bold" style="margin-top: 5px;">DEKAN FAKULTAS ILMU KOMPUTER UNIVERSITAS SUBANG</div>
    </div>

    <!-- Konsiderans: Menimbang & Mengingat (Lengkap 1-14 Menyambung Tanpa Terpotong) -->
    <table class="legal-table">
        <tr>
            <td class="label-col">Menimbang</td>
            <td class="sep-col">:</td>
            <td class="content-col">
                <table class="item-table">
                    <tr>
                        <td class="item-char">a.</td>
                        <td class="item-text">Bahwa mahasiswa mempunyai hak untuk mendapat bimbingan dari dosen yang bertanggungjawab atas program studi yang diikutinya dalam penyelesaian studi;</td>
                    </tr>
                    <tr>
                        <td class="item-char">b.</td>
                        <td class="item-text">Bahwa mahasiswa sebagai bagian dari civitas akademika perlu lebih ditingkatkan kualitasnya serta diarahkan agar dapat menyelesaikan studi tepat pada waktunya;</td>
                    </tr>
                    <tr>
                        <td class="item-char">c.</td>
                        <td class="item-text">Bahwa untuk mewujudkan hal tersebut di atas, maka kepada mahasiswa sebagai peserta didik diberikan bimbingan akademik;</td>
                    </tr>
                    <tr>
                        <td class="item-char">d.</td>
                        <td class="item-text">Bahwa sesuai dengan hal-hal tersebut di atas, maka perlu ditetapkan persyaratan, tugas, wewenang Pembimbing Akademik dengan Keputusan Dekan Fakultas Ilmu Komputer Universitas Subang.</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="label-col" style="padding-top: 2px;">Mengingat</td>
            <td class="sep-col" style="padding-top: 2px;">:</td>
            <td class="content-col" style="padding-top: 2px;">
                <table class="item-table">
                    <tr>
                        <td class="item-char">1.</td>
                        <td class="item-text">Undang-Undang Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional;</td>
                    </tr>
                    <tr>
                        <td class="item-char">2.</td>
                        <td class="item-text">Undang-Undang Nomor 14 Tahun 2005 tentang Guru dan Dosen;</td>
                    </tr>
                    <tr>
                        <td class="item-char">3.</td>
                        <td class="item-text">Undang-Undang Nomor 12 Tahun 2012 tentang Perguruan Tinggi;</td>
                    </tr>
                    <tr>
                        <td class="item-char">4.</td>
                        <td class="item-text">Peraturan Pemerintah Nomor 37 Tahun 2009 tentang Dosen;</td>
                    </tr>
                    <tr>
                        <td class="item-char">5.</td>
                        <td class="item-text">Peraturan Pemerintah Nomor 4 Tahun 2014 tentang Penyelenggaraan Pendidikan dan Pengelolaan Perguruan Tinggi;</td>
                    </tr>
                    <tr>
                        <td class="item-char">6.</td>
                        <td class="item-text">Peraturan Pemerintah Nomor 4 Tahun 2022 tentang Perubahan atas Peraturan Pemerintah Nomor 57 Tahun 2021 tentang Standar Nasional Pendidikan;</td>
                    </tr>
                    <tr>
                        <td class="item-char">7.</td>
                        <td class="item-text">Peraturan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi Republik Indonesia Nomor 53 Tahun 2023 tentang Penjaminan Mutu Pendidikan Tinggi;</td>
                    </tr>
                    <tr>
                        <td class="item-char">8.</td>
                        <td class="item-text">Peraturan Menteri Pendidikan Nasional Republik Indonesia Nomor : 33/D/O/2005 tentang Penggabungan Sekolah Tinggi Ilmu Administrasi (STIA) Kutawaringin di Subang dan Sekolah Tinggi Teknologi (STT) Kutawaringin di Subang menjadi Universitas Subang serta Penambahan Program Studi Baru yang diselenggarakan oleh Yayasan Kutawaringin Subang di Subang;</td>
                    </tr>
                    <tr>
                        <td class="item-char">9.</td>
                        <td class="item-text">Peraturan Yayasan Kutawaringin Nomor 15 Tahun 2020 Tentang Penetapan Statuta Universitas Subang;</td>
                    </tr>
                    <tr>
                        <td class="item-char">10.</td>
                        <td class="item-text">Surat Keputusan Rektor Universitas Subang Nomor 25/US/X/2013 Tentang Penetapan Perubahan Struktur Organisasi dan Tata Kerja Universitas Subang;</td>
                    </tr>
                    <tr>
                        <td class="item-char">11.</td>
                        <td class="item-text">Surat Keputusan Rektor Nomor 68/US/VII/2022 tentang pedoman Penelitian dan Pengabdian kepada Masyarakat (PkM) Universitas Subang;</td>
                    </tr>
                    <tr>
                        <td class="item-char">12.</td>
                        <td class="item-text">Surat Keputusan Rektor Nomor 93/US/VII/2022 tentang Pedoman Akademik Universitas Subang;</td>
                    </tr>
                    <tr>
                        <td class="item-char">13.</td>
                        <td class="item-text">Surat Keputusan Rektor Nomor 71/US/XI/2023 tentang Pengangkatan Dekan Fakultas Ilmu Komputer Universitas Subang masa jabatan 2023-2027;</td>
                    </tr>
                    <tr>
                        <td class="item-char">14.</td>
                        <td class="item-text">Berdasarkan Keputusan LAM INFOKOM No. 110/SK/LAM-INFOKOM/Ak/S/VIII/2025, menyatakan bahwa program studi Sistem Informasi pada Program Sarjana Universitas Subang, Kab. Subang Memenuhi Syarat Peringkat AKREDITASI BAIK SEKALI, sejak tanggal 8 Agustus 2025 sampai dengan 8 Agustus 2030.</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="memutuskan-title">MEMUTUSKAN :</div>

    <table class="legal-table">
        <tr>
            <td class="label-col">Menetapkan</td>
            <td class="sep-col">:</td>
            <td class="content-col"></td>
        </tr>
        <tr>
            <td class="label-col" style="padding-top: 2px;">Pertama</td>
            <td class="sep-col" style="padding-top: 2px;">:</td>
            <td class="content-col" style="padding-top: 2px;">
                Menunjuk nama-nama dosen sebagai pembimbing tugas akhir pada Fakultas Ilmu Komputer Universitas Subang Semester {{ $advisorDecree->semester }} Tahun Akademik {{ $advisorDecree->academic_year }} sebagaimana tercantum pada lampiran Surat Keputusan ini
            </td>
        </tr>
        <tr>
            <td class="label-col" style="padding-top: 2px;">Kedua</td>
            <td class="sep-col" style="padding-top: 2px;">:</td>
            <td class="content-col" style="padding-top: 2px;">
                Prosedur bimbingan tugas akhir sesuai dengan pedoman penyusunan tugas akhir di Fakultas Ilmu Komputer Universitas Subang.
            </td>
        </tr>
        <tr>
            <td class="label-col" style="padding-top: 2px;">Ketiga</td>
            <td class="sep-col" style="padding-top: 2px;">:</td>
            <td class="content-col" style="padding-top: 2px;">
                Keputusan ini mulai berlaku sejak tanggal ditetapkan dan apabila terdapat kekeliruan dalam keputusan ini akan diperbaiki sebagaimana mestinya
            </td>
        </tr>
    </table>

    <!-- Tanda Tangan Dekan -->
    <table class="signature-table" style="margin-top: 8px;">
        <tr>
            <td style="width: 48%;"></td>
            <td style="width: 52%; text-align: center;">
                <div style="text-align: left; padding-left: 20px;">
                    <div>Ditetapkan di Subang</div>
                    <div><u>Pada Tanggal : {{ $advisorDecree->formatted_decree_date }}</u></div>
                </div>

                <div style="font-weight: bold; margin-top: 4px; line-height: 1.2; font-size: 8.5pt;">
                    DEKAN<br>
                    FAKULTAS ILMU KOMPUTER<br>
                    UNIVERSITAS SUBANG
                </div>

                <div class="signature-space">
                    @if(!empty($includeStamp) && !empty($stampBase64))
                        <img src="data:image/png;base64,{{ $stampBase64 }}" class="stamp-img" alt="Cap Fasilkom">
                    @endif
                    @if($signerUser && $signerUser->decrypted_signature)
                        <img src="{{ $signerUser->decrypted_signature }}" class="signer-img" alt="TTD Dekan">
                    @endif
                </div>

                <div style="font-weight: bold; font-size: 9pt; text-decoration: underline;">
                    {{ $advisorDecree->signatory_name ?? 'Dr. TEPI PEIRISAL, M.SI' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Tembusan & SIBIMA Official Security QR Card -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 8px;">
        <tr>
            <td style="width: 55%; vertical-align: bottom;">
                <div style="font-size: 7.5pt; line-height: 1.25;">
                    <strong>Tembusan :</strong><br>
                    1. Yayasan Kutawaringin Subang<br>
                    2. Rektor Universitas Subang<br>
                    3. Yang bersangkutan untuk diketahui dan seperlunya
                </div>
            </td>
            <td style="width: 45%; vertical-align: bottom; text-align: right;">
                <div class="qr-box">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 36px; text-align: center; vertical-align: middle; padding-right: 4px;">
                                <img src="data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(36)->margin(0)->generate(route('sk-pembimbing.verify', $advisorDecree->verification_token))) }}" style="width: 36px; height: 36px; display: block;">
                            </td>
                            <td style="text-align: left; vertical-align: middle; font-size: 5.5pt; color: #1e293b; line-height: 1.15;">
                                <strong style="color: #0f172a; font-size: 6pt;">VERIFIKASI RESMI SIBIMA</strong><br>
                                Dokumen sah terdaftar resmi di sistem.<br>
                                <span style="color: #64748b; font-size: 5pt;">Scan QR Code untuk verifikasi keaslian.</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- ================= HALAMAN 2: LAMPIRAN TABEL ================= -->
    <div class="page-break"></div>

    <div class="lampiran-header">
        <table style="width: 100%; border-collapse: collapse; font-size: 8.5pt; line-height: 1.25;">
            <tr>
                <td style="width: 65px; vertical-align: top;">Lampiran</td>
                <td style="width: 10px; vertical-align: top;">:</td>
                <td style="vertical-align: top; text-transform: uppercase;">
                    KEPUTUSAN DEKAN FAKULTAS ILMU KOMPUTER UNIVERSITAS SUBANG
                </td>
            </tr>
            <tr>
                <td></td>
                <td style="vertical-align: top;"></td>
                <td style="vertical-align: top;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 8.5pt;">
                        <tr>
                            <td style="width: 60px;">Nomor</td>
                            <td style="width: 10px;">:</td>
                            <td>{{ $advisorDecree->decree_number }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Tentang</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="text-transform: uppercase;">
                                DOSEN PEMBIMBING PENULISAN TUGAS AKHIR<br>
                                FAKULTAS ILMU KOMPUTER UNIVERSITAS SUBANG SEMESTER {{ strtoupper($advisorDecree->semester) }} TAHUN AKADEMIK {{ $advisorDecree->academic_year }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabel Daftar Pembimbing Skripsi -->
    <table class="lampiran-table">
        <thead>
            <tr>
                <th style="width: 25px;">NO</th>
                <th style="width: 75px;">NPM</th>
                <th style="width: 135px;">NAMA</th>
                <th>JUDUL TUGAS AKHIR</th>
                <th style="width: 110px;">PEMBIMBING I</th>
                <th style="width: 110px;">PEMBIMBING II</th>
            </tr>
        </thead>
        <tbody>
            @foreach($advisorDecree->theses_data as $idx => $row)
                <tr>
                    <td style="text-align: center; vertical-align: top;">{{ $idx + 1 }}</td>
                    <td style="text-align: left; vertical-align: top; font-family: 'Times New Roman', Times, serif;">
                        {{ strtoupper($row['student_npm'] ?? '') }}
                    </td>
                    <td style="text-align: left; vertical-align: top;">
                        {{ strtoupper($row['student_name'] ?? '') }}
                    </td>
                    <td style="text-align: justify; vertical-align: top; text-transform: uppercase;">
                        {{ (!empty($row['title']) && $row['title'] !== '-') ? $row['title'] : '' }}
                    </td>
                    <td style="text-align: left; vertical-align: top; text-transform: uppercase;">
                        {{ (!empty($row['pembimbing1_name']) && $row['pembimbing1_name'] !== '-') ? $row['pembimbing1_name'] : '' }}
                    </td>
                    <td style="text-align: left; vertical-align: top; text-transform: uppercase;">
                        {{ (!empty($row['pembimbing2_name']) && $row['pembimbing2_name'] !== '-') ? $row['pembimbing2_name'] : '' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tanda Tangan Dekan pada Lampiran -->
    <table class="signature-table" style="margin-top: 15px; page-break-inside: avoid;">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%; text-align: center;">
                <div style="font-weight: bold; line-height: 1.25; font-size: 8.5pt;">
                    DEKAN<br>
                    FAKULTAS ILMU KOMPUTER<br>
                    UNIVERSITAS SUBANG
                </div>

                <div class="signature-space">
                    @if(!empty($includeStamp) && !empty($stampBase64))
                        <img src="data:image/png;base64,{{ $stampBase64 }}" class="stamp-img" alt="Cap Fasilkom">
                    @endif
                    @if($signerUser && $signerUser->decrypted_signature)
                        <img src="{{ $signerUser->decrypted_signature }}" class="signer-img" alt="TTD Dekan">
                    @endif
                </div>

                <div style="font-weight: bold; font-size: 9pt; text-decoration: underline;">
                    {{ $advisorDecree->signatory_name ?? 'Dr. TEPI PEIRISAL, M.SI' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-system">
        Dokumen resmi diterbitkan melalui SIBIMA (Sistem Informasi Bimbingan Mahasiswa) FASILKOM UNSUB | Token Verifikasi: {{ substr($advisorDecree->verification_token, 0, 24) }}
    </div>

</body>
</html>

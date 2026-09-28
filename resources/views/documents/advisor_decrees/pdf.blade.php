<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SK Dosen Pembimbing Skripsi - {{ $advisorDecree->decree_number }}</title>
    <style>
        @page {
            margin: 1.5cm 2cm 1.5cm 2cm;
            size: A4 portrait;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
            font-size: 10pt;
        }

        /* Kop Surat Resmi FASILKOM UNSUB */
        .kop-surat {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-table td {
            border: none !important;
            padding: 0 !important;
            vertical-align: middle;
        }

        .logo-cell {
            width: 75px;
            text-align: left;
        }

        .logo-img {
            width: 68px;
            height: auto;
        }

        .info-cell {
            text-align: center;
            padding-right: 75px !important;
        }

        .univ-name {
            font-size: 15pt;
            margin: 0;
            padding: 0;
            letter-spacing: 0.5px;
        }

        .faculty-name {
            font-size: 17pt;
            margin: 1px 0;
            padding: 0;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .accreditation {
            font-size: 9pt;
            font-weight: bold;
            margin: 1px 0;
        }

        .address, .email {
            font-size: 8.5pt;
            margin: 1px 0;
        }

        /* Judul Dokumen */
        .title-block {
            text-align: center;
            margin-bottom: 15px;
        }

        .title-block h2 {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }

        .title-block .decree-no {
            margin: 2px 0 6px 0;
            font-size: 10pt;
            font-weight: bold;
        }

        .title-block .subject {
            margin: 0;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.25;
        }

        /* Konsiderans */
        .consider-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9.5pt;
        }

        .consider-table td {
            border: none !important;
            padding: 2px 0;
            vertical-align: top;
        }

        .consider-label {
            width: 100px;
            font-weight: bold;
        }

        .consider-sep {
            width: 15px;
            text-align: center;
            font-weight: bold;
        }

        .consider-content {
            text-align: justify;
            line-height: 1.25;
        }

        .consider-list {
            margin: 0;
            padding-left: 18px;
        }

        .consider-list li {
            margin-bottom: 2px;
            text-align: justify;
        }

        /* Dictum / Memutuskan */
        .dictum-title {
            text-align: center;
            font-weight: bold;
            margin: 8px 0 4px 0;
            font-size: 10pt;
        }

        .dictum-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 12px;
        }

        .dictum-table td {
            border: none !important;
            padding: 2px 0;
            vertical-align: top;
        }

        .dictum-label {
            width: 100px;
            font-weight: bold;
        }

        .dictum-sep {
            width: 15px;
            text-align: center;
            font-weight: bold;
        }

        .dictum-content {
            text-align: justify;
            line-height: 1.25;
        }

        /* Main Table Lampiran */
        .page-break {
            page-break-before: always;
        }

        .attachment-header {
            margin-bottom: 12px;
            font-size: 9pt;
        }

        .attachment-header table {
            width: 100%;
            border-collapse: collapse;
        }

        .attachment-header td {
            border: none;
            padding: 1px 0;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 8.5pt;
        }

        .main-table th, .main-table td {
            border: 1px solid black;
            padding: 4px 6px;
            vertical-align: top;
        }

        .main-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
            font-size: 8.5pt;
        }

        /* Signature Block */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .signature-table td {
            border: none !important;
            vertical-align: top;
        }

        .qr-section {
            width: 50%;
        }

        .qr-card {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            width: 250px;
            background-color: #f8fafc;
        }

        .qr-inner-table {
            width: 100%;
            border-collapse: collapse;
        }

        .qr-inner-table td {
            border: none !important;
            padding: 0;
            vertical-align: middle;
        }

        .sign-section {
            width: 50%;
            text-align: left;
            padding-left: 30px;
            font-size: 9.5pt;
        }

        .date-line {
            margin-bottom: 2px;
        }

        .signer-title {
            font-weight: bold;
            margin-bottom: 4px;
        }

        .signature-space {
            height: 48px;
            margin: 4px 0;
            position: relative;
        }

        .stamp-overlay {
            position: absolute;
            top: -12px;
            left: -25px;
            width: 80px;
            height: 80px;
            z-index: 1;
        }

        .signer-img {
            position: relative;
            max-height: 46px;
            max-width: 130px;
            display: block;
            z-index: 2;
        }

        .signer-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 10pt;
        }

        .signer-nip {
            font-size: 9pt;
            margin-top: 1px;
        }

        .footer-system {
            margin-top: 25px;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
            font-size: 7pt;
            color: #64748b;
            text-align: justify;
        }
    </style>
</head>
<body>
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

    <!-- ================= HALAMAN 1: SURAT KEPUTUSAN ================= -->
    <div class="kop-surat">
        <table class="kop-table">
            <tr>
                <td class="logo-cell">
                    @if(!empty($logoBase64))
                        <img src="data:image/png;base64,{{ $logoBase64 }}" class="logo-img">
                    @endif
                </td>
                <td class="info-cell">
                    <div class="univ-name">UNIVERSITAS SUBANG</div>
                    <div class="faculty-name">FAKULTAS ILMU KOMPUTER</div>
                    <div class="accreditation">Akreditasi BAIK SEKALI No. 110/SK/LAM-INFOKOM/Ak/S/VIII/2025</div>
                    <div class="address">Jalan R.A Kartini KM 3 Telp (0260) 411415 Subang</div>
                    <div class="email">E-Mail: <span style="color: blue; text-decoration: underline;">fasilkom@unsub.ac.id</span></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="title-block">
        <h2>SURAT KEPUTUSAN DEKAN FAKULTAS ILMU KOMPUTER</h2>
        <div class="decree-no">Nomor: {{ $advisorDecree->decree_number }}</div>
        <div class="subject">
            TENTANG<br>
            PENETAPAN DOSEN PEMBIMBING SKRIPSI MAHASISWA<br>
            FAKULTAS ILMU KOMPUTER UNIVERSITAS SUBANG<br>
            TAHUN AKADEMIK {{ $advisorDecree->academic_year }} SEMESTER {{ strtoupper($advisorDecree->semester) }}
        </div>
    </div>

    <!-- Preamble / Konsiderans -->
    <table class="consider-table">
        <tr>
            <td class="consider-label">Menimbang</td>
            <td class="consider-sep">:</td>
            <td class="consider-content">
                <ol type="a" class="consider-list">
                    <li>bahwa dalam rangka penyelesaian tugas akhir/skripsi sebagai syarat kelulusan mahasiswa Fakultas Ilmu Komputer Universitas Subang, dipandang perlu menetapkan Dosen Pembimbing Skripsi;</li>
                    <li>bahwa dosen yang namanya tercantum dalam lampiran keputusan ini dipandang cakap dan memenuhi syarat akademis serta kompetensi keilmuan untuk ditugaskan sebagai Dosen Pembimbing Skripsi;</li>
                    <li>bahwa berdasarkan pertimbangan sebagaimana dimaksud pada huruf a dan b, perlu diterbitkan Surat Keputusan Dekan.</li>
                </ol>
            </td>
        </tr>
        <tr>
            <td class="consider-label">Mengingat</td>
            <td class="consider-sep">:</td>
            <td class="consider-content">
                <ol class="consider-list">
                    <li>Undang-Undang Republik Indonesia Nomor 12 Tahun 2012 tentang Pendidikan Tinggi;</li>
                    <li>Peraturan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi tentang Standar Nasional Pendidikan Tinggi;</li>
                    <li>Statuta Universitas Subang;</li>
                    <li>Buku Pedoman Akademik dan Pedoman Penyusunan Skripsi Fakultas Ilmu Komputer Universitas Subang.</li>
                </ol>
            </td>
        </tr>
    </table>

    <div class="dictum-title">MEMUTUSKAN</div>

    <table class="dictum-table">
        <tr>
            <td class="dictum-label">Menetapkan</td>
            <td class="dictum-sep">:</td>
            <td class="dictum-content">
                <strong>KEPUTUSAN DEKAN FAKULTAS ILMU KOMPUTER TENTANG PENETAPAN DOSEN PEMBIMBING SKRIPSI MAHASISWA TAHUN AKADEMIK {{ $advisorDecree->academic_year }}.</strong>
            </td>
        </tr>
        <tr>
            <td class="dictum-label">KESATU</td>
            <td class="dictum-sep">:</td>
            <td class="dictum-content">
                Menetapkan Dosen Pembimbing 1 dan Dosen Pembimbing 2 Skripsi bagi mahasiswa Fakultas Ilmu Komputer Universitas Subang sebagaimana tercantum dalam lampiran yang merupakan bagian tidak terpisahkan dari keputusan ini.
            </td>
        </tr>
        <tr>
            <td class="dictum-label">KEDUA</td>
            <td class="dictum-sep">:</td>
            <td class="dictum-content">
                Dosen Pembimbing bertugas mengarahkan, membimbing, dan memantau penyusunan naskah skripsi mahasiswa terkait materi ilmiah, metodologi penelitian, sistematika penulisan, dan implementasi sistem/program, serta memvalidasi keaktifan logbook bimbingan.
            </td>
        </tr>
        <tr>
            <td class="dictum-label">KETIGA</td>
            <td class="dictum-sep">:</td>
            <td class="dictum-content">
                Surat Keputusan ini berlaku untuk Tahun Akademik {{ $advisorDecree->academic_year }} sejak tanggal ditetapkan, dengan ketentuan apabila di kemudian hari terdapat kekeliruan dalam penetapan ini akan diperbaiki sebagaimana mestinya.
            </td>
        </tr>
    </table>

    <!-- Tanda Tangan Dekan / Pejabat -->
    <table class="signature-table">
        <tr>
            <td class="qr-section">
                <div class="qr-card">
                    <table class="qr-inner-table">
                        <tr>
                            <td style="width: 52px; padding-right: 6px; text-align: center;">
                                <img src="data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(50)->margin(0)->generate(route('sk-pembimbing.verify', $advisorDecree->verification_token))) }}" style="width: 50px; height: 50px; display: block;">
                            </td>
                            <td>
                                <div style="font-size: 7.5pt; font-weight: bold; color: #0f172a;">DOKUMEN RESMI DIGITAL</div>
                                <div style="font-size: 6.5pt; color: #334155; line-height: 1.2; margin-top: 1px;">
                                    Surat Keputusan sah dan terdaftar resmi di sistem SIBIMA FASILKOM UNSUB.
                                </div>
                                <div style="font-size: 6pt; color: #64748b; margin-top: 2px; font-style: italic;">
                                    Pindai QR untuk verifikasi keaslian dokumen.
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td class="sign-section">
                <div class="date-line">Ditetapkan di: Subang</div>
                <div class="date-line">Pada Tanggal: {{ $advisorDecree->formatted_decree_date }}</div>
                <div class="signer-title">{{ $advisorDecree->signatory_title }},</div>
                <div class="signature-space">
                    @if(!empty($includeStamp) && !empty($stampBase64))
                        <img src="data:image/png;base64,{{ $stampBase64 }}" class="stamp-overlay">
                    @endif
                    @if($signerUser && $signerUser->decrypted_signature)
                        <img src="{{ $signerUser->decrypted_signature }}" class="signer-img">
                    @endif
                </div>
                <div class="signer-name">{{ $advisorDecree->signatory_name }}</div>
                @if($advisorDecree->signatory_identifier)
                    <div class="signer-nip">NIDN/NIP: {{ $advisorDecree->signatory_identifier }}</div>
                @endif
            </td>
        </tr>
    </table>

    <div class="footer-system">
        Dokumen resmi diterbitkan melalui SIBIMA (Sistem Informasi Bimbingan Mahasiswa) FASILKOM UNSUB | Token Verifikasi: {{ substr($advisorDecree->verification_token, 0, 20) }}...
    </div>

    <!-- ================= HALAMAN 2: LAMPIRAN TABEL ================= -->
    <div class="page-break"></div>

    <div class="attachment-header">
        <table>
            <tr>
                <td style="width: 55%;"></td>
                <td style="width: 45%;">
                    <strong>LAMPIRAN:</strong><br>
                    SURAT KEPUTUSAN DEKAN FAKULTAS ILMU KOMPUTER<br>
                    NOMOR&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $advisorDecree->decree_number }}<br>
                    TANGGAL&nbsp;: {{ $advisorDecree->formatted_decree_date }}<br>
                    TENTANG&nbsp;&nbsp;: PENETAPAN DOSEN PEMBIMBING SKRIPSI MAHASISWA
                </td>
            </tr>
        </table>
    </div>

    <div style="text-align: center; font-weight: bold; font-size: 10pt; margin-bottom: 8px;">
        DAFTAR PENETAPAN DOSEN PEMBIMBING SKRIPSI MAHASISWA<br>
        TAHUN AKADEMIK {{ $advisorDecree->academic_year }} SEMESTER {{ strtoupper($advisorDecree->semester) }}
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 22px;">No</th>
                <th style="width: 135px;">Mahasiswa / NPM</th>
                <th>Judul Skripsi</th>
                <th style="width: 140px;">Pembimbing 1</th>
                <th style="width: 140px;">Pembimbing 2</th>
            </tr>
        </thead>
        <tbody>
            @foreach($advisorDecree->theses_data as $idx => $row)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $row['student_name'] ?? '-' }}</strong><br>
                        <span style="font-size: 8pt; color: #333;">NPM: {{ $row['student_npm'] ?? '-' }}</span>
                        @if(!empty($row['student_cohort']))
                            <br><span style="font-size: 7.5pt; color: #555;">(Angkatan {{ $row['student_cohort'] }})</span>
                        @endif
                    </td>
                    <td style="font-style: italic;">
                        {{ $row['title'] ?? '-' }}
                    </td>
                    <td>
                        <strong>{{ $row['pembimbing1_name'] ?? '-' }}</strong><br>
                        <span style="font-size: 7.5pt; color: #444;">NIDN: {{ $row['pembimbing1_nidn'] ?? '-' }}</span>
                    </td>
                    <td>
                        <strong>{{ $row['pembimbing2_name'] ?? '-' }}</strong><br>
                        <span style="font-size: 7.5pt; color: #444;">NIDN: {{ $row['pembimbing2_nidn'] ?? '-' }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signature-table" style="margin-top: 15px;">
        <tr>
            <td style="width: 55%;"></td>
            <td style="width: 45%; text-align: left; padding-left: 20px;">
                <div class="date-line">Subang, {{ $advisorDecree->formatted_decree_date }}</div>
                <div class="signer-title">{{ $advisorDecree->signatory_title }},</div>
                <div class="signature-space">
                    @if(!empty($includeStamp) && !empty($stampBase64))
                        <img src="data:image/png;base64,{{ $stampBase64 }}" class="stamp-overlay">
                    @endif
                    @if($signerUser && $signerUser->decrypted_signature)
                        <img src="{{ $signerUser->decrypted_signature }}" class="signer-img">
                    @endif
                </div>
                <div class="signer-name">{{ $advisorDecree->signatory_name }}</div>
                @if($advisorDecree->signatory_identifier)
                    <div class="signer-nip">NIDN/NIP: {{ $advisorDecree->signatory_identifier }}</div>
                @endif
            </td>
        </tr>
    </table>
</body>
</html>

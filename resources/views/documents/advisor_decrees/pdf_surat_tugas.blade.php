<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Tugas Pembimbing - {{ $advisorDecree->decree_number }}</title>
    <style>
        @page {
            margin: 1.2cm 2cm 1.5cm 2cm;
            size: A4 portrait;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.35;
            color: #000;
            margin: 0;
            padding: 0;
            font-size: 11pt;
        }

        /* Kop Surat Resmi FASILKOM UNSUB */
        .kop-surat {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 4px;
            margin-bottom: 16px;
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
            font-size: 14pt;
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
            font-size: 8.5pt;
            font-weight: bold;
            margin: 1px 0;
        }

        .address, .email {
            font-size: 8pt;
            margin: 1px 0;
        }

        /* Judul Surat Tugas */
        .title-block {
            text-align: center;
            margin-bottom: 16px;
        }

        .title-block h2 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .title-block .doc-no {
            margin-top: 3px;
            font-size: 10.5pt;
            font-weight: normal;
        }

        .preamble {
            text-align: justify;
            margin-bottom: 12px;
            line-height: 1.4;
            font-size: 10.5pt;
        }

        /* Tabel Data Dosen */
        .lecturer-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 10.5pt;
        }

        .lecturer-table td {
            border: none !important;
            padding: 2px 0;
            vertical-align: top;
        }

        .lecturer-label {
            width: 220px;
        }

        .lecturer-sep {
            width: 15px;
            text-align: center;
        }

        .lecturer-val {
            text-align: left;
        }

        /* Tabel Mahasiswa Bimbingan */
        .theses-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 10pt;
        }

        .theses-table th, .theses-table td {
            border: 1px solid #000;
            padding: 5px 8px;
        }

        .theses-table th {
            text-align: center;
            font-weight: bold;
        }

        .theses-table td.col-no {
            width: 35px;
            text-align: center;
        }

        .theses-table td.col-name {
            text-align: left;
        }

        .theses-table td.col-npm {
            width: 120px;
            text-align: center;
        }

        .theses-table td.col-role {
            width: 130px;
            text-align: center;
        }

        .closing {
            margin-top: 14px;
            margin-bottom: 22px;
            font-size: 10.5pt;
            text-align: justify;
        }

        /* Signature block */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            border: none !important;
            padding: 0;
            vertical-align: top;
        }

        .sign-left {
            width: 50%;
            padding-right: 15px;
        }

        .sign-right {
            width: 50%;
            text-align: left;
            padding-left: 30px;
            font-size: 10pt;
            line-height: 1.35;
        }

        .signer-title {
            margin-bottom: 2px;
        }

        .signature-space {
            height: 65px;
            position: relative;
            margin-top: 4px;
            margin-bottom: 4px;
        }

        .stamp-overlay {
            position: absolute;
            top: -12px;
            left: -28px;
            width: 95px;
            height: 95px;
            z-index: 1;
        }

        .signer-img {
            position: relative;
            max-height: 55px;
            max-width: 140px;
            display: block;
            z-index: 2;
        }

        .qr-card {
            border: 1px dashed #cbd5e1;
            padding: 6px;
            border-radius: 6px;
            background-color: #f8fafc;
            display: inline-block;
        }

        .footer-system {
            margin-top: 25px;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
            font-size: 7.5pt;
            color: #64748b;
            text-align: justify;
        }
    </style>
</head>
<body>
    @php
        // Resolve Dosen
        $dosenUser = $advisorDecree->dosen ?? null;
        $dosenName = $dosenUser ? $dosenUser->name : ($advisorDecree->theses_data[0]['pembimbing1_name'] ?? 'Dosen Pembimbing');
        $dosenNidn = $dosenUser ? ($dosenUser->identifier ?? '-') : ($advisorDecree->theses_data[0]['pembimbing1_nidn'] ?? '-');

        // Segregate into Pembimbing I and Pembimbing II
        $theses = $advisorDecree->theses_data ?? [];
        $p1 = [];
        $p2 = [];
        foreach ($theses as $item) {
            $isP1 = false;
            if ($dosenUser && !empty($item['pembimbing1_id']) && $item['pembimbing1_id'] == $dosenUser->id) {
                $isP1 = true;
            } elseif (!empty($item['pembimbing1_name']) && str_contains(strtolower($item['pembimbing1_name']), strtolower($dosenName))) {
                $isP1 = true;
            } elseif (!empty($item['role_label']) && str_contains(strtolower($item['role_label']), 'pembimbing i') && !str_contains(strtolower($item['role_label']), 'ii')) {
                $isP1 = true;
            }

            if ($isP1) {
                $p1[] = array_merge($item, ['role_text' => 'Pembimbing I']);
            } else {
                $p2[] = array_merge($item, ['role_text' => 'Pembimbing II']);
            }
        }
        $sortedList = array_merge($p1, $p2);
        if (empty($sortedList)) {
            $sortedList = $theses;
        }
    @endphp

    <!-- Kop Surat Resmi FASILKOM UNSUB -->
    <div class="kop-surat">
        <table class="kop-table">
            <tr>
                <td class="logo-cell">
                    @if(file_exists(public_path('logo_unsub.png')))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo_unsub.png'))) }}" class="logo-img">
                    @endif
                </td>
                <td class="info-cell">
                    <div class="univ-name">UNIVERSITAS SUBANG</div>
                    <div class="faculty-name">FAKULTAS ILMU KOMPUTER</div>
                    <div class="accreditation">Akreditasi BAIK SEKALI No. 110/SK/LAM-INFOKOM/Ak/S/VIII/2025</div>
                    <div class="address">Jalan R.A Kartini KM 3 Telp (0260) 411415 Subang</div>
                    <div class="email">E-Mail : <span style="color: blue; text-decoration: underline;">fasilkom@unsub.ac.id</span></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Judul Dokumen -->
    <div class="title-block">
        <h2>SURAT TUGAS</h2>
        <div class="doc-no">Nomor : {{ $advisorDecree->decree_number }}</div>
    </div>

    <!-- Preamble -->
    <div class="preamble">
        Yang bertanda tangan di bawah ini {{ $advisorDecree->signatory_title ?? 'Wakil Dekan Fakultas Ilmu Komputer Universitas Subang' }} dengan ini menugaskan :
    </div>

    <!-- Identitas Dosen -->
    <table class="lecturer-table">
        <tr>
            <td class="lecturer-label">N A M A</td>
            <td class="lecturer-sep">:</td>
            <td class="lecturer-val"><strong>{{ strtoupper($dosenName) }}</strong></td>
        </tr>
        <tr>
            <td class="lecturer-label">STATUS TENAGA PENGAJAR</td>
            <td class="lecturer-sep">:</td>
            <td class="lecturer-val">Dosen Tetap</td>
        </tr>
        <tr>
            <td class="lecturer-label">NIDN</td>
            <td class="lecturer-sep">:</td>
            <td class="lecturer-val">{{ $dosenNidn }}</td>
        </tr>
        <tr>
            <td class="lecturer-label">PROGRAM STUDI</td>
            <td class="lecturer-sep">:</td>
            <td class="lecturer-val">Sistem Informasi</td>
        </tr>
        <tr>
            <td class="lecturer-label">SEMESTER</td>
            <td class="lecturer-sep">:</td>
            <td class="lecturer-val">{{ ucfirst($advisorDecree->semester) }}</td>
        </tr>
        <tr>
            <td class="lecturer-label">TAHUN AKADEMIK</td>
            <td class="lecturer-sep">:</td>
            <td class="lecturer-val">{{ $advisorDecree->academic_year }}</td>
        </tr>
    </table>

    <!-- Tabel Daftar Mahasiswa Bimbingan -->
    <table class="theses-table">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-name">Nama</th>
                <th class="col-npm">NPM</th>
                <th class="col-role">Pembimbing</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sortedList as $idx => $st)
                <tr>
                    <td class="col-no">{{ $idx + 1 }}.</td>
                    <td class="col-name">{{ $st['student_name'] ?? '-' }}</td>
                    <td class="col-npm">{{ $st['student_npm'] ?? '-' }}</td>
                    <td class="col-role">{{ $st['role_text'] ?? ($st['role_label'] ?? 'Pembimbing I') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Penutup -->
    <div class="closing">
        Demikian Surat Tugas ini kami sampaikan atas perhatiannya kami ucapkan terimakasih.
    </div>

    <!-- Tanda Tangan & QR Code -->
    <table class="signature-table">
        <tr>
            <td class="sign-left">
                <!-- QR Code Verifikasi Resmi SIBIMA -->
                <div class="qr-card">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 50px; text-align: center; vertical-align: middle; border: none !important;">
                                <img src="data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(48)->margin(0)->generate(route('sk-pembimbing.verify', $advisorDecree->verification_token))) }}" style="width: 48px; height: 48px; display: block;">
                            </td>
                            <td style="padding-left: 8px; vertical-align: middle; border: none !important; font-size: 7.5pt; color: #1e293b; line-height: 1.25;">
                                <strong>VERIFIKASI RESMI SIBIMA</strong><br>
                                Dokumen sah terdaftar secara digital.<br>
                                <span style="font-size: 6.5pt; color: #64748b;">Scan QR Code untuk verifikasi keaslian surat.</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td class="sign-right">
                <div>Subang, {{ $advisorDecree->formatted_decree_date }}</div>
                <div>UNIVERSITAS SUBANG</div>
                <div>Fakultas Ilmu Komputer</div>
                <div class="signer-title">Wakil Dekan I,</div>
                
                <div class="signature-space">
                    @if(!empty($includeStamp) && file_exists(public_path('images/stempel_fasilkom.png')))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/stempel_fasilkom.png'))) }}" class="stamp-overlay">
                    @endif
                    @if($signerUser && $signerUser->decrypted_signature)
                        <img src="{{ $signerUser->decrypted_signature }}" class="signer-img">
                    @endif
                </div>

                <div class="signer-name">{{ $advisorDecree->signatory_name ?? 'BAMBANG TJAHJO UTOMO, MT' }}</div>
            </td>
        </tr>
    </table>

    <div class="footer-system">
        Dokumen Surat Tugas resmi diterbitkan secara mandiri melalui SIBIMA FASILKOM UNSUB | Token Verifikasi: {{ substr($advisorDecree->verification_token, 0, 24) }}
    </div>
</body>
</html>

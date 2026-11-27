<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Pendaftaran Seminar Skripsi/Tugas Akhir</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 16mm 12mm 16mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000000;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        /* KOP SURAT RESMI FASILKOM UNSUB */
        .kop-surat {
            width: 100%;
            margin-bottom: 10px;
            text-align: center;
        }

        .kop-img {
            width: 100%;
            height: auto;
            display: block;
        }

        .kop-table-wrapper {
            border-bottom: 3px double #000000;
            padding-bottom: 5px;
        }

        .kop-surat table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-surat td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .kop-surat .logo-cell {
            width: 78px;
            text-align: left;
        }

        .kop-surat .logo-img {
            width: 68px;
            height: auto;
        }

        .kop-surat .info-cell {
            text-align: center;
            padding-right: 78px;
        }

        .kop-surat .univ-name {
            font-size: 14pt;
            margin: 0;
        }

        .kop-surat .faculty-name {
            font-size: 17pt;
            font-weight: bold;
            margin: 0;
        }

        .kop-surat .accreditation {
            font-size: 9pt;
            font-weight: bold;
            margin: 1px 0;
        }

        .kop-surat .address,
        .kop-surat .email {
            font-size: 8.5pt;
            margin: 0;
        }

        /* JUDUL FORMULIR */
        .form-header {
            text-align: center;
            margin-top: 4px;
            margin-bottom: 12px;
        }

        .form-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 0.2px;
        }

        .form-subtitle {
            font-size: 10.5pt;
            margin: 2px 0 0 0;
        }

        /* SECTION HEADING */
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            margin: 10px 0 3px 0;
            text-transform: uppercase;
        }

        .intro-text {
            font-size: 10.5pt;
            margin: 0 0 8px 0;
            line-height: 1.35;
        }

        /* A. DATA PRIBADI TABLE */
        .personal-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .personal-table td {
            padding: 3px 0;
            vertical-align: bottom;
            font-size: 10.5pt;
        }

        .personal-table .col-label {
            width: 168px;
            white-space: nowrap;
        }

        .personal-table .col-colon {
            width: 14px;
            text-align: center;
        }

        .personal-table .col-value {
            border-bottom: 1px solid #000000;
            padding: 2px 4px 2px 4px;
            font-weight: normal;
            line-height: 1.35;
        }

        .address-sub-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }

        .address-sub-table td {
            padding: 2px 0 1px 0;
            font-size: 10.5pt;
            vertical-align: bottom;
        }

        .inline-line {
            border-bottom: 1px solid #000000;
            padding: 0 4px 1px 4px;
        }

        /* B. DATA KESIAPAN SEMINAR TABLE */
        .readiness-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        .readiness-table th,
        .readiness-table td {
            border: 1px solid #000000;
            padding: 6px 8px;
            font-size: 10.5pt;
            vertical-align: top;
        }

        .readiness-no {
            width: 22px;
            text-align: center;
        }

        .readiness-label {
            width: 158px;
        }

        .thesis-title-cell {
            font-weight: bold;
            line-height: 1.45;
            min-height: 54px;
            text-transform: uppercase;
        }

        .statement-cell {
            text-align: justify;
            line-height: 1.4;
        }

        /* MAHASISWA SIGNATURE + MATERAI */
        .student-sign-cell {
            text-align: center;
            padding: 8px 12px 10px 12px !important;
        }

        .date-line {
            text-align: right;
            font-size: 10pt;
            margin-bottom: 2px;
        }

        .sign-role-title {
            font-weight: bold;
            font-size: 10.5pt;
            margin: 0 0 6px 0;
        }

        .materai-layout {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0 4px 0;
        }

        .materai-layout td {
            border: none !important;
            padding: 0 !important;
            vertical-align: middle;
        }

        .materai-box {
            width: 82px;
            height: 44px;
            border: 1px solid #000000;
            text-align: center;
            vertical-align: middle;
            font-size: 9pt;
            font-weight: bold;
            line-height: 1.25;
            padding-top: 10px;
            box-sizing: border-box;
            margin: 6px 0 6px auto;
        }

        .student-sign-name {
            display: inline-block;
            min-width: 220px;
            border-bottom: 1px solid #000000;
            padding: 0 10px 2px 10px;
            font-weight: bold;
            font-size: 10.5pt;
            text-transform: uppercase;
        }

        .student-sign-npm {
            font-size: 9.5pt;
            margin-top: 2px;
        }

        /* MENYETUJUI & SUPERVISOR SIGNATURES */
        .approval-header-cell {
            text-align: center;
            padding: 4px 8px !important;
            font-size: 10.5pt;
        }

        .supervisors-wrapper-cell {
            padding: 0 !important;
        }

        .supervisors-table {
            width: 100%;
            border-collapse: collapse;
        }

        .supervisors-table td {
            width: 50%;
            border: none !important;
            text-align: center;
            vertical-align: top;
            padding: 8px 10px 10px 10px !important;
        }

        .supervisors-table td.left-col {
            border-right: 1px solid #000000 !important;
        }

        .sig-area {
            height: 68px;
            vertical-align: middle;
            margin: 4px 0 4px 0;
        }

        .sig-inner-table {
            margin: 0 auto;
            border-collapse: collapse;
        }

        .sig-inner-table td {
            width: auto !important;
            border: none !important;
            padding: 0 4px !important;
            vertical-align: middle !important;
        }

        .sig-img {
            max-height: 50px;
            max-width: 118px;
            width: auto;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .qr-img {
            width: 38px;
            height: 38px;
            display: block;
        }

        .digital-caption {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 6.5pt;
            color: #334155;
            margin-top: 2px;
        }

        .dosen-name-line {
            display: inline-block;
            min-width: 180px;
            border-bottom: 1px solid #000000;
            padding: 0 6px 2px 6px;
            font-weight: bold;
            font-size: 10pt;
        }

        .dosen-id-line {
            font-size: 8.5pt;
            color: #1e293b;
            margin-top: 2px;
        }

        /* FOOTER META */
        .doc-footer {
            margin-top: 8px;
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 7pt;
            color: #475569;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $canEmbedPng = extension_loaded('gd');
        $kopBase64 = null;
        if ($canEmbedPng) {
            $kopCandidates = [
                public_path('images/kop_fasilkom.png'),
                public_path('kop_fasilkom.png'),
                base_path('public/images/kop_fasilkom.png'),
                base_path('public/kop_fasilkom.png'),
                resource_path('images/kop_fasilkom.png'),
            ];
            foreach ($kopCandidates as $cand) {
                if (file_exists($cand) && is_readable($cand)) {
                    $kopBase64 = base64_encode(file_get_contents($cand));
                    break;
                }
            }
        }

        $birthDateFormatted = '-';
        if (!empty($formData['birth_date'])) {
            try {
                $birthDateFormatted = \Carbon\Carbon::parse($formData['birth_date'])
                    ->locale('id')
                    ->translatedFormat('d F Y');
            } catch (\Throwable $e) {
                $birthDateFormatted = (string) $formData['birth_date'];
            }
        }

        $birthPlaceUpper = mb_strtoupper(trim((string) ($formData['birth_place'] ?? '-')));
        $ttlDisplay = ($birthPlaceUpper !== '-' && $birthPlaceUpper !== '')
            ? ($birthPlaceUpper . ', ' . mb_strtoupper($birthDateFormatted))
            : mb_strtoupper($birthDateFormatted);

        $submittedCarbon = $application->statement_agreed_at ?? $application->created_at ?? now();
        $submittedDateFormatted = $submittedCarbon->copy()->locale('id')->translatedFormat('d F Y');
        $submittedDateTime = $submittedCarbon->copy()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB';

        $semesterText = !empty($formData['semester_number']) && $formData['semester_number'] !== '-'
            ? 'SEMESTER ' . $formData['semester_number']
            : '-';
        $academicYearUpper = mb_strtoupper((string) ($formData['academic_year'] ?? '-'));
        $semesterAcademicDisplay = $semesterText . ' / ' . $academicYearUpper;

        $homePhoneDisplay = !empty($formData['home_phone']) && trim((string) $formData['home_phone']) !== ''
            ? trim((string) $formData['home_phone'])
            : '-';

        $emailPhoneParts = array_filter([
            !empty($formData['email']) && $formData['email'] !== '-' ? trim((string) $formData['email']) : null,
            !empty($formData['phone_number']) && $formData['phone_number'] !== '-' ? trim((string) $formData['phone_number']) : null,
        ]);
        $emailPhoneDisplay = !empty($emailPhoneParts) ? implode(' / ', $emailPhoneParts) : '-';
    @endphp

    {{-- KOP SURAT RESMI --}}
    <div class="kop-surat">
        @if(!empty($kopBase64))
            <img src="data:image/png;base64,{{ $kopBase64 }}" class="kop-img" alt="Kop Surat FASILKOM UNSUB">
        @else
            <div class="kop-table-wrapper">
                <table>
                    <tr>
                        <td class="logo-cell">
                            @if($canEmbedPng && file_exists(public_path('logo_unsub.png')))
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo_unsub.png'))) }}" class="logo-img" alt="Logo Universitas Subang">
                            @endif
                        </td>
                        <td class="info-cell">
                            <div class="univ-name">UNIVERSITAS SUBANG</div>
                            <div class="faculty-name">FAKULTAS ILMU KOMPUTER</div>
                            <div class="accreditation">Akreditasi BAIK SEKALI No. 110/SK/LAM-INFOKOM/Ak/S/VIII/2025</div>
                            <div class="address">Jalan R.A Kartini KM 3 Telp (0260) 411415 Subang</div>
                            <div class="email">E-Mail : <span style="color: #1d4ed8; text-decoration: underline;">fasilkom@unsub.ac.id</span></div>
                        </td>
                    </tr>
                </table>
            </div>
        @endif
    </div>

    {{-- JUDUL FORMULIR --}}
    <div class="form-header">
        <div class="form-title">FORMULIR PENDAFTARAN SEMINAR SKRIPSI/TUGAS AKHIR</div>
        <div class="form-subtitle">(Data mohon diisi dengan <u>huruf kapital</u> dan diisi dengan sebenar-benarnya)</div>
    </div>

    {{-- A. DATA PRIBADI --}}
    <div class="section-title">A.&nbsp;&nbsp;DATA PRIBADI</div>
    <p class="intro-text">
        Bagian Administrasi Akademik dan Kemahasiswaan (BAAK) FAKULTAS ILMU KOMPUTER<br>
        UNIVERSITAS SUBANG menerangkan bahwa :
    </p>

    <table class="personal-table">
        <tr>
            <td class="col-label">Nama Lengkap</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ mb_strtoupper((string) ($formData['full_name'] ?? '-')) }}</td>
        </tr>
        <tr>
            <td class="col-label">Tempat/Tgl Lahir</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $ttlDisplay }}</td>
        </tr>
        <tr>
            <td class="col-label">NPM</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ mb_strtoupper((string) ($formData['npm'] ?? '-')) }}</td>
        </tr>
        <tr>
            <td class="col-label">Program Studi/Jenjang</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ mb_strtoupper((string) ($formData['study_program'] ?? 'SISTEM INFORMASI / S1')) }}</td>
        </tr>
        <tr>
            <td class="col-label">Semester/Tahun Akademik</td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $semesterAcademicDisplay }}</td>
        </tr>
        <tr>
            <td class="col-label" style="vertical-align: top; padding-top: 5px;">Alamat</td>
            <td class="col-colon" style="vertical-align: top; padding-top: 5px;">:</td>
            <td style="padding: 2px 0 0 0;">
                <div style="border-bottom: 1px solid #000000; padding: 2px 4px; line-height: 1.35;">
                    {{ mb_strtoupper((string) ($formData['address'] ?? '-')) }}
                </div>
                <table class="address-sub-table">
                    <tr>
                        <td style="width: 66px; white-space: nowrap;">Kode Pos</td>
                        <td class="inline-line" style="width: 110px;">{{ mb_strtoupper((string) ($formData['postal_code'] ?? '-')) }}</td>
                        <td style="width: 18px;"></td>
                        <td style="width: 34px; white-space: nowrap;">Telp</td>
                        <td class="inline-line">{{ $homePhoneDisplay }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="col-label" style="padding-top: 5px;">E-mail &amp; No. HP</td>
            <td class="col-colon" style="padding-top: 5px;">:</td>
            <td class="col-value" style="padding-top: 5px;">{{ $emailPhoneDisplay }}</td>
        </tr>
    </table>

    {{-- B. DATA KESIAPAN SEMINAR --}}
    <div class="section-title">B.&nbsp;&nbsp;DATA KESIAPAN SEMINAR</div>

    <table class="readiness-table">
        <tr>
            <td class="readiness-no">1</td>
            <td class="readiness-label">Judul Skripsi/Tugas Akhir</td>
            <td class="thesis-title-cell">
                {{ mb_strtoupper((string) ($formData['thesis_title'] ?? '-')) }}
            </td>
        </tr>
        <tr>
            <td class="readiness-no">2</td>
            <td colspan="2" class="statement-cell">
                Dengan ini saya menyatakan siap mengikuti Seminar Skripsi/Tugas Akhir dan bersedia mengoreksi serta memperbaiki kesalahan dan kekurangan berdasarkan saran dari dosen pembimbing dan penguji.
            </td>
        </tr>
        <tr>
            <td colspan="3" class="student-sign-cell">
                <div class="sign-role-title">Mahasiswa/i</div>

                {{-- Area kosong TTD Mahasiswa + Kotak Materai 10.000 untuk TTD basah & materai fisik --}}
                <table class="materai-layout">
                    <tr>
                        <td style="width: 47%; text-align: right; padding-right: 8px !important;">
                            <div class="materai-box">
                                Materai<br>10.000
                            </div>
                        </td>
                        <td style="width: 53%; height: 64px;">
                            &nbsp;
                        </td>
                    </tr>
                </table>

                <div class="student-sign-name">{{ mb_strtoupper((string) ($formData['full_name'] ?? '-')) }}</div>
                <div class="student-sign-npm">NPM. {{ mb_strtoupper((string) ($formData['npm'] ?? '-')) }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="approval-header-cell">Menyetujui</td>
        </tr>
        <tr>
            <td colspan="3" class="supervisors-wrapper-cell">
                <table class="supervisors-table">
                    <tr>
                        {{-- Dosen Pembimbing 1 (Opsi A: TTD Digital + QR jika tersedia & ACC UP, kosong jika belum) --}}
                        <td class="left-col">
                            <div class="sign-role-title">Dosen Pembimbing 1</div>
                            <div class="sig-area">
                                @if(!empty($p1Signature))
                                    <table class="sig-inner-table">
                                        <tr>
                                            @if($canEmbedPng || !str_contains($p1Signature, 'image/png'))
                                                <td>
                                                    <img src="{{ $p1Signature }}" class="sig-img" alt="TTD Pembimbing 1">
                                                </td>
                                            @endif
                                            @if(!empty($p1QrSvg))
                                                <td>
                                                    <img src="data:image/svg+xml;base64,{{ $p1QrSvg }}" class="qr-img" alt="QR Verifikasi Pembimbing 1">
                                                </td>
                                            @endif
                                        </tr>
                                    </table>
                                @else
                                    <div style="height: 64px;">&nbsp;</div>
                                @endif
                            </div>
                            <div class="dosen-name-line">{{ $p1?->name ?? ($formData['pembimbing1_name'] ?? '-') }}</div>
                            @if($p1 && !empty($p1->identifier))
                                <div class="dosen-id-line">NIDN/NIP. {{ $p1->identifier }}</div>
                            @endif
                        </td>

                        {{-- Dosen Pembimbing 2 (Opsi A: TTD Digital + QR jika tersedia & ACC UP, kosong jika belum) --}}
                        <td>
                            <div class="sign-role-title">Dosen Pembimbing 2</div>
                            <div class="sig-area">
                                @if(!empty($p2Signature))
                                    <table class="sig-inner-table">
                                        <tr>
                                            @if($canEmbedPng || !str_contains($p2Signature, 'image/png'))
                                                <td>
                                                    <img src="{{ $p2Signature }}" class="sig-img" alt="TTD Pembimbing 2">
                                                </td>
                                            @endif
                                            @if(!empty($p2QrSvg))
                                                <td>
                                                    <img src="data:image/svg+xml;base64,{{ $p2QrSvg }}" class="qr-img" alt="QR Verifikasi Pembimbing 2">
                                                </td>
                                            @endif
                                        </tr>
                                    </table>
                                @else
                                    <div style="height: 64px;">&nbsp;</div>
                                @endif
                            </div>
                            <div class="dosen-name-line">{{ $p2?->name ?? ($formData['pembimbing2_name'] ?? '-') }}</div>
                            @if($p2 && !empty($p2->identifier))
                                <div class="dosen-id-line">NIDN. {{ $p2->identifier }}</div>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="doc-footer">
        Formulir Pendaftaran Seminar Skripsi/Tugas Akhir diterbitkan melalui SIBIMA FASILKOM UNSUB
        @if($application->wave)
            &bull; {{ $application->wave->name }}
        @endif
        &bull; Diajukan: {{ $submittedDateTime }}
    </div>
</body>
</html>

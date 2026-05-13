<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Logbook Bimbingan Skripsi</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .kop-surat {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }
        .kop-surat table {
            width: 100%;
            border: none;
        }
        .kop-surat td {
            border: none !important;
            padding: 0 !important;
            vertical-align: middle;
        }
        .kop-surat .logo-cell {
            width: 100px;
            text-align: left;
        }
        .kop-surat .logo-img {
            width: 90px;
            height: auto;
        }
        .kop-surat .info-cell {
            text-align: center;
            padding-right: 90px !important; /* Balance the logo width */
        }
        .kop-surat .univ-name {
            font-size: 18px;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
        }
        .kop-surat .faculty-name {
            font-size: 22px;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
        }
        .kop-surat .accreditation {
            font-size: 12px;
            font-weight: bold;
            margin: 2px 0;
        }
        .kop-surat .address {
            font-size: 10px;
            margin: 1px 0;
        }
        .kop-surat .email {
            font-size: 10px;
            margin: 1px 0;
        }
        
        .report-title {
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 20px;
            text-decoration: underline;
        }
        .student-info {
            margin-bottom: 25px;
        }
        .student-info table {
            width: 100%;
        }
        .student-info td {
            padding: 3px 0;
            vertical-align: top;
        }
        .label {
            width: 150px;
            font-weight: bold;
        }
        .separator {
            width: 10px;
        }
        .log-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .log-table th, .log-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        .log-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .text-center {
            text-align: center;
        }
        .footer-sign {
            margin-top: 50px;
            width: 100%;
        }
        .footer-sign table {
            width: 100%;
        }
        .footer-sign td {
            width: 50%;
            text-align: center;
        }
        .sign-space {
            height: 80px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="kop-surat">
        <table>
            <tr>
                <td class="logo-cell">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo_unsub.png'))) }}" class="logo-img">
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

    <div class="report-title">
        LOGBOOK BIMBINGAN SKRIPSI
    </div>

    <div class="student-info">
        <table>
            <tr>
                <td class="label">Nama Mahasiswa</td>
                <td class="separator">:</td>
                <td>{{ $thesis->student->name }}</td>
            </tr>
            <tr>
                <td class="label">NPM</td>
                <td class="separator">:</td>
                <td>{{ $thesis->student->identifier }}</td>
            </tr>
            <tr>
                <td class="label">Judul Skripsi</td>
                <td class="separator">:</td>
                <td>{{ $thesis->final_title ?? $thesis->title }}</td>
            </tr>
            <tr>
                <td class="label">Dosen Pembimbing 1</td>
                <td class="separator">:</td>
                <td>{{ $thesis->pembimbing1->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Dosen Pembimbing 2</td>
                <td class="separator">:</td>
                <td>{{ $thesis->pembimbing2->name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <table class="log-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 100px;">Tanggal</th>
                <th>Topik & Hasil Bimbingan</th>
                <th style="width: 120px;">Paraf Dosen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $index => $session)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $session->scheduled_at->format('d/m/Y') }}</td>
                    <td>
                        <strong>Topik:</strong> {{ $session->topic }}<br>
                        <div style="margin-top: 5px;">
                            <strong>Hasil:</strong> {{ $session->feedback ?: '-' }}
                        </div>
                        @if($session->notes)
                            <div style="margin-top: 5px; color: #777; font-size: 10px; font-style: italic;">
                                <strong>Ket. Pengajuan:</strong> {{ $session->notes }}
                            </div>
                        @endif
                        <div style="margin-top: 5px; font-size: 10px; color: #555;">
                            <strong>Dosen:</strong> {{ $session->dosen->name ?? '-' }}
                        </div>
                    </td>
                    <td class="text-center" style="vertical-align: middle;">
                        @if($session->dosen && $session->dosen->signature)
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $session->dosen->signature))) }}" style="height: 100px; width: auto;">
                        @elseif($session->dosen && $session->dosen->signature_token)
                            {{-- Fallback to QR if no image but has token --}}
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(40)->generate(url('/verify-signature/' . $session->dosen->signature_token))) !!} ">
                        @else
                            <span style="font-size: 8px; color: #999;">ACC Digital</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada catatan bimbingan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer removed --}}
</body>
</html>

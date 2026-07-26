<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Pengajuan Skripsi</title>
    <style>
        @page {
            size: landscape;
            margin: 1cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
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
            margin-bottom: 20px;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 16px;
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f8f8f8;
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
        }
        .status {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 8pt;
        }
        .footer {
            margin-top: 30px;
            width: 100%;
        }
        .footer-table {
            width: 100%;
            border: none;
        }
        .footer-table td {
            border: none;
            width: 50%;
        }
        .signature-container {
            text-align: right;
            padding-right: 50px;
        }
        .signature-box {
            display: inline-block;
            text-align: center;
        }
        .signature-img {
            height: 100px;
            margin: 5px 0;
        }
        .qr-code {
            height: 80px;
            margin: 5px 0;
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
        Laporan Data Pengajuan Skripsi Mahasiswa
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 150px;">Mahasiswa</th>
                <th style="width: 200px;">Judul Skripsi</th>
                <th>Deskripsi</th>
                <th style="width: 80px;">Status</th>
                <th style="width: 180px;">Pembimbing</th>
            </tr>
        </thead>
        <tbody>
            @foreach($theses as $index => $thesis)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $thesis->student->name }}</strong><br>
                        <span style="color: #666; font-size: 9pt;">{{ $thesis->student->identifier }}</span>
                    </td>
                    <td>
                        @if($thesis->final_title)
                            <strong>{{ $thesis->final_title }}</strong><br>
                            <small style="color: #666;">Awal: {{ $thesis->title }}</small>
                        @else
                            {{ $thesis->title }}
                        @endif
                    </td>
                    <td>
                        <div style="font-size: 8pt; line-height: 1.2; white-space: pre-line;">
                            {{ $thesis->abstract ?? '-' }}
                        </div>
                    </td>
                    <td class="status">
                        @if($thesis->status === 'pending') Menunggu
                        @elseif($thesis->status === 'active') Aktif
                        @elseif($thesis->status === 'completed') Selesai
                        @else {{ $thesis->status }}
                        @endif
                    </td>
                    <td>
                        <div style="font-size: 9pt;">
                            1. {{ $thesis->pembimbing1->name ?? '-' }}<br>
                            2. {{ $thesis->pembimbing2->name ?? '-' }}
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td style="font-size: 8pt; color: #999; vertical-align: bottom;">
                    Dicetak pada: {{ now()->locale('id')->translatedFormat('d F Y H:i') }}
                </td>
                <td class="signature-container">
                    <div class="signature-box">
                        <p style="margin-bottom: 5px;">Subang, {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
                        <p style="margin-bottom: 10px;">Mengetahui,<br>Ketua Program Studi</p>
                        
                        <div style="min-height: 110px;">
                            @if(isset($kaprodi) && $kaprodi && $kaprodi->signature)
                                <img src="{{ $kaprodi->decrypted_signature }}" class="signature-img">
                            @elseif(isset($kaprodi) && $kaprodi && $kaprodi->signature_token)
                                <img src="data:image/png;base64, {!! base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(250)->generate(url('/verify-signature/' . $kaprodi->signature_token))) !!} " class="qr-code">
                            @else
                                <br><br><br>
                            @endif
                        </div>
                        
                        <p style="margin-top: 5px;"><strong>( {{ (isset($kaprodi) && $kaprodi) ? $kaprodi->name : 'Kaprodi' }} )</strong></p>
                        <p style="font-size: 8pt; color: #666; margin-top: -5px;">NIDN: {{ (isset($kaprodi) && $kaprodi) ? $kaprodi->identifier : '........................' }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

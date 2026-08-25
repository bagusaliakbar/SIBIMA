<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Jadwal Seminar Skripsi</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
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
        }
        .report-title h1 {
            font-size: 18px;
            margin: 0;
            padding: 0;
            letter-spacing: 2px;
            text-decoration: underline;
        }
        .report-title h2 {
            font-size: 13px;
            margin: 5px 0 0 0;
            padding: 0;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 15px;
        }
        .meta-info td {
            vertical-align: top;
        }
        .meta-info .label {
            width: 100px;
            font-weight: bold;
        }
        .meta-info .location-box {
            text-align: right;
            font-weight: bold;
        }
        table.schedule {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.schedule th {
            background-color: #f8f9fa;
            border: 0.5pt solid #000;
            padding: 6px 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        table.schedule td {
            border: 0.5pt solid #000;
            padding: 6px 4px;
            vertical-align: top;
            font-size: 10px;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .activity-row {
            background-color: #fcfcfc;
        }
        .student-name {
            font-weight: bold;
            font-size: 12px;
            display: block;
            margin-bottom: 3px;
        }
        .thesis-title {
            font-size: 10px;
            color: #555;
        }
        .list-item {
            margin-bottom: 4px;
        }
        .no-wrap { white-space: nowrap; }
        
        /* Footer & Signature */
        .footer {
            margin-top: 30px;
            width: 100%;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-container {
            width: 250px;
            text-align: left;
        }
        .signature-box {
            display: inline-block;
            text-align: left;
        }
        .signature-img {
            max-height: 80px;
            margin: 10px 0;
        }
        .qr-code {
            width: 80px;
            height: 80px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="kop-surat">
        <table>
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

    <div class="report-title">
        <h1>JADWAL SEMINAR SKRIPSI</h1>
        <h2>{{ strtoupper($seminarSchedule->title) }}</h2>
    </div>

    <table class="meta-info">
        <tr>
            <td class="label">Hari / Tanggal</td>
            <td>: {{ \Carbon\Carbon::parse($seminarSchedule->date)->locale('id')->translatedFormat('l, d F Y') }}</td>
            <td class="location-box">{{ $seminarSchedule->location ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Ketua Sidang</td>
            <td>: {{ $seminarSchedule->chairman?->name ?? '-' }}</td>
            <td></td>
        </tr>
        <tr>
            <td class="label">Moderator</td>
            <td>: {{ $seminarSchedule->moderator?->name ?? '-' }}</td>
            <td></td>
        </tr>
        @if($seminarSchedule->meeting_link)
        <tr>
            <td class="label">Link / URL</td>
            <td colspan="2">: <span style="color: #0066cc;">{{ $seminarSchedule->meeting_link }}</span></td>
        </tr>
        @endif
    </table>

    <table class="schedule">
        <thead>
            <tr>
                <th style="width: 15px;">NO</th>
                <th style="width: 45px;">WAKTU</th>
                <th style="width: 80px;">NPM</th>
                <th style="width: 120px;">NAMA</th>
                <th>JUDUL SKRIPSI</th>
                <th style="width: 130px;">PEMBIMBING</th>
                <th style="width: 130px;">PENGUJI</th>
            </tr>
        </thead>
        <tbody>
            @php $studentNo = 1; @endphp
            @foreach($seminarSchedule->details as $detail)
                @if($detail->thesis_id)
                    <tr>
                        <td class="text-center font-bold">{{ $studentNo++ }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($detail->start_time)->format('H.i') }}-{{ \Carbon\Carbon::parse($detail->end_time)->format('H.i') }}</td>
                        <td class="text-center">{{ $detail->thesis->student->identifier }}</td>
                        <td class="font-bold">{{ strtoupper($detail->thesis->student->name) }}</td>
                        <td class="thesis-title">{{ $detail->thesis->title }}</td>
                        <td>
                            <div class="list-item">1. {{ $detail->thesis->pembimbing1->name }}</div>
                            @if($detail->thesis->pembimbing2)
                                <div class="list-item">2. {{ $detail->thesis->pembimbing2->name }}</div>
                            @endif
                        </td>
                        <td>
                            @if($detail->examiner1)
                                <div class="list-item">1. {{ $detail->examiner1->name }}</div>
                            @endif
                            @if($detail->examiner2)
                                <div class="list-item">2. {{ $detail->examiner2->name }}</div>
                            @endif
                        </td>
                    </tr>
                @else
                    <tr class="activity-row">
                        <td class="text-center font-bold">#</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($detail->start_time)->format('H.i') }}-{{ \Carbon\Carbon::parse($detail->end_time)->format('H.i') }}</td>
                        <td colspan="5" class="font-bold" style="text-transform: uppercase;">{{ $detail->activity_name }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td style="font-size: 8pt; color: #999; vertical-align: bottom;">
                    Dicetak pada: {{ now()->locale('id')->translatedFormat('d F Y H:i') }}<br>
                    URL: {{ url('/') }}
                </td>
                <td class="signature-container">
                    <div class="signature-box">
                        <p style="margin-bottom: 5px;">Subang, {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
                        <p style="margin-bottom: 8px;">Mengetahui,<br>Ketua Program Studi</p>
                        
                        <div style="min-height: 80px;">
                            @if(isset($kaprodi) && $kaprodi && $kaprodi->signature)
                                <img src="{{ $kaprodi->decrypted_signature }}" class="signature-img">
                            @elseif(isset($kaprodi) && $kaprodi && $kaprodi->signature_token)
                                <img src="data:image/png;base64, {!! base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(300)->generate(url('/verify-signature/' . $kaprodi->signature_token))) !!} " class="qr-code">
                            @else
                                <br><br><br>
                            @endif
                        </div>
                        
                        <p style="margin-top: 5px;"><strong>({{ (isset($kaprodi) && $kaprodi) ? $kaprodi->name : '..................................................' }})</strong></p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

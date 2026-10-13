<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Jadwal Sidang Skripsi - {{ $thesisDefenseSchedule->title }}</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 8mm 12mm 8mm 12mm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8pt;
            color: #1e293b;
            line-height: 1.35;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat Resmi FASILKOM UNSUB */
        .kop-surat {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
            margin-bottom: 12px;
        }
        .kop-surat table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }
        .kop-surat td {
            border: none !important;
            padding: 0 !important;
            vertical-align: middle;
        }
        .kop-surat .logo-cell {
            width: 85px;
            text-align: left;
        }
        .kop-surat .logo-img {
            width: 75px;
            height: auto;
            display: block;
        }
        .kop-surat .info-cell {
            text-align: center;
            padding-right: 85px !important; /* Balance logo width */
        }
        .kop-surat .univ-name {
            font-size: 16px;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            letter-spacing: 0.5px;
            color: #000;
        }
        .kop-surat .faculty-name {
            font-size: 21px;
            margin: 1px 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #000;
        }
        .kop-surat .accreditation {
            font-size: 11px;
            font-weight: bold;
            margin: 2px 0 1px 0;
            color: #000;
        }
        .kop-surat .address {
            font-size: 9.5px;
            margin: 1px 0;
            color: #000;
        }
        .kop-surat .email {
            font-size: 9.5px;
            margin: 1px 0;
            color: #000;
        }

        /* Document Title */
        .header-title-box {
            text-align: center;
            margin-bottom: 8px;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
        }
        .doc-subtitle {
            font-size: 9pt;
            font-weight: 700;
            color: #475569;
            margin: 2px 0 0 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Meta Card */
        .meta-card {
            width: 100%;
            margin-bottom: 8px;
            border-collapse: collapse;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .meta-card td {
            padding: 5px 10px;
            vertical-align: middle;
            border: none;
            font-size: 8pt;
        }
        .meta-col-left {
            width: 65%;
        }
        .meta-col-right {
            width: 35%;
            text-align: right;
        }
        .meta-item {
            margin-bottom: 2px;
        }
        .meta-label {
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            font-size: 7pt;
            display: inline-block;
            width: 85px;
            letter-spacing: 0.3px;
        }
        .meta-val {
            font-weight: 700;
            color: #0f172a;
        }
        .room-badge {
            display: inline-block;
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: 700;
            font-size: 7.8pt;
            padding: 3px 8px;
            border-radius: 3px;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .link-text {
            color: #2563eb;
            font-size: 7pt;
            text-decoration: underline;
        }

        /* Schedule Table */
        table.schedule-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 8px;
        }
        table.schedule-table thead th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7.2pt;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 6px 4px;
            border: 1px solid #0f172a;
            text-align: center;
            vertical-align: middle;
        }
        table.schedule-table tbody tr {
            page-break-inside: avoid;
        }
        table.schedule-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        table.schedule-table tbody td {
            border: 1px solid #cbd5e1;
            padding: 6px 6px;
            vertical-align: top;
            font-size: 7.5pt;
            line-height: 1.35;
            color: #1e293b;
        }
        .td-center {
            text-align: center;
        }
        .td-num {
            font-weight: 700;
            color: #475569;
        }
        .td-time {
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            font-size: 7.2pt;
        }
        .td-npm {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-weight: 700;
            color: #334155;
            letter-spacing: 0.3px;
            font-size: 7.2pt;
        }
        .student-name {
            font-weight: 700;
            color: #0f172a;
            font-size: 7.8pt;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .thesis-title-text {
            font-size: 7.2pt;
            color: #334155;
            line-height: 1.3;
            text-align: left;
        }
        .person-item {
            margin-bottom: 2px;
            font-size: 7.2pt;
            line-height: 1.25;
            color: #1e293b;
        }
        .person-num {
            display: inline-block;
            font-weight: 700;
            color: #1e3a8a;
            margin-right: 2px;
        }
        .activity-row {
            background-color: #f1f5f9 !important;
        }
        .activity-text {
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 7.5pt;
        }

        /* Signatures & Footer */
        .footer-section {
            width: 100%;
            margin-top: 6px;
            page-break-inside: avoid;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-table td {
            vertical-align: top;
            padding: 0;
            border: none;
        }
        .system-info {
            font-size: 6.8pt;
            color: #64748b;
            line-height: 1.4;
            padding-top: 10px;
        }
        .signature-box {
            width: 220px;
            float: right;
            text-align: left;
            font-size: 7.5pt;
            color: #0f172a;
        }
        .sig-city-date {
            margin-bottom: 2px;
        }
        .sig-role {
            font-weight: 700;
            margin-bottom: 4px;
        }
        .sig-space {
            min-height: 48px;
            margin: 3px 0;
        }
        .sig-img {
            max-height: 48px;
            width: auto;
        }
        .sig-qr {
            width: 48px;
            height: 48px;
        }
        .sig-name {
            font-weight: 700;
            font-size: 8pt;
            text-decoration: underline;
            color: #0f172a;
            margin-top: 2px;
        }
        .sig-nip {
            font-size: 6.8pt;
            color: #64748b;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT RESMI FASILKOM UNSUB --}}
    <div class="kop-surat">
        <table>
            <tr>
                <td class="logo-cell">
                    @if(file_exists(public_path('logo_unsub.png')))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo_unsub.png'))) }}" class="logo-img" alt="Logo Universitas Subang">
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

    {{-- JUDUL DOKUMEN --}}
    <div class="header-title-box">
        <h1 class="doc-title">JADWAL SIDANG SKRIPSI</h1>
        <h2 class="doc-subtitle">{{ strtoupper($thesisDefenseSchedule->title) }}</h2>
    </div>

    {{-- METADATA INFO CARD --}}
    <table class="meta-card">
        <tr>
            <td class="meta-col-left">
                <div class="meta-item">
                    <span class="meta-label">Hari / Tanggal</span>
                    <span class="meta-val">: {{ \Carbon\Carbon::parse($thesisDefenseSchedule->date)->locale('id')->translatedFormat('l, d F Y') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Ketua Sidang</span>
                    <span class="meta-val">: {{ $thesisDefenseSchedule->chairman?->name ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Moderator</span>
                    <span class="meta-val">: {{ $thesisDefenseSchedule->moderator?->name ?? '-' }}</span>
                </div>
            </td>
            <td class="meta-col-right">
                <div class="room-badge">
                    RUANGAN: {{ strtoupper($thesisDefenseSchedule->location ?: 'BELUM DITENTUKAN') }}
                </div>
                @if($thesisDefenseSchedule->meeting_link)
                    <div>
                        <span style="font-size: 6.8pt; color: #64748b;">Meeting Link:</span><br>
                        <span class="link-text">{{ $thesisDefenseSchedule->meeting_link }}</span>
                    </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- TABEL JADWAL PESERTA --}}
    <table class="schedule-table">
        <thead>
            <tr>
                <th style="width: 25px;">NO</th>
                <th style="width: 68px;">WAKTU</th>
                <th style="width: 72px;">NPM</th>
                <th style="width: 130px;">NAMA MAHASISWA</th>
                <th>JUDUL PENELITIAN / SKRIPSI</th>
                <th style="width: 135px;">PEMBIMBING</th>
                <th style="width: 135px;">TIM PENGUJI</th>
            </tr>
        </thead>
        <tbody>
            @php $studentNo = 1; @endphp
            @foreach($thesisDefenseSchedule->details as $detail)
                @if($detail->thesis_id)
                    <tr>
                        <td class="td-center td-num">{{ $studentNo++ }}</td>
                        <td class="td-center td-time">
                            {{ \Carbon\Carbon::parse($detail->start_time)->format('H.i') }} - {{ \Carbon\Carbon::parse($detail->end_time)->format('H.i') }}
                        </td>
                        <td class="td-center td-npm">{{ $detail->thesis->student->identifier }}</td>
                        <td>
                            <div class="student-name">{{ $detail->thesis->student->name }}</div>
                        </td>
                        <td>
                            <div class="thesis-title-text">{{ $detail->thesis->display_title ?? $detail->thesis->title }}</div>
                        </td>
                        <td>
                            <div class="person-item">
                                <span class="person-num">1.</span> {{ $detail->thesis->pembimbing1?->name ?? '-' }}
                            </div>
                            @if($detail->thesis->pembimbing2)
                                <div class="person-item">
                                    <span class="person-num">2.</span> {{ $detail->thesis->pembimbing2->name }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($detail->examiner1)
                                <div class="person-item">
                                    <span class="person-num">1.</span> {{ $detail->examiner1->name }}
                                </div>
                            @endif
                            @if($detail->examiner2)
                                <div class="person-item">
                                    <span class="person-num">2.</span> {{ $detail->examiner2->name }}
                                </div>
                            @endif
                        </td>
                    </tr>
                @else
                    <tr class="activity-row">
                        <td class="td-center td-num">#</td>
                        <td class="td-center td-time">
                            {{ \Carbon\Carbon::parse($detail->start_time)->format('H.i') }} - {{ \Carbon\Carbon::parse($detail->end_time)->format('H.i') }}
                        </td>
                        <td colspan="5" class="activity-text">
                            {{ strtoupper($detail->activity_name) }}
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER & TANDA TANGAN KAPRODI --}}
    <div class="footer-section">
        <table class="footer-table">
            <tr>
                <td class="system-info">
                    <strong>Sistem Informasi Bimbingan Mahasiswa (SIBIMA)</strong><br>
                    Fakultas Ilmu Komputer - Universitas Subang<br>
                    Dicetak secara otomatis pada: {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB
                </td>
                <td style="width: 230px;">
                    <div class="signature-box">
                        <div class="sig-city-date">Subang, {{ \Carbon\Carbon::parse($thesisDefenseSchedule->date)->locale('id')->translatedFormat('d F Y') }}</div>
                        <div class="sig-role">Mengetahui,<br>Ketua Program Studi</div>
                        
                        <div class="sig-space">
                            @if(isset($kaprodi) && $kaprodi && $kaprodi->signature)
                                <img src="{{ $kaprodi->decrypted_signature }}" class="sig-img">
                            @elseif(isset($kaprodi) && $kaprodi && $kaprodi->signature_token)
                                <img src="data:image/png;base64, {!! base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(250)->generate(url('/verify-signature/' . $kaprodi->signature_token))) !!} " class="sig-qr">
                            @else
                                <div style="height: 40px;"></div>
                            @endif
                        </div>
                        
                        <div class="sig-name">({{ (isset($kaprodi) && $kaprodi) ? $kaprodi->name : '..................................................' }})</div>
                        @if(isset($kaprodi) && $kaprodi && $kaprodi->identifier && is_numeric($kaprodi->identifier))
                            <div class="sig-nip">NIDN / NIP. {{ $kaprodi->identifier }}</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

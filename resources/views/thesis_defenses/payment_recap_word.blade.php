<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Bukti Pembayaran Sidang Skripsi</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        @page Section1 {
            size: 210mm 297mm; /* A4 portrait */
            margin: 15mm 15mm 15mm 15mm;
            mso-header-margin: 10mm;
            mso-footer-margin: 10mm;
            mso-paper-source: 0;
        }

        div.Section1 {
            page: Section1;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            color: #111827;
            line-height: 1.3;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* KOP SURAT */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #000000;
            margin-bottom: 12px;
            padding-bottom: 6px;
        }

        .kop-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .kop-title {
            text-align: center;
        }

        .univ-name {
            font-size: 14pt;
            font-weight: normal;
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
        }

        .faculty-name {
            font-size: 16pt;
            font-weight: bold;
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
        }

        .accreditation {
            font-size: 9pt;
            font-weight: bold;
            margin: 2px 0;
        }

        .address, .email {
            font-size: 8.5pt;
            margin: 1px 0;
        }

        /* HEADER LAPORAN */
        .report-header {
            text-align: center;
            margin-bottom: 18px;
        }

        .report-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        /* DATA TABLE */
        .data-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 6px;
            border: 1px solid #000000;
            text-align: center;
        }

        .data-table td {
            border: 1px solid #94a3b8;
            padding: 7px 6px;
            vertical-align: middle;
            font-size: 8.5pt;
        }

        .col-no {
            width: 5%;
            text-align: center;
            font-weight: bold;
        }

        .col-npm {
            width: 15%;
            text-align: center;
            font-weight: bold;
        }

        .col-nama {
            width: 40%;
            text-align: left;
        }

        .col-bukti {
            width: 40%;
            text-align: center;
        }

        .student-name {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
            margin: 0;
        }

        .proof-img {
            width: 200px;
            max-width: 100%;
            height: auto;
            border: 1px solid #cbd5e1;
            margin: 0 auto;
            display: block;
        }

        .proof-link-card {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 7px 8px;
            text-align: center;
            font-size: 8pt;
            word-break: break-all;
            word-wrap: break-word;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .signature-table td {
            border: none;
            padding: 0;
            vertical-align: top;
            font-size: 9.5pt;
        }
    </style>
</head>
<body>
<div class="Section1">

    {{-- KOP SURAT --}}
    @php
        $kopBase64 = null;
        $kopCandidates = [
            public_path('images/kop_fasilkom.png'),
            public_path('kop_fasilkom.png'),
            base_path('public/images/kop_fasilkom.png'),
            base_path('public/kop_fasilkom.png'),
        ];
        foreach ($kopCandidates as $cand) {
            if (file_exists($cand) && is_readable($cand)) {
                $kopBase64 = base64_encode(file_get_contents($cand));
                break;
            }
        }
    @endphp

    @if(!empty($kopBase64))
        <table width="100%" cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
            <tr>
                <td align="center" style="text-align: center; padding: 0;">
                    <img src="data:image/png;base64,{{ $kopBase64 }}" width="615" height="138" style="width: 615px; height: 138px; display: block; margin: 0 auto;" alt="Kop Surat FASILKOM UNSUB">
                </td>
            </tr>
        </table>
    @else
        <table class="kop-table" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="15%" style="width: 15%; text-align: center; vertical-align: middle;">
                    @if(file_exists(public_path('logo_unsub.png')))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo_unsub.png'))) }}" width="70" height="70" style="width: 70px; height: 70px;" alt="Logo Unsub">
                    @endif
                </td>
                <td width="85%" class="kop-title" style="width: 85%; padding-right: 40px;">
                    <div class="univ-name">UNIVERSITAS SUBANG</div>
                    <div class="faculty-name">FAKULTAS ILMU KOMPUTER</div>
                    <div class="accreditation">Akreditasi BAIK SEKALI No. 110/SK/LAM-INFOKOM/Ak/S/VIII/2025</div>
                    <div class="address">Jalan R.A Kartini KM 3 Telp (0260) 411415 Subang</div>
                    <div class="email">E-Mail : fasilkom@unsub.ac.id</div>
                </td>
            </tr>
        </table>
    @endif

    {{-- HEADER LAPORAN --}}
    <div class="report-header">
        <div class="report-title">REKAPITULASI BUKTI PEMBAYARAN PENDAFTARAN SIDANG SKRIPSI</div>
    </div>

    {{-- DATA TABLE --}}
    <table class="data-table" width="100%" cellpadding="4" cellspacing="0">
        <thead>
            <tr style="mso-yfti-tblheader: yes;">
                <th class="col-no" width="5%" style="width: 5%;">NO</th>
                <th class="col-npm" width="15%" style="width: 15%;">NPM</th>
                <th class="col-nama" width="40%" style="width: 40%;">NAMA MAHASISWA</th>
                <th class="col-bukti" width="40%" style="width: 40%;">BUKTI PEMBAYARAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($applications as $index => $app)
                @php
                    $student = $app->thesis?->student;
                    $proof = $app->payment_proof_data ?? null;
                @endphp
                <tr>
                    <td class="col-no" width="5%" style="text-align: center; vertical-align: middle;">{{ $index + 1 }}</td>
                    <td class="col-npm" width="15%" style="text-align: center; vertical-align: middle;">{{ $student?->identifier ?? '-' }}</td>
                    <td class="col-nama" width="40%" style="text-align: left; vertical-align: middle;">
                        <div class="student-name">{{ $student?->name ?? 'Mahasiswa' }}</div>
                    </td>
                    <td class="col-bukti" width="40%" style="text-align: center; vertical-align: middle;">
                        @if($proof && $proof['type'] === 'image' && !empty($proof['base64']))
                            <img src="{{ $proof['base64'] }}" width="200" class="proof-img" alt="Bukti Pembayaran {{ $student?->name }}">
                            <div style="font-size: 7.5pt; color: #64748b; margin-top: 4px;">
                                Bukti Pembayaran Terverifikasi
                            </div>
                        @elseif($proof && $proof['type'] === 'url')
                            <div class="proof-link-card">
                                <strong style="color: #1e293b; font-size: 8pt;">{{ $proof['is_gdrive'] ? 'Dokumen di Google Drive' : 'Tautan Bukti Bayar' }}</strong><br>
                                <a href="{{ $proof['url'] }}" target="_blank" style="color: #2563eb; text-decoration: underline; font-size: 7.5pt; word-break: break-all; word-wrap: break-word;">{{ \Illuminate\Support\Str::limit($proof['url'], 45) }}</a>
                            </div>
                        @elseif($proof && $proof['type'] === 'pdf')
                            <div class="proof-link-card">
                                <strong style="color: #1e293b; font-size: 8pt;">Berkas PDF Bukti Pembayaran</strong><br>
                                <a href="{{ $proof['url'] }}" target="_blank" style="color: #2563eb; text-decoration: underline; font-size: 7.5pt;">Buka Dokumen PDF</a>
                            </div>
                        @elseif($proof && $proof['type'] === 'file')
                            <div class="proof-link-card">
                                <strong style="color: #1e293b; font-size: 8pt;">Berkas Bukti: {{ $proof['filename'] ?? '-' }}</strong><br>
                                <a href="{{ $proof['url'] }}" target="_blank" style="color: #2563eb; text-decoration: underline; font-size: 7.5pt;">Buka Berkas Asli</a>
                            </div>
                        @else
                            <div style="color: #94a3b8; font-style: italic; font-size: 8pt; padding: 10px;">
                                — Belum ada bukti pembayaran diunggah —
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px; color: #64748b; font-style: italic;">
                        Tidak ada data pendaftaran sidang yang sesuai dengan filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- LEMBAR PENGESAHAN KAPRODI --}}
    <table class="signature-table" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center;">
                <div>Subang, {{ now()->translatedFormat('d F Y') }}</div>
                <div style="margin-top: 4px; font-weight: bold;">Mengetahui,</div>
                <div style="font-weight: bold; margin-bottom: 50px;">Koordinator Program Studi</div>
                <div style="font-weight: bold; text-decoration: underline;">{{ $kaprodi?->name ?? '............................................' }}</div>
                <div style="font-size: 8.5pt; color: #4b5563;">NIP/NIDN. {{ $kaprodi?->identifier ?? '-' }}</div>
            </td>
        </tr>
    </table>

</div>
</body>
</html>

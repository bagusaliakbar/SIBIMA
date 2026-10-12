<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Bukti Pembayaran Seminar Proposal</title>
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
            margin-bottom: 15px;
            padding-bottom: 5px;
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
            margin-bottom: 12px;
        }

        .report-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .report-subtitle {
            font-size: 8.5pt;
            font-weight: bold;
            color: #374151;
            margin-top: 2px;
        }

        /* METADATA */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            margin-bottom: 14px;
        }

        .meta-table td {
            padding: 4px 8px;
            font-size: 8.5pt;
            border: none;
        }

        .meta-label {
            font-weight: bold;
            color: #4b5563;
            text-transform: uppercase;
            font-size: 8pt;
        }

        .meta-val {
            font-weight: bold;
            color: #111827;
        }

        /* DATA TABLE */
        .data-table {
            width: 100%;
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
            padding: 8px 6px;
            vertical-align: top;
            font-size: 8.5pt;
        }

        .col-no {
            width: 5%;
            text-align: center;
            font-weight: bold;
        }

        .col-npm {
            width: 16%;
            text-align: center;
            font-weight: bold;
        }

        .col-nama {
            width: 25%;
            text-align: left;
        }

        .col-bukti {
            width: 54%;
            text-align: center;
        }

        .student-name {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
            margin-bottom: 3px;
        }

        .thesis-title {
            font-size: 8pt;
            color: #4b5563;
            font-style: italic;
        }

        .proof-img {
            max-height: 250px;
            max-width: 340px;
            border: 1px solid #cbd5e1;
            margin: 0 auto;
            display: block;
        }

        .proof-link-card {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 8px;
            text-align: center;
            font-size: 8pt;
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
        <div style="text-align: center; margin-bottom: 12px; border-bottom: 3px double #000; padding-bottom: 4px;">
            <img src="data:image/png;base64,{{ $kopBase64 }}" style="width: 100%; max-width: 650px; height: auto;" alt="Kop Surat FASILKOM UNSUB">
        </div>
    @else
        <table class="kop-table">
            <tr>
                <td style="width: 80px; text-align: left;">
                    @if(file_exists(public_path('logo_unsub.png')))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo_unsub.png'))) }}" style="width: 70px; height: auto;" alt="Logo Unsub">
                    @endif
                </td>
                <td class="kop-title">
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
        <div class="report-title">REKAPITULASI BUKTI PEMBAYARAN PENDAFTARAN SEMINAR PROPOSAL</div>
        <div class="report-subtitle">SISTEM INFORMASI BIMBINGAN MAHASISWA (SIBIMA) - FAKULTAS ILMU KOMPUTER - UNIVERSITAS SUBANG</div>
    </div>

    {{-- METADATA BAR --}}
    <table class="meta-table">
        <tr>
            <td style="width: 15%;" class="meta-label">Gelombang:</td>
            <td style="width: 35%;" class="meta-val">{{ $wave ? strtoupper($wave->name) : 'SEMUA GELOMBANG' }}</td>
            <td style="width: 15%;" class="meta-label">Tanggal Cetak:</td>
            <td style="width: 35%;" class="meta-val">{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
        <tr>
            <td class="meta-label">Status Validasi:</td>
            <td class="meta-val">
                @if(($status ?? 'all') === 'approved')
                    Disetujui (Approved)
                @elseif(($status ?? '') === 'pending')
                    Menunggu Validasi (Pending)
                @elseif(($status ?? '') === 'rejected')
                    Perlu Revisi / Ditolak (Rejected)
                @else
                    Semua Status
                @endif
            </td>
            <td class="meta-label">Total Mahasiswa:</td>
            <td class="meta-val">{{ count($applications) }} Mahasiswa Pendaftar</td>
        </tr>
        @if(!empty($search))
        <tr>
            <td class="meta-label">Filter Pencarian:</td>
            <td colspan="3" class="meta-val">"{{ $search }}"</td>
        </tr>
        @endif
    </table>

    {{-- DATA TABLE --}}
    <table class="data-table">
        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th class="col-npm">NPM</th>
                <th class="col-nama">NAMA & JUDUL SKRIPSI</th>
                <th class="col-bukti">BUKTI PEMBAYARAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($applications as $index => $app)
                @php
                    $student = $app->thesis?->student;
                    $proof = $app->payment_proof_data ?? null;
                @endphp
                <tr>
                    <td class="col-no">{{ $index + 1 }}</td>
                    <td class="col-npm">{{ $student?->identifier ?? '-' }}</td>
                    <td class="col-nama">
                        <div class="student-name">{{ $student?->name ?? 'Mahasiswa' }}</div>
                        <div class="thesis-title">{{ $app->thesis?->final_title ?? $app->thesis?->title ?? '-' }}</div>
                        @if($app->thesis?->pembimbing1)
                            <div style="font-size: 7.5pt; color: #64748b; margin-top: 4px;">
                                P1: {{ $app->thesis->pembimbing1->name }}
                            </div>
                        @endif
                    </td>
                    <td class="col-bukti">
                        @if($proof && $proof['type'] === 'image' && !empty($proof['base64']))
                            <img src="{{ $proof['base64'] }}" class="proof-img" alt="Bukti Pembayaran {{ $student?->name }}">
                            <div style="font-size: 7.5pt; color: #64748b; margin-top: 4px;">
                                Bukti Pembayaran Terverifikasi
                            </div>
                        @elseif($proof && $proof['type'] === 'url')
                            <div class="proof-link-card">
                                <strong>{{ $proof['is_gdrive'] ? 'Dokumen di Google Drive' : 'Tautan Bukti Bayar' }}</strong><br>
                                <a href="{{ $proof['url'] }}" target="_blank">{{ \Illuminate\Support\Str::limit($proof['url'], 55) }}</a>
                            </div>
                        @elseif($proof && $proof['type'] === 'pdf')
                            <div class="proof-link-card">
                                <strong>Berkas PDF Bukti Pembayaran</strong><br>
                                <a href="{{ $proof['url'] }}" target="_blank">Buka Dokumen PDF</a>
                            </div>
                        @elseif($proof && $proof['type'] === 'file')
                            <div class="proof-link-card">
                                <strong>Berkas Bukti Pembayaran: {{ $proof['filename'] ?? '-' }}</strong><br>
                                <a href="{{ $proof['url'] }}" target="_blank">Buka Berkas Asli</a>
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
                        Tidak ada data pendaftaran seminar yang sesuai dengan filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- LEMBAR PENGESAHAN KAPRODI --}}
    <table class="signature-table">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center;">
                <div>Subang, {{ now()->translatedFormat('d F Y') }}</div>
                <div style="margin-top: 4px; font-weight: bold;">Mengetahui,</div>
                <div style="font-weight: bold; margin-bottom: 60px;">Koordinator Program Studi</div>
                <div style="font-weight: bold; text-decoration: underline;">{{ $kaprodi?->name ?? '............................................' }}</div>
                <div style="font-size: 8.5pt; color: #4b5563;">NIP/NIDN. {{ $kaprodi?->identifier ?? '-' }}</div>
            </td>
        </tr>
    </table>

</div>
</body>
</html>

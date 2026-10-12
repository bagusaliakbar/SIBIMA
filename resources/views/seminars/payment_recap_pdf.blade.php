<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Bukti Pembayaran Seminar Proposal</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 12mm 15mm 12mm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.35;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* KOP SURAT RESMI (SERAGAM DENGAN DOKUMEN LAIN) */
        .kop-surat {
            width: 100%;
            margin-bottom: 14px;
            text-align: center;
        }

        .kop-img {
            width: 100%;
            height: auto;
            display: block;
        }

        .kop-table-wrapper {
            border-bottom: 3px double #000;
            padding-bottom: 5px;
        }

        .kop-surat table {
            width: 100%;
            border: none;
            margin-bottom: 0;
        }

        .kop-surat td {
            border: none !important;
            padding: 0 !important;
            vertical-align: middle;
        }

        .kop-surat .logo-cell {
            width: 80px;
            text-align: left;
        }

        .kop-surat .logo-img {
            width: 70px;
            height: auto;
        }

        .kop-surat .info-cell {
            text-align: center;
            padding-right: 80px !important;
        }

        .kop-surat .univ-name {
            font-size: 16px;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
        }

        .kop-surat .faculty-name {
            font-size: 20px;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
        }

        .kop-surat .accreditation {
            font-size: 11px;
            font-weight: bold;
            margin: 2px 0;
        }

        .kop-surat .address {
            font-size: 9px;
            margin: 1px 0;
        }

        .kop-surat .email {
            font-size: 9px;
            margin: 1px 0;
        }

        /* HEADER LAPORAN */
        .report-header {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 12px;
        }

        .report-title {
            font-size: 12pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .report-subtitle {
            font-size: 8pt;
            font-weight: 700;
            color: #475569;
            margin-top: 3px;
            letter-spacing: 0.3px;
        }

        /* METADATA BAR */
        .meta-box {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px 10px;
            margin-bottom: 14px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .meta-table td {
            border: none !important;
            padding: 2px 4px !important;
            font-size: 7.5pt;
            color: #334155;
            vertical-align: middle;
        }

        .meta-label {
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            font-size: 7pt;
        }

        .meta-val {
            font-weight: bold;
            color: #0f172a;
        }

        /* DATA TABLE */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 6px;
            border: 1px solid #0f172a;
            text-align: center;
        }

        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 6px;
            vertical-align: top;
            font-size: 8pt;
        }

        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .data-table tr {
            page-break-inside: avoid;
        }

        .col-no {
            width: 5%;
            text-align: center;
            font-weight: bold;
            color: #475569;
        }

        .col-npm {
            width: 16%;
            text-align: center;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            color: #0f172a;
            font-size: 8.5pt;
        }

        .col-nama {
            width: 24%;
            text-align: left;
        }

        .col-bukti {
            width: 55%;
            text-align: center;
        }

        .student-name {
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            font-size: 8.5pt;
            margin-bottom: 3px;
        }

        /* BUKTI PEMBAYARAN CARD */
        .proof-card {
            display: inline-block;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 5px;
            max-width: 98%;
            text-align: center;
        }

        .proof-img {
            max-height: 220px;
            max-width: 320px;
            width: auto;
            height: auto;
            display: block;
            margin: 0 auto;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }

        .proof-caption {
            font-size: 6.5pt;
            color: #64748b;
            margin-top: 3px;
            font-weight: 500;
        }

        .proof-link-card {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
            padding: 6px 8px;
            text-align: center;
            font-size: 7.5pt;
        }

        .proof-link-card a {
            color: #2563eb;
            text-decoration: underline;
            font-weight: bold;
            word-break: break-all;
        }

        .proof-empty {
            color: #94a3b8;
            font-style: italic;
            font-size: 7.5pt;
            padding: 8px;
        }


    </style>
</head>
<body>

    {{-- KOP SURAT RESMI (SERAGAM DENGAN DOKUMEN LAIN) --}}
    @php
        $kopBase64 = null;
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
    @endphp

    <div class="kop-surat">
        @if(!empty($kopBase64))
            <img src="data:image/png;base64,{{ $kopBase64 }}" class="kop-img" alt="Kop Surat FASILKOM UNSUB">
        @elseif(file_exists(public_path('kop_fasilkom.png')))
            <img src="{{ public_path('kop_fasilkom.png') }}" class="kop-img" alt="Kop Surat FASILKOM UNSUB">
        @elseif(file_exists(public_path('images/kop_fasilkom.png')))
            <img src="{{ public_path('images/kop_fasilkom.png') }}" class="kop-img" alt="Kop Surat FASILKOM UNSUB">
        @else
            <div class="kop-table-wrapper">
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
        @endif
    </div>

    {{-- HEADER LAPORAN --}}
    <div class="report-header">
        <h1 class="report-title">REKAPITULASI BUKTI PEMBAYARAN PENDAFTARAN SEMINAR PROPOSAL</h1>
        <div class="report-subtitle">SISTEM INFORMASI BIMBINGAN MAHASISWA (SIBIMA) - FAKULTAS ILMU KOMPUTER - UNIVERSITAS SUBANG</div>
    </div>

    {{-- METADATA BAR --}}
    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td style="width: 14%;" class="meta-label">Gelombang:</td>
                <td style="width: 36%;" class="meta-val">{{ $wave ? strtoupper($wave->name) : 'SEMUA GELOMBANG' }}</td>
                <td style="width: 16%;" class="meta-label">Tanggal Cetak:</td>
                <td style="width: 34%;" class="meta-val">{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
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
    </div>

    {{-- DATA TABLE (NO, NPM, NAMA, BUKTI PEMBAYARAN) --}}
    <table class="data-table">
        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th class="col-npm">NPM</th>
                <th class="col-nama">NAMA MAHASISWA</th>
                <th class="col-bukti">BUKTI PEMBAYARAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($applications as $index => $app)
                @php
                    $student = $app->thesis?->student;
                    $proof = $app->payment_proof_data ?? null;
                    $statusLabel = match($app->status) {
                        'approved' => ['class' => 'badge-success', 'text' => 'Disetujui'],
                        'rejected' => ['class' => 'badge-danger', 'text' => 'Perlu Revisi'],
                        default => ['class' => 'badge-pending', 'text' => 'Menunggu']
                    };
                @endphp
                <tr>
                    <td class="col-no">{{ $index + 1 }}</td>
                    <td class="col-npm">{{ $student?->identifier ?? '-' }}</td>
                    <td class="col-nama">
                        <div class="student-name">{{ $student?->name ?? 'Mahasiswa' }}</div>
                    </td>
                    <td class="col-bukti">
                        @if($proof && $proof['type'] === 'image' && !empty($proof['base64']))
                            <div class="proof-card">
                                <img src="{{ $proof['base64'] }}" class="proof-img" alt="Bukti Pembayaran {{ $student?->name }}">
                                <div class="proof-caption">
                                    Bukti Pembayaran Terverifikasi
                                    @if(!empty($proof['url']))
                                        • <a href="{{ $proof['url'] }}" target="_blank" style="color: #2563eb; text-decoration: underline;">Buka Berkas Asli ↗</a>
                                    @endif
                                </div>
                            </div>
                        @elseif($proof && $proof['type'] === 'url')
                            <div class="proof-link-card">
                                <div style="font-weight: bold; color: #1e293b; margin-bottom: 2px;">
                                    {{ $proof['is_gdrive'] ? '📁 Dokumen Bukti Bayar di Google Drive' : '🔗 Tautan Dokumen Bukti Bayar' }}
                                </div>
                                <div style="font-size: 7pt; color: #64748b; margin-bottom: 3px;">
                                    (Akses file dibatasi oleh mahasiswa, klik tautan di bawah untuk melihat)
                                </div>
                                <a href="{{ $proof['url'] }}" target="_blank">
                                    {{ \Illuminate\Support\Str::limit($proof['url'], 55) }}
                                </a>
                            </div>
                        @elseif($proof && $proof['type'] === 'pdf')
                            <div class="proof-link-card" style="background-color: #fef2f2; border-color: #fecaca;">
                                <div style="font-weight: bold; color: #b91c1c; margin-bottom: 2px;">
                                    📄 Berkas Bukti Pembayaran (Format PDF)
                                </div>
                                <a href="{{ $proof['url'] }}" target="_blank" style="color: #b91c1c;">
                                    Klik untuk membuka dokumen PDF ↗
                                </a>
                            </div>
                        @elseif($proof && $proof['type'] === 'file')
                            <div class="proof-link-card">
                                <div style="font-weight: bold; color: #1e293b; margin-bottom: 2px;">
                                    📎 Berkas Bukti Pembayaran: {{ $proof['filename'] ?? '-' }}
                                </div>
                                <a href="{{ $proof['url'] }}" target="_blank">
                                    Buka berkas asli ↗
                                </a>
                            </div>
                        @else
                            <div class="proof-empty">
                                — Belum ada bukti pembayaran diunggah —
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 25px; color: #64748b; font-style: italic;">
                        Tidak ada data pendaftaran seminar yang sesuai dengan filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>


</body>
</html>

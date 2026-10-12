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

        /* KOP SURAT RESMI */
        .kop-wrapper {
            width: 100%;
            margin-bottom: 12px;
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 8px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .kop-table td {
            border: none !important;
            padding: 0 !important;
            vertical-align: middle;
        }

        .kop-logo-cell {
            width: 75px;
            text-align: left;
        }

        .kop-logo-img {
            width: 65px;
            height: auto;
        }

        .kop-text-cell {
            text-align: center;
            padding-right: 75px !important;
        }

        .kop-univ {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin: 0;
            text-transform: uppercase;
        }

        .kop-faculty {
            font-size: 15pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.8px;
            margin: 2px 0;
            text-transform: uppercase;
        }

        .kop-accreditation {
            font-size: 8pt;
            font-weight: bold;
            color: #0284c7;
            margin: 1px 0;
        }

        .kop-address {
            font-size: 7.5pt;
            color: #475569;
            margin-top: 2px;
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
            width: 15%;
            text-align: center;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            color: #0f172a;
            font-size: 8.5pt;
        }

        .col-nama {
            width: 28%;
            text-align: left;
        }

        .col-bukti {
            width: 52%;
            text-align: center;
        }

        .student-name {
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            font-size: 8.5pt;
            margin-bottom: 3px;
        }

        .student-info {
            font-size: 7pt;
            color: #64748b;
            margin-bottom: 4px;
        }

        .thesis-title-box {
            background-color: #f1f5f9;
            border-left: 2px solid #3b82f6;
            padding: 3px 5px;
            border-radius: 3px;
            margin-top: 4px;
            font-size: 7pt;
            color: #334155;
            line-height: 1.25;
            font-style: italic;
        }

        /* BADGES */
        .badge {
            display: inline-block;
            padding: 1.5px 5px;
            font-size: 6.5pt;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-success {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .badge-pending {
            background-color: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .badge-danger {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        /* BUKTI PEMBAYARAN CARD */
        .proof-card {
            display: inline-block;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 4px;
            max-width: 95%;
            text-align: center;
        }

        .proof-img {
            max-height: 155px;
            max-width: 220px;
            width: auto;
            height: auto;
            display: block;
            margin: 0 auto;
            border-radius: 3px;
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

        /* SIGNATURE SECTION */
        .signature-section {
            width: 100%;
            margin-top: 18px;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .signature-table td {
            border: none !important;
            padding: 0 !important;
            vertical-align: top;
        }

        .sign-box {
            width: 220px;
            text-align: center;
            float: right;
        }

        .sign-date {
            font-size: 8pt;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .sign-role {
            font-size: 8pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }

        .sign-space {
            height: 55px;
        }

        .sign-name {
            font-size: 8.5pt;
            font-weight: bold;
            color: #0f172a;
            text-decoration: underline;
        }

        .sign-nip {
            font-size: 7.5pt;
            color: #475569;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT RESMI --}}
    @php
        $logoBase64 = null;
        if (file_exists(public_path('logo_unsub.png'))) {
            $logoBase64 = base64_encode(file_get_contents(public_path('logo_unsub.png')));
        }
    @endphp

    <div class="kop-wrapper">
        <table class="kop-table">
            <tr>
                <td class="kop-logo-cell">
                    @if($logoBase64)
                        <img src="data:image/png;base64,{{ $logoBase64 }}" class="kop-logo-img" alt="Logo Universitas Subang">
                    @endif
                </td>
                <td class="kop-text-cell">
                    <div class="kop-univ">YAYASAN PEMBINA PENDIDIKAN KUTAWALUYA</div>
                    <div class="kop-univ">UNIVERSITAS SUBANG</div>
                    <div class="kop-faculty">FAKULTAS ILMU KOMPUTER</div>
                    <div class="kop-accreditation">Terakreditasi "BAIK SEKALI" LAM INFOKOM (No. 110/SK/LAM-INFOKOM/Ak/S/VIII/2025)</div>
                    <div class="kop-address">Jl. R.A. Kartini No. 4 Subang 41211, Jawa Barat | Website: falkom.unsub.ac.id | Email: falkom@unsub.ac.id</div>
                </td>
            </tr>
        </table>
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
                        <div class="student-info">
                            Angkatan: {{ $student?->entry_year ?? '-' }}
                            • <span class="badge {{ $statusLabel['class'] }}">{{ $statusLabel['text'] }}</span>
                        </div>
                        @if($app->thesis?->title)
                            <div class="thesis-title-box">
                                <strong>Judul:</strong> {{ $app->thesis->final_title ?: $app->thesis->title }}
                            </div>
                        @endif
                    </td>
                    <td class="col-bukti">
                        @if($proof && $proof['type'] === 'image' && !empty($proof['base64']))
                            <div class="proof-card">
                                <img src="{{ $proof['base64'] }}" class="proof-img" alt="Bukti Pembayaran {{ $student?->name }}">
                                <div class="proof-caption">
                                    Scan/Foto Bukti Pembayaran Resmi
                                    @if(!empty($proof['filename']))
                                        • {{ $proof['filename'] }}
                                    @endif
                                </div>
                            </div>
                        @elseif($proof && $proof['type'] === 'url')
                            <div class="proof-link-card">
                                <div style="font-weight: bold; color: #1e293b; margin-bottom: 2px;">
                                    {{ $proof['is_gdrive'] ? '📁 Dokumen Bukti Bayar di Google Drive' : '🔗 Tautan Dokumen Bukti Bayar' }}
                                </div>
                                <a href="{{ $proof['url'] }}" target="_blank">
                                    {{ \Illuminate\Support\Str::limit($proof['url'], 55) }}
                                </a>
                            </div>
                        @elseif($proof && $proof['type'] === 'pdf')
                            <div class="proof-link-card" style="background-color: #fef2f2; border-color: #fecaca;">
                                <div style="font-weight: bold; color: #b91c1c; margin-bottom: 2px;">
                                    📄 Berkas Bukti Pembayaran Terlampir (Format PDF)
                                </div>
                                <a href="{{ $proof['url'] }}" target="_blank" style="color: #b91c1c;">
                                    Klik untuk membuka file PDF: {{ $proof['filename'] ?? 'Lihat Dokumen' }}
                                </a>
                            </div>
                        @elseif($proof && $proof['type'] === 'file')
                            <div class="proof-link-card">
                                <div style="font-weight: bold; color: #1e293b; margin-bottom: 2px;">
                                    📎 Berkas Bukti Pembayaran: {{ $proof['filename'] ?? '-' }}
                                </div>
                                <a href="{{ $proof['url'] }}" target="_blank">
                                    Klik untuk membuka berkas asli
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

    {{-- TANDA TANGAN / PENGESAHAN --}}
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td style="width: 60%;">
                    <div style="font-size: 7.5pt; color: #64748b; font-style: italic; margin-top: 15px;">
                        * Dokumen ini dicetak otomatis secara resmi melalui Sistem Informasi Bimbingan Mahasiswa (SIBIMA).
                    </div>
                </td>
                <td style="width: 40%;">
                    <div class="sign-box">
                        <div class="sign-date">Subang, {{ now()->translatedFormat('d F Y') }}</div>
                        <div class="sign-role">Ketua Program Studi,</div>
                        <div class="sign-space"></div>
                        <div class="sign-name">{{ $kaprodi?->name ?? 'Ketua Program Studi' }}</div>
                        <div class="sign-nip">NIDN/NIP. {{ $kaprodi?->identifier ?? '-' }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

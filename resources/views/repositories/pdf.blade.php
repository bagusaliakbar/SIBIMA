<!DOCTYPE html>
<html>
<head>
    <title>Laporan Katalog Pustaka & Skripsi</title>
    <style>
        @page {
            size: landscape;
            margin: 1cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #333;
            line-height: 1.3;
        }
        .kop-surat {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
            margin-bottom: 15px;
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
            width: 90px;
            text-align: left;
        }
        .kop-surat .logo-img {
            width: 80px;
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
        .kop-surat .address, .kop-surat .email {
            font-size: 9px;
            margin: 1px 0;
        }
        
        .report-title {
            text-align: center;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 14px;
            text-decoration: underline;
        }
        .filter-info {
            text-align: center;
            font-size: 9pt;
            color: #666;
            margin-bottom: 15px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #999;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
            font-size: 8.5pt;
        }
        table.data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 8.5pt;
        }
        .text-center {
            text-align: center !important;
        }
        .topic-badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7.5pt;
            font-weight: bold;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .footer {
            margin-top: 20px;
            width: 100%;
            page-break-inside: avoid;
        }
        .footer-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }
        .footer-table td {
            border: none;
            width: 50%;
            vertical-align: top;
        }
        .signature-container {
            text-align: right;
            padding-right: 40px;
        }
        .signature-box {
            display: inline-block;
            text-align: center;
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
        Laporan Katalog Pustaka & Rekap Judul Skripsi
    </div>
    
    <div class="filter-info">
        @php
            $filterLabels = [];
            if (!empty($year)) $filterLabels[] = "Angkatan: {$year}";
            if (!empty($advisor)) $filterLabels[] = "Pembimbing: {$advisor}";
            if (!empty($topic) && $topic !== 'all') $filterLabels[] = "Topik: " . ucfirst($topic);
            if (!empty($search)) $filterLabels[] = "Pencarian: '{$search}'";
        @endphp
        <span>Total Arsip: <strong>{{ $repositories->count() }} Data</strong></span>
        @if(!empty($filterLabels))
            <span> | Filter: {{ implode(' | ', $filterLabels) }}</span>
        @endif
        <span> | Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 8%;">Angkatan</th>
                <th style="width: 12%;">NPM</th>
                <th style="width: 18%;">Nama Mahasiswa</th>
                <th style="width: 32%;">Judul Skripsi</th>
                <th style="width: 12%;">Kategori Topik</th>
                <th style="width: 14%;">Dosen Pembimbing</th>
            </tr>
        </thead>
        <tbody>
            @forelse($repositories as $index => $repo)
                @php
                    $badge = $repo->topic_badge;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $repo->year ?? '-' }}</td>
                    <td class="text-center" style="font-family: monospace;">{{ $repo->identifier ?? '-' }}</td>
                    <td><strong>{{ $repo->name }}</strong></td>
                    <td>{{ $repo->title }}</td>
                    <td class="text-center">
                        <span class="topic-badge">{{ $badge['label'] ?? 'Umum' }}</span>
                    </td>
                    <td>
                        @if($repo->pembimbing1)
                            <div><small><strong>P1:</strong> {{ $repo->pembimbing1 }}</small></div>
                        @endif
                        @if($repo->pembimbing2)
                            <div><small><strong>P2:</strong> {{ $repo->pembimbing2 }}</small></div>
                        @endif
                        @if(!$repo->pembimbing1 && !$repo->pembimbing2)
                            <small class="text-muted">-</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">Tidak ada data katalog pustaka yang sesuai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td></td>
                <td>
                    <div class="signature-container">
                        <div class="signature-box">
                            <div>Subang, {{ now()->translatedFormat('d F Y') }}</div>
                            <div style="margin-top: 5px;">Mengetahui,</div>
                            <div style="font-weight: bold; margin-bottom: 60px;">Ketua Program Studi Sistem Informasi</div>
                            
                            <div style="font-weight: bold; text-decoration: underline;">
                                {{ $kaprodi->name ?? 'Drs. M. Hairiyanov, MT' }}
                            </div>
                            @if($kaprodi && $kaprodi->identifier)
                                <div>NIDN / NIP. {{ $kaprodi->identifier }}</div>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

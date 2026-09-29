<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Radar Keaktifan Bimbingan Skripsi - SIBIMA</title>
    <style>
        @page {
            size: landscape;
            margin: 0.8cm 1cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8pt;
            color: #1e293b;
            line-height: 1.35;
        }
        .kop-surat {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 4px;
            margin-bottom: 12px;
        }
        .kop-surat table {
            width: 100%;
            border: none;
            margin: 0;
        }
        .kop-surat td {
            border: none !important;
            padding: 0 !important;
            vertical-align: middle;
        }
        .kop-surat .logo-cell {
            width: 75px;
            text-align: left;
        }
        .kop-surat .logo-img {
            width: 65px;
            height: auto;
        }
        .kop-surat .info-cell {
            text-align: center;
            padding-right: 65px !important;
        }
        .kop-surat .univ-name {
            font-size: 14px;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            letter-spacing: 0.5px;
        }
        .kop-surat .faculty-name {
            font-size: 16px;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
            color: #0f172a;
        }
        .kop-surat .accreditation {
            font-size: 8.5px;
            margin: 1px 0 0 0;
            font-style: italic;
            color: #475569;
        }
        .kop-surat .address {
            font-size: 7.5px;
            margin: 2px 0 0 0;
            color: #64748b;
        }
        .report-title {
            text-align: center;
            margin: 8px 0 12px 0;
        }
        .report-title h2 {
            font-size: 12.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            color: #0f172a;
        }
        .report-title p {
            font-size: 8.5pt;
            color: #475569;
            margin: 3px 0 0 0;
        }
        .kpi-row {
            width: 100%;
            margin-bottom: 12px;
        }
        .kpi-row td {
            width: 25%;
            padding: 6px 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        .kpi-row .kpi-num {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
        }
        .kpi-row .kpi-lbl {
            font-size: 7pt;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }
        .section-header {
            font-size: 9.5pt;
            font-weight: bold;
            color: #0f172a;
            margin: 12px 0 5px 0;
            padding-bottom: 2px;
            border-bottom: 1.5px solid #cbd5e1;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            font-size: 7.5pt;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            color: #334155;
        }
        table.data-table td {
            border: 1px solid #e2e8f0;
            padding: 4px 6px;
            font-size: 7.5pt;
            vertical-align: middle;
        }
        .badge {
            display: inline-block;
            padding: 1.5px 5px;
            border-radius: 4px;
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-emerald { background: #d1fae5; color: #065f46; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-amber { background: #fef3c7; color: #92400e; }
        .badge-rose { background: #ffe4e6; color: #9f1239; }
        .badge-slate { background: #f1f5f9; color: #475569; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .ttd-container {
            width: 100%;
            margin-top: 15px;
            page-break-inside: avoid;
        }
        .ttd-container td {
            border: none;
            vertical-align: top;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="kop-surat">
        <table>
            <tr>
                <td class="logo-cell">
                    @php
                        $logoPath = public_path('images/unsub.png');
                        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
                    @endphp
                    @if($logoData)
                        <img src="data:image/png;base64,{{ $logoData }}" class="logo-img" alt="Logo UNSUB">
                    @endif
                </td>
                <td class="info-cell">
                    <div class="univ-name">YAYASAN KUTAWAGIH SUBANG</div>
                    <div class="faculty-name">UNIVERSITAS SUBANG - FAKULTAS ILMU KOMPUTER</div>
                    <div class="accreditation">Program Studi Sistem Informasi Terakreditasi "Baik Sekali" (LAM INFOKOM)</div>
                    <div class="address">Jl. R.A. Kartini No. 2 Subang 41285 Jawa Barat | Telp: (0260) 417855 | Email: fasilkom@unsub.ac.id</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- TITLE -->
    <div class="report-title">
        <h2>Laporan Radar Keaktifan & Peringkat Bimbingan Skripsi</h2>
        <p>Periode Evaluasi: <strong>{{ $period_label }}</strong> | Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <!-- KPI STATS -->
    <table class="kpi-row">
        <tr>
            <td>
                <div class="kpi-num">{{ number_format($kpi['total_sessions_in_period']) }}</div>
                <div class="kpi-lbl">Total Sesi Terlaksana</div>
            </td>
            <td>
                <div class="kpi-num">{{ $kpi['avg_sessions_per_dosen'] }}</div>
                <div class="kpi-lbl">Rata-Rata Sesi per Dosen</div>
            </td>
            <td>
                <div class="kpi-num" style="color: #10b981;">{{ $kpi['top_student_name'] }} ({{ $kpi['top_student_sessions'] }}x)</div>
                <div class="kpi-lbl">Mahasiswa Teraktif Periode Ini</div>
            </td>
            <td>
                <div class="kpi-num" style="color: #e11d48;">{{ $kpi['inactive_students_count'] }} Mahasiswa</div>
                <div class="kpi-lbl">Perlu Perhatian (Pasif / Kritis)</div>
            </td>
        </tr>
    </table>

    <!-- 1. TOP MAHASISWA TERAJIN -->
    <div class="section-header">A. TOP 10 MAHASISWA TERAJIN (KONSISTENSI SESI BIMBINGAN)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th>Nama Mahasiswa</th>
                <th style="width: 80px;">NPM</th>
                <th style="width: 50px;" class="text-center">Angkatan</th>
                <th style="width: 70px;" class="text-center">Sesi Periode</th>
                <th style="width: 80px;" class="text-center">P1 / P2</th>
                <th style="width: 60px;" class="text-center">Total Sesi</th>
                <th>Dosen Pembimbing</th>
                <th style="width: 75px;">Bimbingan Terakhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($top_students->take(10) as $idx => $s)
                <tr>
                    <td class="text-center font-bold">{{ $idx + 1 }}</td>
                    <td><strong>{{ $s['name'] }}</strong></td>
                    <td style="font-family: monospace;">{{ $s['identifier'] ?: '-' }}</td>
                    <td class="text-center">{{ $s['entry_year'] ?: '-' }}</td>
                    <td class="text-center" style="font-weight: bold; color: #10b981;">{{ $s['sessions_in_period'] }} Sesi</td>
                    <td class="text-center">{{ $s['p1_sessions'] }} / {{ $s['p2_sessions'] }}</td>
                    <td class="text-center font-bold">{{ $s['total_all_time'] }}x</td>
                    <td>P1: {{ $s['pembimbing1_name'] ?: '-' }}<br>P2: {{ $s['pembimbing2_name'] ?: '-' }}</td>
                    <td>{{ $s['last_session_at'] ? \Carbon\Carbon::parse($s['last_session_at'])->format('d/m/Y') : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center" style="padding: 10px;">Tidak ada data mahasiswa bimbingan pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- 2. MAHASISWA PASIF / KRITIS -->
    <div class="section-header">B. MAHASISWA PASIF / PERLU PERHATIAN KHUSUS (GAP BIMBINGAN LAMA / MANDOR)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th>Nama Mahasiswa</th>
                <th style="width: 80px;">NPM</th>
                <th style="width: 50px;" class="text-center">Angkatan</th>
                <th style="width: 90px;" class="text-center">Status Keaktifan</th>
                <th style="width: 90px;" class="text-center">Hari Tanpa Bimbingan</th>
                <th>Dosen Pembimbing</th>
                <th>Judul Skripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inactive_students->take(10) as $idx => $s)
                <tr>
                    <td class="text-center font-bold">{{ $idx + 1 }}</td>
                    <td><strong>{{ $s['name'] }}</strong></td>
                    <td style="font-family: monospace;">{{ $s['identifier'] ?: '-' }}</td>
                    <td class="text-center">{{ $s['entry_year'] ?: '-' }}</td>
                    <td class="text-center">
                        <span class="badge badge-rose">{{ $s['health_status'] }}</span>
                    </td>
                    <td class="text-center font-bold" style="color: #e11d48;">
                        {{ $s['days_since_last'] !== null ? $s['days_since_last'] . ' Hari' : 'Belum Pernah' }}
                    </td>
                    <td>P1: {{ $s['pembimbing1_name'] ?: '-' }}<br>P2: {{ $s['pembimbing2_name'] ?: '-' }}</td>
                    <td style="font-size: 7pt;">{{ \Illuminate\Support\Str::limit($s['title'], 65) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center" style="padding: 10px;">Seluruh mahasiswa aktif melakukan bimbingan secara berkala.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="page-break-after: always;"></div>

    <!-- 3. TOP DOSEN TERAJIN -->
    <div class="section-header">C. PERINGKAT KEAKTIFAN DOSEN PEMBIMBING (TERAJIN MEMBIMBING)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th>Nama Dosen Pembimbing</th>
                <th style="width: 90px;">NIDN / Kode</th>
                <th style="width: 80px;" class="text-center">Sesi Periode Ini</th>
                <th style="width: 90px;" class="text-center">Mahasiswa Terbimbing</th>
                <th style="width: 80px;" class="text-center">Total Mhs Aktif</th>
                <th style="width: 80px;" class="text-center">Kategori Keaktifan</th>
                <th style="width: 85px;">Sesi Terakhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($top_dosens as $idx => $d)
                <tr>
                    <td class="text-center font-bold">{{ $idx + 1 }}</td>
                    <td><strong>{{ $d['name'] }}</strong></td>
                    <td style="font-family: monospace;">{{ $d['identifier'] ?: '-' }}</td>
                    <td class="text-center" style="font-weight: bold; color: #2563eb;">{{ $d['sessions_in_period'] }} Sesi</td>
                    <td class="text-center">{{ $d['unique_students_in_period'] }} Mahasiswa</td>
                    <td class="text-center">{{ $d['supervised_count'] }} Mahasiswa</td>
                    <td class="text-center">
                        <span class="badge badge-{{ $d['activity_color'] }}">{{ $d['activity_level'] }}</span>
                    </td>
                    <td>{{ $d['last_session_at'] ? \Carbon\Carbon::parse($d['last_session_at'])->format('d/m/Y') : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center" style="padding: 10px;">Tidak ada data bimbingan dosen pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- 4. DOSEN PERLU PERHATIAN -->
    @if(count($inactive_dosens) > 0)
        <div class="section-header">D. DOSEN PEMBIMBING DENGAN MAHASISWA AKTIF YANG PERLU KOORDINASI (PASIF)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;" class="text-center">No</th>
                    <th>Nama Dosen Pembimbing</th>
                    <th style="width: 90px;">NIDN</th>
                    <th style="width: 80px;" class="text-center">Mahasiswa Menunggu</th>
                    <th style="width: 80px;" class="text-center">Sesi Periode Ini</th>
                    <th style="width: 95px;" class="text-center">Hari Tanpa Sesi</th>
                    <th style="width: 90px;">Sesi Terakhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inactive_dosens as $idx => $d)
                    <tr>
                        <td class="text-center font-bold">{{ $idx + 1 }}</td>
                        <td><strong>{{ $d['name'] }}</strong></td>
                        <td style="font-family: monospace;">{{ $d['identifier'] ?: '-' }}</td>
                        <td class="text-center font-bold" style="color: #e11d48;">{{ $d['supervised_count'] }} Mahasiswa</td>
                        <td class="text-center">{{ $d['sessions_in_period'] }} Sesi</td>
                        <td class="text-center" style="color: #e11d48;">{{ $d['days_since_last'] !== null ? $d['days_since_last'] . ' Hari Lalu' : 'Belum Pernah' }}</td>
                        <td>{{ $d['last_session_at'] ? \Carbon\Carbon::parse($d['last_session_at'])->format('d/m/Y') : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- TANDA TANGAN KAPRODI -->
    <div class="ttd-container">
        <table>
            <tr>
                <td style="width: 65%;"></td>
                <td style="width: 35%; text-align: center;">
                    Subang, {{ now()->translatedFormat('d F Y') }}<br>
                    <strong>Ketua Program Studi Sistem Informasi</strong><br><br><br><br>
                    <strong><u>{{ $kaprodi?->name ?? 'Dr. H. Yusman, M.Kom.' }}</u></strong><br>
                    <span>NIDN: {{ $kaprodi?->identifier ?? '-' }}</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

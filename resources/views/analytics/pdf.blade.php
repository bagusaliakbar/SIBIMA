<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Statistik & Grafik Data SIBIMA</title>
    <style>
        @page {
            size: landscape;
            margin: 0.8cm 1cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8.5pt;
            color: #1e293b;
            line-height: 1.35;
        }
        .kop-surat {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 4px;
            margin-bottom: 14px;
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
            width: 80px;
            text-align: left;
        }
        .kop-surat .logo-img {
            width: 70px;
            height: auto;
        }
        .kop-surat .info-cell {
            text-align: center;
            padding-right: 70px !important;
        }
        .kop-surat .univ-name {
            font-size: 15px;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            letter-spacing: 0.5px;
        }
        .kop-surat .faculty-name {
            font-size: 18px;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
            color: #0f172a;
        }
        .kop-surat .accreditation {
            font-size: 10px;
            font-weight: bold;
            color: #475569;
            margin: 1px 0;
        }
        .kop-surat .address {
            font-size: 8.5px;
            color: #64748b;
            margin: 0;
        }
        .report-header {
            text-align: center;
            margin-bottom: 12px;
        }
        .report-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin: 0 0 4px 0;
        }
        .report-meta {
            font-size: 8pt;
            color: #64748b;
        }
        .filter-badges {
            margin-bottom: 12px;
            padding: 6px 10px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-size: 7.5pt;
        }
        .filter-badges span {
            display: inline-block;
            margin-right: 12px;
            color: #334155;
        }
        .filter-badges strong {
            color: #0f172a;
        }
        .kpi-grid {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: separate;
            border-spacing: 6px 0;
        }
        .kpi-grid td {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 8px;
            text-align: center;
            background-color: #ffffff;
            vertical-align: top;
        }
        .kpi-grid .kpi-num {
            font-size: 13pt;
            font-weight: bold;
            color: #ea580c;
            margin-bottom: 2px;
        }
        .kpi-grid .kpi-label {
            font-size: 7pt;
            text-transform: uppercase;
            font-weight: 600;
            color: #475569;
            line-height: 1.1;
        }
        .section-title {
            font-size: 9.5pt;
            font-weight: bold;
            color: #0f172a;
            margin: 10px 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-left: 3px solid #ea580c;
            padding-left: 6px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 7.5pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7pt;
            text-align: center;
        }
        table.data-table td.center {
            text-align: center;
        }
        table.data-table td.num {
            text-align: right;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #fafbfc;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            margin-top: 15px;
            width: 100%;
        }
        .footer table {
            width: 100%;
            border: none;
        }
        .footer td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .footer .sign-box {
            text-align: center;
            width: 250px;
        }
        .footer .sign-box .date {
            margin-bottom: 45px;
            font-size: 8pt;
        }
        .footer .sign-box .name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 8.5pt;
        }
        .footer .sign-box .nip {
            font-size: 7.5pt;
            color: #475569;
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
                        $logoPath = public_path('logo_unsub.png');
                        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
                    @endphp
                    @if($logoData)
                        <img src="data:image/png;base64,{{ $logoData }}" class="logo-img" alt="Logo UNSUB">
                    @endif
                </td>
                <td class="info-cell">
                    <h2 class="univ-name">UNIVERSITAS SUBANG</h2>
                    <h1 class="faculty-name">FAKULTAS ILMU KOMPUTER</h1>
                    <p class="accreditation">TERAKREDITASI BAIK SEKALI</p>
                    <p class="address">Jl. Raden Ajeng Kartini No. 4, Subang, Jawa Barat 41285</p>
                    <p class="address">Website: fti.unsub.ac.id | Email: fasilkom@unsub.ac.id</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- REPORT TITLE -->
    <div class="report-header">
        <div class="report-title">Laporan Komprehensif Statistik & Grafik Data SIBIMA</div>
        <div class="report-meta">Sistem Informasi Bimbingan Tugas Akhir & Skripsi Mahasiswa</div>
    </div>

    <!-- FILTER PARAMETERS -->
    <div class="filter-badges">
        <span><strong>Gelombang:</strong> {{ $filters['wave_name'] ?? 'Semua Gelombang' }}</span>
        <span><strong>Angkatan:</strong> {{ !empty($filters['entry_year']) && $filters['entry_year'] !== 'all' ? 'Angkatan ' . $filters['entry_year'] : 'Semua Angkatan' }}</span>
        <span><strong>Dosen Pembimbing:</strong> {{ $filters['dosen_name'] ?? 'Semua Dosen' }}</span>
        <span><strong>Status Skripsi:</strong> {{ ucfirst($filters['status'] ?? 'Semua Status') }}</span>
        <span><strong>Dicetak Pada:</strong> {{ now()->translatedFormat('d F Y H:i') }} WIB</span>
    </div>

    <!-- KPI CARDS GRID -->
    <table class="kpi-grid">
        <tr>
            <td>
                <div class="kpi-num">{{ $kpi['totalStudents'] ?? 0 }}</div>
                <div class="kpi-label">Total Mahasiswa</div>
            </td>
            <td>
                <div class="kpi-num" style="color: #2563eb;">{{ $kpi['activeTheses'] ?? 0 }}</div>
                <div class="kpi-label">Skripsi Aktif</div>
            </td>
            <td>
                <div class="kpi-num" style="color: #059669;">{{ $kpi['seminarDone'] ?? 0 }}</div>
                <div class="kpi-label">Sudah Seminar</div>
            </td>
            <td>
                <div class="kpi-num" style="color: #d97706;">{{ $kpi['seminarPending'] ?? 0 }}</div>
                <div class="kpi-label">Belum Seminar</div>
            </td>
            <td>
                <div class="kpi-num" style="color: #7c3aed;">{{ $kpi['defenseDone'] ?? 0 }}</div>
                <div class="kpi-label">Sudah Sidang</div>
            </td>
            <td>
                <div class="kpi-num" style="color: #0284c7;">{{ $kpi['completedTheses'] ?? 0 }}</div>
                <div class="kpi-label">Sudah Lulus</div>
            </td>
            <td>
                <div class="kpi-num" style="color: #dc2626;">{{ $kpi['criticalMentoringStudents'] ?? 0 }}</div>
                <div class="kpi-label">Kritis Bimbingan</div>
            </td>
            <td>
                <div class="kpi-num" style="color: #16a34a;">{{ $kpi['onTimePercentage'] ?? 0 }}%</div>
                <div class="kpi-label">Tepat Waktu</div>
            </td>
        </tr>
    </table>

    <!-- SECTION 1: SEMINAR STATUS BY ADVISOR (P1 & P2) -->
    <div class="section-title">1. Rekapitulasi Mahasiswa Sudah vs Belum Seminar Berdasarkan Pembimbing 1 & 2</div>
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">No</th>
                <th rowspan="2" style="text-align: left;">Nama Dosen Pembimbing</th>
                <th colspan="4" style="background-color: #fed7aa; color: #9a3412;">Sebagai Pembimbing 1 (P1)</th>
                <th colspan="4" style="background-color: #bae6fd; color: #0369a1;">Sebagai Pembimbing 2 (P2)</th>
            </tr>
            <tr>
                <th style="width: 50px;">Total</th>
                <th style="width: 55px;">Sudah</th>
                <th style="width: 55px;">Belum</th>
                <th style="width: 50px;">% Selesai</th>
                <th style="width: 50px;">Total</th>
                <th style="width: 55px;">Sudah</th>
                <th style="width: 55px;">Belum</th>
                <th style="width: 50px;">% Selesai</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($seminarByAdvisor as $name => $item)
                @php
                    $p1Total = ($item['p1_done'] ?? 0) + ($item['p1_pending'] ?? 0);
                    $p2Total = ($item['p2_done'] ?? 0) + ($item['p2_pending'] ?? 0);
                    $p1Rate = $p1Total > 0 ? round((($item['p1_done'] ?? 0) / $p1Total) * 100, 1) : 0;
                    $p2Rate = $p2Total > 0 ? round((($item['p2_done'] ?? 0) / $p2Total) * 100, 1) : 0;
                @endphp
                <tr>
                    <td class="center">{{ $no++ }}</td>
                    <td><strong>{{ $name }}</strong></td>
                    <td class="center">{{ $p1Total }}</td>
                    <td class="center" style="color: #059669; font-weight: bold;">{{ $item['p1_done'] ?? 0 }}</td>
                    <td class="center" style="color: #d97706; font-weight: bold;">{{ $item['p1_pending'] ?? 0 }}</td>
                    <td class="center">{{ $p1Rate }}%</td>
                    <td class="center">{{ $p2Total }}</td>
                    <td class="center" style="color: #059669; font-weight: bold;">{{ $item['p2_done'] ?? 0 }}</td>
                    <td class="center" style="color: #d97706; font-weight: bold;">{{ $item['p2_pending'] ?? 0 }}</td>
                    <td class="center">{{ $p2Rate }}%</td>
                </tr>
            @empty
                <tr><td colspan="10" class="center">Tidak ada data pembimbing yang cocok dengan filter.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- SECTION 2: COHORT PROGRESS -->
    <div class="section-title">2. Rekapitulasi Mahasiswa Sudah vs Belum Seminar & Sidang per Angkatan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Tahun Angkatan</th>
                <th>Total Mahasiswa</th>
                <th style="color: #059669;">Sudah Seminar</th>
                <th style="color: #d97706;">Belum Seminar</th>
                <th>Rasio Seminar (%)</th>
                <th style="color: #2563eb;">Sudah Sidang / Lulus</th>
                <th style="color: #dc2626;">Belum Sidang</th>
                <th>Rasio Sidang (%)</th>
            </tr>
        </thead>
        <tbody>
            @php $noCohort = 1; @endphp
            @forelse($cohortProgress as $year => $stat)
                @php
                    $tot = $stat['total'] ?? 0;
                    $semDone = $stat['seminar_done'] ?? 0;
                    $semPending = $stat['seminar_pending'] ?? 0;
                    $defDone = $stat['defense_done'] ?? 0;
                    $defPending = $stat['defense_pending'] ?? 0;
                    $semPercent = $tot > 0 ? round(($semDone / $tot) * 100, 1) : 0;
                    $defPercent = $tot > 0 ? round(($defDone / $tot) * 100, 1) : 0;
                @endphp
                <tr>
                    <td class="center">{{ $noCohort++ }}</td>
                    <td class="center"><strong>Angkatan {{ $year }}</strong></td>
                    <td class="center font-bold">{{ $tot }}</td>
                    <td class="center" style="color: #059669; font-weight: bold;">{{ $semDone }}</td>
                    <td class="center" style="color: #d97706;">{{ $semPending }}</td>
                    <td class="center">{{ $semPercent }}%</td>
                    <td class="center" style="color: #2563eb; font-weight: bold;">{{ $defDone }}</td>
                    <td class="center" style="color: #dc2626;">{{ $defPending }}</td>
                    <td class="center">{{ $defPercent }}%</td>
                </tr>
            @empty
                <tr><td colspan="9" class="center">Tidak ada data angkatan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- SECTION 3: UNFINISHED STUDENTS PER ADVISOR -->
    <div class="section-title">3. Rekapitulasi Mahasiswa Belum Lulus per Dosen Pembimbing</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="text-align: left;">Nama Dosen Pembimbing</th>
                <th>Belum Lulus (P1)</th>
                <th>Belum Lulus (P2)</th>
                <th>Total Belum Lulus</th>
                <th>Batas Kuota</th>
                <th>Status Beban</th>
            </tr>
        </thead>
        <tbody>
            @php $noUnfinished = 1; @endphp
            @forelse($unfinishedByAdvisor as $dosenName => $info)
                @php
                    $p1 = $info['p1'] ?? 0;
                    $p2 = $info['p2'] ?? 0;
                    $total = $p1 + $p2;
                    $quota = $info['quota'] ?? 10;
                    $isOverload = $total >= $quota;
                @endphp
                <tr>
                    <td class="center">{{ $noUnfinished++ }}</td>
                    <td><strong>{{ $dosenName }}</strong></td>
                    <td class="center">{{ $p1 }}</td>
                    <td class="center">{{ $p2 }}</td>
                    <td class="center font-bold" style="{{ $isOverload ? 'color: #dc2626;' : '' }}">{{ $total }}</td>
                    <td class="center">{{ $quota }}</td>
                    <td class="center">
                        @if($isOverload)
                            <span style="color: #dc2626; font-weight: bold;">Penuh / Overload</span>
                        @elseif($total >= ($quota * 0.8))
                            <span style="color: #d97706; font-weight: bold;">Mendekati Kuota</span>
                        @else
                            <span style="color: #059669;">Tersedia</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="center">Tidak ada data beban bimbingan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- SECTION 4: DEFENSE SCORES DISTRIBUTION -->
    <div class="section-title">4. Distribusi Nilai Kelulusan Sidang Skripsi</div>
    <table class="data-table" style="width: 65%;">
        <thead>
            <tr>
                <th>Grade / Nilai</th>
                <th>Rentang Nilai</th>
                <th>Jumlah Mahasiswa</th>
                <th>Persentase</th>
            </tr>
        </thead>
        <tbody>
            @php
                $dist = $scoreDistribution ?? ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];
                $totScore = array_sum($dist);
            @endphp
            <tr>
                <td class="center"><strong>Grade A</strong></td>
                <td class="center">80.00 - 100.00</td>
                <td class="center" style="font-weight: bold; color: #059669;">{{ $dist['A'] ?? 0 }}</td>
                <td class="center">{{ $totScore > 0 ? round((($dist['A'] ?? 0)/$totScore)*100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td class="center"><strong>Grade B</strong></td>
                <td class="center">70.00 - 79.99</td>
                <td class="center" style="font-weight: bold; color: #2563eb;">{{ $dist['B'] ?? 0 }}</td>
                <td class="center">{{ $totScore > 0 ? round((($dist['B'] ?? 0)/$totScore)*100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td class="center"><strong>Grade C</strong></td>
                <td class="center">60.00 - 69.99</td>
                <td class="center" style="font-weight: bold; color: #d97706;">{{ $dist['C'] ?? 0 }}</td>
                <td class="center">{{ $totScore > 0 ? round((($dist['C'] ?? 0)/$totScore)*100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td class="center"><strong>Grade D</strong></td>
                <td class="center">50.00 - 59.99</td>
                <td class="center" style="font-weight: bold; color: #ea580c;">{{ $dist['D'] ?? 0 }}</td>
                <td class="center">{{ $totScore > 0 ? round((($dist['D'] ?? 0)/$totScore)*100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td class="center"><strong>Grade E</strong></td>
                <td class="center">0.00 - 49.99</td>
                <td class="center" style="font-weight: bold; color: #dc2626;">{{ $dist['E'] ?? 0 }}</td>
                <td class="center">{{ $totScore > 0 ? round((($dist['E'] ?? 0)/$totScore)*100, 1) : 0 }}%</td>
            </tr>
        </tbody>
    </table>

    <!-- SIGNATURE FOOTER -->
    <div class="footer">
        <table>
            <tr>
                <td></td>
                <td class="sign-box">
                    <div class="date">Subang, {{ now()->translatedFormat('d F Y') }}<br>Ketua Program Studi Ilmu Komputer,</div>
                    <div class="name">{{ $kaprodi->name ?? 'Ketua Program Studi' }}</div>
                    <div class="nip">NIDN/NIP. {{ $kaprodi->identifier ?? '-' }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

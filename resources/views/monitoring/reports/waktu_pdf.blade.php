<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Analisis Masa Studi & Tren Durasi Skripsi</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 8.5pt; color: #1e293b; line-height: 1.3; }
        .header { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 15px; }
        .header h2 { margin: 0 0 4px 0; font-size: 13pt; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; }
        .header h3 { margin: 0 0 4px 0; font-size: 9.5pt; font-weight: 700; color: #334155; text-transform: uppercase; }
        .header p { margin: 0; font-size: 8pt; color: #64748b; }
        .meta-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 6px 12px; margin-bottom: 15px; font-size: 8pt; }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { padding: 2px 4px; border: none; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; }
        table.data-table th { background-color: #0f172a; color: #ffffff; font-weight: bold; font-size: 8pt; text-align: center; }
        table.data-table tr:nth-child(even) { background-color: #f8fafc; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .badge { display: inline-block; padding: 2px 6px; font-size: 7.5pt; font-weight: bold; border-radius: 3px; }
        .badge-success { background-color: #dcfce7; color: #15803d; }
        .footer { margin-top: 25px; width: 100%; }
        .sign-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .sign-table td { border: none; text-align: center; font-size: 8.5pt; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN ANALISIS MASA STUDI & TREN GELOMBANG KELULUSAN</h2>
        <h3>SISTEM INFORMASI BIMBINGAN MAHASISWA (SIBIMA) - FAKULTAS ILMU KOMPUTER</h3>
        <p>Universitas Subang • Tanggal Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td width="20%"><strong>Tujuan Evaluasi</strong></td>
                <td width="30%">: Analisis Ketepatan Waktu Lulus Mahasiswa</td>
                <td width="20%"><strong>Total Gelombang</strong></td>
                <td width="30%">: {{ count($data) }} Gelombang Pendaftaran</td>
            </tr>
            <tr>
                <td><strong>Ambang Batas Tepat</strong></td>
                <td>: Masa Studi &le; 4.0 Tahun</td>
                <td><strong>Dicetak Oleh</strong></td>
                <td>: {{ auth()->user()->name ?? 'Administrator' }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="30">NO</th>
                <th>NAMA GELOMBANG</th>
                <th width="140">PERIODE TANGGAL</th>
                <th width="80">TOTAL LULUS</th>
                <th width="120">RATA-RATA DURASI</th>
                <th width="90">TEPAT WAKTU (&le;4 Thn)</th>
                <th width="90">TERLAMBAT (>4 Thn)</th>
                <th width="110">% TEPAT WAKTU</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $item->wave_name }}</td>
                    <td class="text-center">{{ $item->period }}</td>
                    <td class="text-center font-bold">{{ $item->completed_count }}</td>
                    <td class="text-center font-bold" style="color: #4338ca;">{{ $item->avg_duration_months }} Bulan</td>
                    <td class="text-center" style="color: #15803d; font-weight: bold;">{{ $item->on_time_count }}</td>
                    <td class="text-center" style="color: #b91c1c; font-weight: bold;">{{ $item->late_count }}</td>
                    <td class="text-center">
                        <span class="badge badge-success">{{ $item->on_time_percent }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada data gelombang dan masa studi ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table class="sign-table">
            <tr>
                <td width="70%"></td>
                <td width="30%">
                    Subang, {{ now()->translatedFormat('d F Y') }}<br>
                    <strong>Ketua Program Studi,</strong>
                    <br><br><br><br>
                    <u>{{ $kaprodi->name ?? 'Fahmy Firdauzi, S.Kom., M.Kom.' }}</u><br>
                    NIP/NIDN. {{ $kaprodi->identifier ?? '-' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

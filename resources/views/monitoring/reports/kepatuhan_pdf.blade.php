<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kepatuhan Bimbingan & Early Warning Mahasiswa Pasif</title>
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
        table.data-table th, table.data-table td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        table.data-table th { background-color: #0f172a; color: #ffffff; font-weight: bold; font-size: 8pt; text-align: center; }
        table.data-table tr:nth-child(even) { background-color: #f8fafc; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .badge { display: inline-block; padding: 2px 6px; font-size: 7pt; font-weight: bold; border-radius: 3px; text-transform: uppercase; }
        .badge-success { background-color: #dcfce7; color: #15803d; }
        .badge-danger { background-color: #fee2e2; color: #b91c1c; }
        .badge-warning { background-color: #fef3c7; color: #b45309; }
        .footer { margin-top: 25px; width: 100%; }
        .sign-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .sign-table td { border: none; text-align: center; font-size: 8.5pt; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN KEPATUHAN BIMBINGAN & EARLY WARNING MAHASISWA PASIF</h2>
        <h3>SISTEM INFORMASI BIMBINGAN MAHASISWA (SIBIMA) - FAKULTAS ILMU KOMPUTER</h3>
        <p>Universitas Subang • Tanggal Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td width="15%"><strong>Filter Angkatan</strong></td>
                <td width="35%">: {{ $entryYear ? 'Angkatan ' . $entryYear : 'Semua Angkatan' }}</td>
                <td width="15%"><strong>Dosen Pembimbing</strong></td>
                <td width="35%">: {{ $pembimbingName ?? 'Semua Pembimbing' }}</td>
            </tr>
            <tr>
                <td><strong>Kriteria Audit</strong></td>
                <td>: Keaktifan Bimbingan Mingguan & Target Kuota Sesi</td>
                <td><strong>Total Mahasiswa</strong></td>
                <td>: {{ count($data) }} Mahasiswa Aktif</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="25">NO</th>
                <th width="75">NPM</th>
                <th>NAMA MAHASISWA</th>
                <th width="50">ANGK.</th>
                <th>PEMBIMBING 1</th>
                <th>PEMBIMBING 2</th>
                <th width="45">SESI P1</th>
                <th width="45">SESI P2</th>
                <th width="50">TOTAL</th>
                <th width="85">HARI PASIF</th>
                <th width="90">STATUS RISIKO</th>
                <th width="90">NO. WHATSAPP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                @php
                    $days = $item->days_since_last_mentoring;
                    $p1 = $item->sessions_p1 ?? 0;
                    $p2 = $item->sessions_p2 ?? 0;
                    $tot = $item->total_sessions ?? ($p1 + $p2);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-bold">{{ $item->student->identifier ?? '-' }}</td>
                    <td class="font-bold">{{ $item->student->name ?? '-' }}</td>
                    <td class="text-center">{{ $item->student->entry_year ?? '-' }}</td>
                    <td>{{ $item->pembimbing1->name ?? 'Belum Ditugaskan' }}</td>
                    <td>{{ $item->pembimbing2->name ?? 'Belum Ditugaskan' }}</td>
                    <td class="text-center">{{ $p1 }}</td>
                    <td class="text-center">{{ $p2 }}</td>
                    <td class="text-center font-bold">{{ $tot }}</td>
                    <td class="text-center">
                        {{ $days !== null ? $days . ' Hari Lalu' : 'Belum Pernah' }}
                    </td>
                    <td class="text-center">
                        @if($days === null || $days > 14)
                            <span class="badge badge-danger">KRITIS (>14 HARI)</span>
                        @elseif($days > 7)
                            <span class="badge badge-warning">PERINGATAN</span>
                        @else
                            <span class="badge badge-success">AKTIF</span>
                        @endif
                    </td>
                    <td class="text-center" style="font-size: 7.5pt;">{{ $item->student->phone_number ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada data kepatuhan mahasiswa yang sesuai filter.
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

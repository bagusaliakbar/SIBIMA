<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Beban Kinerja Dosen Pembimbing & Penguji</title>
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
        table.data-table th, table.data-table td { border: 1px solid #cbd5e1; padding: 7px 10px; text-align: left; }
        table.data-table th { background-color: #0f172a; color: #ffffff; font-weight: bold; font-size: 8pt; text-align: center; }
        table.data-table tr:nth-child(even) { background-color: #f8fafc; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .footer { margin-top: 25px; width: 100%; }
        .sign-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .sign-table td { border: none; text-align: center; font-size: 8.5pt; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN REKAPITULASI BEBAN KINERJA DOSEN PEMBIMBING & PENGUJI</h2>
        <h3>SISTEM INFORMASI BIMBINGAN MAHASISWA (SIBIMA) - FAKULTAS ILMU KOMPUTER</h3>
        <p>Universitas Subang • Tanggal Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td width="20%"><strong>Periode Pelaporan</strong></td>
                <td width="30%">: {{ $startDate ?? 'Awal' }} s/d {{ $endDate ?? 'Sekarang' }}</td>
                <td width="20%"><strong>Total Dosen</strong></td>
                <td width="30%">: {{ count($data) }} Dosen Pengajar</td>
            </tr>
            <tr>
                <td><strong>Kegunaan Laporan</strong></td>
                <td>: Penetapan SK Beban & Honorarium Ujian</td>
                <td><strong>Dicetak Oleh</strong></td>
                <td>: {{ auth()->user()->name ?? 'Administrator' }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="30">NO</th>
                <th>NAMA DOSEN</th>
                <th width="120">NIP / NIDN</th>
                <th width="80">PEMBIMBING 1</th>
                <th width="80">PEMBIMBING 2</th>
                <th width="85">PENGUJI SEMINAR</th>
                <th width="85">PENGUJI SIDANG</th>
                <th width="90">TOTAL BEBAN</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $sumP1 = 0; $sumP2 = 0; $sumSem = 0; $sumDef = 0; $sumTot = 0;
            @endphp
            @forelse($data as $index => $item)
                @php
                    $p1 = $item->theses_as_p1_count ?? 0;
                    $p2 = $item->theses_as_p2_count ?? 0;
                    $sem = $item->seminar_examiner_count ?? 0;
                    $def = $item->defense_examiner_count ?? 0;
                    $tot = $item->total_workload ?? ($p1 + $p2 + $sem + $def);
                    $sumP1 += $p1; $sumP2 += $p2; $sumSem += $sem; $sumDef += $def; $sumTot += $tot;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $item->name }}</td>
                    <td class="text-center">{{ $item->identifier ?? '-' }}</td>
                    <td class="text-center">{{ $p1 }}</td>
                    <td class="text-center">{{ $p2 }}</td>
                    <td class="text-center">{{ $sem }}</td>
                    <td class="text-center">{{ $def }}</td>
                    <td class="text-center font-bold" style="background-color: #f1f5f9; font-size: 9pt;">{{ $tot }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada data kinerja dosen ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($data) > 0)
        <tfoot>
            <tr style="background-color: #0f172a; color: #ffffff; font-weight: bold;">
                <td colspan="3" class="text-center font-bold">TOTAL KESELURUHAN</td>
                <td class="text-center">{{ $sumP1 }}</td>
                <td class="text-center">{{ $sumP2 }}</td>
                <td class="text-center">{{ $sumSem }}</td>
                <td class="text-center">{{ $sumDef }}</td>
                <td class="text-center font-bold" style="font-size: 9.5pt;">{{ $sumTot }}</td>
            </tr>
        </tfoot>
        @endif
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

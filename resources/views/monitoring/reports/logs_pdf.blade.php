<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Audit Log Aktivitas Sistem</title>
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
        .footer { margin-top: 25px; width: 100%; }
        .sign-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .sign-table td { border: none; text-align: center; font-size: 8.5pt; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN AUDIT LOG & JEJAK AKTIVITAS SISTEM</h2>
        <h3>SISTEM INFORMASI BIMBINGAN MAHASISWA (SIBIMA) - FAKULTAS ILMU KOMPUTER</h3>
        <p>Universitas Subang • Tanggal Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td width="20%"><strong>Periode Audit</strong></td>
                <td width="30%">: {{ $startDate ?? 'Awal' }} s/d {{ $endDate ?? 'Sekarang' }}</td>
                <td width="20%"><strong>Total Log Tercatat</strong></td>
                <td width="30%">: {{ count($data) }} Entri Aktivitas</td>
            </tr>
            <tr>
                <td><strong>Tujuan</strong></td>
                <td>: Audit Trail Keamanan & Integritas Data</td>
                <td><strong>Dicetak Oleh</strong></td>
                <td>: {{ auth()->user()->name ?? 'Administrator' }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="30">NO</th>
                <th width="110">WAKTU AKTIVITAS</th>
                <th width="130">PENGGUNA / ROLE</th>
                <th width="110">TIPE AKTIVITAS</th>
                <th>DESKRIPSI PERUBAHAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->created_at ? $item->created_at->format('d/m/Y H:i:s') : '-' }}</td>
                    <td>
                        <span class="font-bold">{{ $item->user->name ?? 'System' }}</span>
                        @if($item->user && $item->user->role)
                            <span style="font-size: 7.5pt; color: #64748b;">({{ ucfirst($item->user->role) }})</span>
                        @endif
                    </td>
                    <td class="text-center font-bold" style="color: #4338ca;">{{ strtoupper($item->activity ?? '-') }}</td>
                    <td>{{ $item->description ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada log aktivitas dalam rentang waktu yang dipilih.
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

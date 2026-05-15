<!DOCTYPE html>
<html>
<head>
    <title>Laporan Audit Log</title>
    <style>
        body { font-family: sans-serif; font-size: 8pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin-bottom: 5px;">LAPORAN AUDIT LOG SISTEM</h2>
        <h3 style="margin-top: 0;">SISTEM INFORMASI BIMBINGAN MAHASISWA (SIBIMA)</h3>
        <p>Periode: {{ $startDate ?? 'Awal' }} s/d {{ $endDate ?? 'Sekarang' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="20">NO</th>
                <th width="80">WAKTU</th>
                <th width="100">PENGGUNA</th>
                <th width="60">AKSI</th>
                <th>DESKRIPSI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $item->user->name ?? 'System' }}</td>
                    <td>{{ strtoupper($item->activity) }}</td>
                    <td>{{ $item->description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

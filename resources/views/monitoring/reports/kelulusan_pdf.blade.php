<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kelulusan</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin-bottom: 5px;">REKAPITULASI KELULUSAN MAHASISWA</h2>
        <h3 style="margin-top: 0;">SISTEM INFORMASI BIMBINGAN MAHASISWA (SIBIMA)</h3>
        <p>Periode: {{ $startDate ?? 'Awal' }} s/d {{ $endDate ?? 'Sekarang' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">NO</th>
                <th>NAMA MAHASISWA</th>
                <th>NPM</th>
                <th>JUDUL AKHIR</th>
                <th>TANGGAL LULUS</th>
                <th>NILAI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->student->name }}</td>
                    <td>{{ $item->student->identifier }}</td>
                    <td>{{ $item->final_title ?? $item->title }}</td>
                    <td>{{ $item->updated_at->format('d/m/Y') }}</td>
                    <td>A</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

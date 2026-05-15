<!DOCTYPE html>
<html>
<head>
    <title>Laporan Akademik</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 8pt; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin-bottom: 5px;">LAPORAN AKADEMIK GLOBAL</h2>
        <h3 style="margin-top: 0;">SISTEM INFORMASI BIMBINGAN MAHASISWA (SIBIMA)</h3>
        <p>Periode: {{ $startDate ?? 'Awal' }} s/d {{ $endDate ?? 'Sekarang' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">NO</th>
                <th>NAMA MAHASISWA</th>
                <th>NPM</th>
                <th>JUDUL SKRIPSI</th>
                <th>PEMBIMBING 1</th>
                <th>PEMBIMBING 2</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->student->name }}</td>
                    <td>{{ $item->student->identifier }}</td>
                    <td>{{ $item->final_title ?? $item->title }}</td>
                    <td>{{ $item->pembimbing1->name ?? '-' }}</td>
                    <td>{{ $item->pembimbing2->name ?? '-' }}</td>
                    <td>{{ strtoupper($item->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>

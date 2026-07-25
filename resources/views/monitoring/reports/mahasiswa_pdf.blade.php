<!DOCTYPE html>
<html>
<head>
    <title>Laporan Progres Mahasiswa</title>
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
        <h2 style="margin-bottom: 5px;">LAPORAN PROGRES SKRIPSI MAHASISWA</h2>
        <h3 style="margin-top: 0;">SISTEM INFORMASI BIMBINGAN MAHASISWA (SIBIMA)</h3>
        <p>Periode: {{ $startDate ?? 'Awal' }} s/d {{ $endDate ?? 'Sekarang' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">NO</th>
                <th>NAMA MAHASISWA</th>
                <th>NPM</th>
                <th>TOTAL BIMBINGAN</th>
                <th>LOGBOOK</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->student->name }}</td>
                    <td>{{ $item->student->identifier }}</td>
                    <td>{{ \App\Models\MentoringSession::where('thesis_id', $item->id)->where('status', 'completed')->where('is_absent', false)->count() }}</td>
                    <td>{{ \App\Models\Logbook::where('thesis_id', $item->id)->count() }}</td>
                    <td>{{ strtoupper($item->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kinerja Dosen</title>
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
        <h2 style="margin-bottom: 5px;">LAPORAN KINERJA & BEBAN BIMBINGAN DOSEN</h2>
        <h3 style="margin-top: 0;">SISTEM INFORMASI BIMBINGAN MAHASISWA (SIBIMA)</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">NO</th>
                <th>NAMA DOSEN</th>
                <th>NIP/NIDN</th>
                <th>BIMBINGAN P1</th>
                <th>BIMBINGAN P2</th>
                <th>TOTAL BEBAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->identifier }}</td>
                    <td>{{ $item->theses_as_p1_count }}</td>
                    <td>{{ $item->theses_as_p2_count }}</td>
                    <td>{{ $item->theses_as_p1_count + $item->theses_as_p2_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

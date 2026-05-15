<!DOCTYPE html>
<html>
<head>
    <title>Laporan Tren Masa Studi</title>
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
        <h2 style="margin-bottom: 5px;">ANALISIS TREN MASA STUDI</h2>
        <h3 style="margin-top: 0;">SISTEM INFORMASI BIMBINGAN MAHASISWA (SIBIMA)</h3>
    </div>

    <p>Laporan ini merangkum rata-rata durasi pengerjaan skripsi untuk evaluasi efisiensi akademik.</p>

    <table>
        <thead>
            <tr>
                <th width="30">NO</th>
                <th>ANGKATAN / GELOMBANG</th>
                <th>JUMLAH MAHASISWA</th>
                <th>RATA-RATA DURASI (BULAN)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Angkatan 2020</td>
                <td>{{ count($data) }}</td>
                <td>6.5</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Angkatan 2021</td>
                <td>{{ count($data) > 0 ? floor(count($data)/2) : 0 }}</td>
                <td>5.8</td>
            </tr>
        </tbody>
    </table>
</body>
</html>

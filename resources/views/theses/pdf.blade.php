<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Skripsi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
        }
        td {
            font-size: 9pt;
        }
        .status {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 8pt;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10pt;
        }
        .date {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Data Skripsi Mahasiswa</h2>
        <p>Sistem Informasi Bimbingan Mahasiswa (SIBIMA)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Mahasiswa</th>
                <th width="25%">Judul Skripsi</th>
                <th width="20%">Deskripsi</th>
                <th width="10%">Status</th>
                <th width="25%">Pembimbing</th>
            </tr>
        </thead>
        <tbody>
            @foreach($theses as $index => $thesis)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $thesis->student->name }}</strong><br>
                        {{ $thesis->student->identifier }}
                    </td>
                    <td>
                        @if($thesis->final_title)
                            <strong>{{ $thesis->final_title }}</strong><br>
                            <small>Awal: {{ $thesis->title }}</small>
                        @else
                            {{ $thesis->title }}
                        @endif
                    </td>
                    <td>
                        <div style="font-size: 8pt; line-height: 1.2;">
                            {{ $thesis->abstract ?? '-' }}
                        </div>
                    </td>
                    <td class="status">
                        @if($thesis->status === 'pending') Menunggu
                        @elseif($thesis->status === 'active') Aktif
                        @elseif($thesis->status === 'completed') Selesai
                        @else {{ $thesis->status }}
                        @endif
                    </td>
                    <td>
                        1. {{ $thesis->pembimbing1->name ?? '-' }}<br>
                        2. {{ $thesis->pembimbing2->name ?? '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="date">Dicetak pada: {{ now()->format('d F Y H:i') }}</div>
        <br><br>
        <div class="signature">
            (.........................................)<br>
            Administrator SIBIMA
        </div>
    </div>
</body>
</html>

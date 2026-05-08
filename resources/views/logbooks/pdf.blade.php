<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Logbook Bimbingan Skripsi</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }
        .student-info {
            margin-bottom: 25px;
        }
        .student-info table {
            width: 100%;
        }
        .student-info td {
            padding: 3px 0;
            vertical-align: top;
        }
        .label {
            width: 150px;
            font-weight: bold;
        }
        .separator {
            width: 10px;
        }
        .log-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .log-table th, .log-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        .log-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .text-center {
            text-align: center;
        }
        .footer-sign {
            margin-top: 50px;
            width: 100%;
        }
        .footer-sign table {
            width: 100%;
        }
        .footer-sign td {
            width: 50%;
            text-align: center;
        }
        .sign-space {
            height: 80px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Sistem Informasi Bimbingan Mahasiswa (SIBIMA)</h2>
        <p>Laporan Logbook Bimbingan Skripsi</p>
    </div>

    <div class="student-info">
        <table>
            <tr>
                <td class="label">Nama Mahasiswa</td>
                <td class="separator">:</td>
                <td>{{ $thesis->student->name }}</td>
            </tr>
            <tr>
                <td class="label">NPM</td>
                <td class="separator">:</td>
                <td>{{ $thesis->student->identifier }}</td>
            </tr>
            <tr>
                <td class="label">Judul Skripsi</td>
                <td class="separator">:</td>
                <td>{{ $thesis->final_title ?? $thesis->title }}</td>
            </tr>
            <tr>
                <td class="label">Dosen Pembimbing 1</td>
                <td class="separator">:</td>
                <td>{{ $thesis->pembimbing1->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Dosen Pembimbing 2</td>
                <td class="separator">:</td>
                <td>{{ $thesis->pembimbing2->name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <table class="log-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 100px;">Tanggal</th>
                <th>Topik & Hasil Bimbingan</th>
                <th style="width: 120px;">Paraf Dosen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $index => $session)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $session->scheduled_at->format('d/m/Y') }}</td>
                    <td>
                        <strong>Topik:</strong> {{ $session->topic }}<br>
                        <div style="margin-top: 5px;">
                            <strong>Hasil:</strong> {{ $session->feedback ?: '-' }}
                        </div>
                        @if($session->notes)
                            <div style="margin-top: 5px; color: #777; font-size: 10px; font-style: italic;">
                                <strong>Ket. Pengajuan:</strong> {{ $session->notes }}
                            </div>
                        @endif
                        <div style="margin-top: 5px; font-size: 10px; color: #555;">
                            <strong>Dosen:</strong> {{ $session->dosen->name ?? '-' }}
                        </div>
                    </td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada catatan bimbingan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-sign">
        <table>
            <tr>
                <td>
                    Mengetahui,<br>
                    Dosen Pembimbing 1
                    <div class="sign-space"></div>
                    ( {{ $thesis->pembimbing1->name ?? '........................................' }} )<br>
                    NIDN: {{ $thesis->pembimbing1->identifier ?? '....................' }}
                </td>
                <td>
                    Dicetak pada: {{ now()->format('d M Y') }}<br>
                    Mahasiswa
                    <div class="sign-space"></div>
                    ( {{ $thesis->student->name }} )<br>
                    NPM: {{ $thesis->student->identifier }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

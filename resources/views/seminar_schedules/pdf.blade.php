<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Jadwal Seminar Skripsi</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            padding: 0;
            letter-spacing: 2px;
        }
        .header h2 {
            font-size: 13px;
            margin: 5px 0 0 0;
            padding: 0;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 15px;
        }
        .meta-info td {
            vertical-align: top;
        }
        .meta-info .label {
            width: 100px;
            font-weight: bold;
        }
        .meta-info .location-box {
            text-align: right;
            font-weight: bold;
        }
        table.schedule {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.schedule th {
            background-color: #f8f9fa;
            border: 1px solid #333;
            padding: 8px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        table.schedule td {
            border: 1px solid #333;
            padding: 8px;
            vertical-align: top;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .activity-row {
            background-color: #fcfcfc;
            font-style: italic;
        }
        .student-name {
            font-weight: bold;
            font-size: 12px;
            display: block;
            margin-bottom: 3px;
        }
        .thesis-title {
            font-size: 10px;
            font-style: italic;
            color: #555;
        }
        .list-item {
            margin-bottom: 4px;
        }
        .no-wrap { white-space: nowrap; }
    </style>
</head>
<body>
    <div class="header">
        <h1>JADWAL SEMINAR SKRIPSI</h1>
        <h2>{{ strtoupper($seminarSchedule->title) }}</h2>
    </div>

    <table class="meta-info">
        <tr>
            <td class="label">Hari / Tanggal</td>
            <td>: {{ \Carbon\Carbon::parse($seminarSchedule->date)->translatedFormat('l, d F Y') }}</td>
            <td class="location-box">{{ $seminarSchedule->location ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Ketua Sidang</td>
            <td>: {{ $seminarSchedule->chairman->name }}</td>
            <td></td>
        </tr>
        <tr>
            <td class="label">Moderator</td>
            <td>: {{ $seminarSchedule->moderator->name }}</td>
            <td></td>
        </tr>
        @if($seminarSchedule->meeting_link)
        <tr>
            <td class="label">Link</td>
            <td colspan="2">: {{ $seminarSchedule->meeting_link }}</td>
        </tr>
        @endif
    </table>

    <table class="schedule">
        <thead>
            <tr>
                <th style="width: 30px;">NO</th>
                <th style="width: 70px;">WAKTU</th>
                <th style="width: 80px;">NPM</th>
                <th style="width: 120px;">NAMA</th>
                <th>JUDUL SKRIPSI</th>
                <th style="width: 130px;">PEMBIMBING</th>
                <th style="width: 130px;">PENGUJI</th>
            </tr>
        </thead>
        <tbody>
            @php $studentNo = 1; @endphp
            @foreach($seminarSchedule->details as $detail)
                @if($detail->thesis_id)
                    <tr>
                        <td class="text-center font-bold">{{ $studentNo++ }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($detail->start_time)->format('H.i') }}-{{ \Carbon\Carbon::parse($detail->end_time)->format('H.i') }}</td>
                        <td class="text-center">{{ $detail->thesis->student->identifier }}</td>
                        <td class="font-bold">{{ strtoupper($detail->thesis->student->name) }}</td>
                        <td class="thesis-title">{{ $detail->thesis->title }}</td>
                        <td>
                            <div class="list-item">1. {{ $detail->thesis->pembimbing1->name }}</div>
                            @if($detail->thesis->pembimbing2)
                                <div class="list-item">2. {{ $detail->thesis->pembimbing2->name }}</div>
                            @endif
                        </td>
                        <td>
                            @if($detail->examiner1)
                                <div class="list-item">1. {{ $detail->examiner1->name }}</div>
                            @endif
                            @if($detail->examiner2)
                                <div class="list-item">2. {{ $detail->examiner2->name }}</div>
                            @endif
                        </td>
                    </tr>
                @else
                    <tr class="activity-row">
                        <td class="text-center font-bold">#</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($detail->start_time)->format('H.i') }}-{{ \Carbon\Carbon::parse($detail->end_time)->format('H.i') }}</td>
                        <td colspan="5" class="font-bold" style="text-transform: uppercase;">{{ $detail->activity_name }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Rekapitulasi Nilai Sidang</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 8px; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 5px 0 0; color: #666; }
        .grade-a { color: #059669; }
        .grade-b { color: #2563eb; }
        .grade-c { color: #d97706; }
        .footer { margin-top: 30px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Rekapitulasi Nilai Sidang Skripsi</h2>
        <p>Program Studi Teknik Informatika - Universitas Subang</p>
        @if($wave)
            <p>Gelombang: {{ $wave->name }}</p>
        @endif
        <p>Tanggal Cetak: {{ now()->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="text-center">NO</th>
                <th rowspan="2" class="text-center">NPM</th>
                <th rowspan="2">NAMA MAHASISWA</th>
                <th rowspan="2">TIM PENELAAH</th>
                <th colspan="3" class="text-center">KOMPONEN NILAI</th>
                <th rowspan="2" class="text-center">TOTAL</th>
                <th rowspan="2" class="text-center">AKHIR</th>
                <th rowspan="2" class="text-center">HURUF</th>
            </tr>
            <tr>
                <th class="text-center">PRES (25%)</th>
                <th class="text-center">NASKAH (40%)</th>
                <th class="text-center">TULIS (35%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($defenseDetails as $index => $detail)
                @php
                    $revP1 = $detail->revisions->where('examiner_id', $detail->thesis->pembimbing1_id)->first();
                    $revE1 = $detail->revisions->where('examiner_id', $detail->examiner1_id)->first();
                    $revE2 = $detail->revisions->where('examiner_id', $detail->examiner2_id)->first();

                    $calc = function($rev) {
                        if (!$rev || $rev->score_presentation === null) return null;
                        return ($rev->score_presentation * 0.25) + ($rev->score_explanation * 0.40) + ($rev->score_writing * 0.35);
                    };

                    $scoreP1 = $calc($revP1);
                    $scoreE1 = $calc($revE1);
                    $scoreE2 = $calc($revE2);

                    $scores = collect([$scoreP1, $scoreE1, $scoreE2])->filter(fn($s) => $s !== null);
                    $totalScore = $scores->sum();
                    $finalScore = $scores->count() > 0 ? $totalScore / $scores->count() : 0;

                    $getGrade = function($s) {
                        if ($s >= 80) return 'A';
                        if ($s >= 75) return 'B+';
                        if ($s >= 70) return 'B';
                        if ($s >= 65) return 'C+';
                        if ($s >= 60) return 'C';
                        if ($s >= 50) return 'D';
                        return 'E';
                    };
                    $finalGrade = $scores->count() > 0 ? $getGrade($finalScore) : '-';
                @endphp
                <tr>
                    <td rowspan="3" class="text-center">{{ $index + 1 }}</td>
                    <td rowspan="3" class="text-center">{{ $detail->thesis->student->identifier }}</td>
                    <td rowspan="3">
                        <div class="font-bold">{{ $detail->thesis->student->name }}</div>
                        <div style="font-style: italic; color: #666; margin-top: 4px;">"{{ $detail->thesis->title }}"</div>
                    </td>
                    <td>P1: {{ $detail->thesis->pembimbing1->name }}</td>
                    <td class="text-center">{{ $revP1->score_presentation ?? '-' }}</td>
                    <td class="text-center">{{ $revP1->score_explanation ?? '-' }}</td>
                    <td class="text-center">{{ $revP1->score_writing ?? '-' }}</td>
                    <td rowspan="3" class="text-center font-bold">{{ number_format($totalScore, 1) }}</td>
                    <td rowspan="3" class="text-center font-bold" style="font-size: 12px;">{{ number_format($finalScore, 1) }}</td>
                    <td rowspan="3" class="text-center font-bold" style="font-size: 14px;">{{ $finalGrade }}</td>
                </tr>
                <tr>
                    <td>U1: {{ $detail->examiner1->name }}</td>
                    <td class="text-center">{{ $revE1->score_presentation ?? '-' }}</td>
                    <td class="text-center">{{ $revE1->score_explanation ?? '-' }}</td>
                    <td class="text-center">{{ $revE1->score_writing ?? '-' }}</td>
                </tr>
                <tr>
                    <td>U2: {{ $detail->examiner2->name }}</td>
                    <td class="text-center">{{ $revE2->score_presentation ?? '-' }}</td>
                    <td class="text-center">{{ $revE2->score_explanation ?? '-' }}</td>
                    <td class="text-center">{{ $revE2->score_writing ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Subang, {{ now()->format('d F Y') }}</p>
        <p style="margin-top: 60px;">( ......................................... )</p>
        <p>Koordinator Skripsi</p>
    </div>
</body>
</html>

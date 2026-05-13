<!DOCTYPE html>
<html>

<head>
    <title>Rekapitulasi Nilai Sidang</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .kop-surat {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .kop-surat table {
            width: 100%;
            border: none;
            margin-top: 0;
        }

        .kop-surat td {
            border: none !important;
            padding: 0 !important;
            vertical-align: middle;
        }

        .kop-surat .logo-cell {
            width: 80px;
            text-align: left;
        }

        .kop-surat .logo-img {
            width: 70px;
            height: auto;
        }

        .kop-surat .info-cell {
            text-align: center;
            padding-right: 80px !important;
        }

        .kop-surat .univ-name {
            font-size: 16px;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
        }

        .kop-surat .faculty-name {
            font-size: 20px;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
        }

        .kop-surat .accreditation {
            font-size: 10px;
            font-weight: bold;
            margin: 2px 0;
        }

        .kop-surat .address {
            font-size: 9px;
            margin: 1px 0;
        }

        .kop-surat .email {
            font-size: 9px;
            margin: 1px 0;
        }

        .report-title {
            text-align: center;
            margin-bottom: 20px;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 14px;
        }

        .grade-a {
            color: #059669;
        }

        .grade-b {
            color: #2563eb;
        }

        .grade-c {
            color: #d97706;
        }

        .footer {
            margin-top: 40px;
        }

        .footer-table {
            width: 100%;
            border: none;
        }

        .footer-table td {
            border: none !important;
            padding: 0 !important;
        }

        .footer-table .sign-cell {
            width: 250px;
            text-align: center;
        }

        .sign-space {
            height: 80px;
        }
    </style>
</head>

<body>
    <div class="kop-surat">
        <table>
            <tr>
                <td class="logo-cell">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo_unsub.png'))) }}"
                        class="logo-img">
                </td>
                <td class="info-cell">
                    <div class="univ-name">UNIVERSITAS SUBANG</div>
                    <div class="faculty-name">FAKULTAS ILMU KOMPUTER</div>
                    <div class="accreditation">Akreditasi BAIK SEKALI No. 110/SK/LAM-INFOKOM/Ak/S/VIII/2025</div>
                    <div class="address">Jalan R.A Kartini KM 3 Telp (0260) 411415 Subang</div>
                    <div class="email">E-Mail : <span
                            style="color: blue; text-decoration: underline;">fasilkom@unsub.ac.id</span></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="report-title">
        Rekapitulasi Nilai Sidang Skripsi
        @if($wave)
            <br><span style="font-size: 14px; font-weight: bold;">{{ $wave->name }}</span>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="text-center">NO</th>
                <th rowspan="2" class="text-center">NPM</th>
                <th rowspan="2">NAMA MAHASISWA</th>
                <th rowspan="2">TIM PENELAAH</th>
                <th colspan="3" class="text-center">KOMPONEN NILAI</th>
                <th rowspan="2" class="text-center">JML NILAI</th>
                <th rowspan="2" class="text-center">TOTAL</th>
                <th rowspan="2" class="text-center">NILAI AKHIR</th>
                <th rowspan="2" class="text-center">NILAI HURUF</th>
            </tr>
            <tr>
                <th class="text-center">PERSENTASI (25%)</th>
                <th class="text-center">PENJELASAN NASKAH (40%)</th>
                <th class="text-center">PENULISAN NASKAH (35%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($defenseDetails as $index => $detail)
                @php
                    $revP1 = $detail->revisions->where('examiner_id', $detail->thesis->pembimbing1_id)->first();
                    $revE1 = $detail->revisions->where('examiner_id', $detail->examiner1_id)->first();
                    $revE2 = $detail->revisions->where('examiner_id', $detail->examiner2_id)->first();

                    $calc = function ($rev) {
                        if (!$rev || $rev->score_presentation === null)
                            return null;
                        return ($rev->score_presentation * 0.25) + ($rev->score_explanation * 0.40) + ($rev->score_writing * 0.35);
                    };

                    $scoreP1 = $calc($revP1);
                    $scoreE1 = $calc($revE1);
                    $scoreE2 = $calc($revE2);

                    $scores = collect([$scoreP1, $scoreE1, $scoreE2])->filter(fn($s) => $s !== null);
                    $totalScore = $scores->sum();
                    $finalScore = $scores->count() > 0 ? $totalScore / $scores->count() : 0;

                    $getGrade = function ($s) {
                        if ($s >= 80)
                            return 'A';
                        if ($s >= 70)
                            return 'B';
                        if ($s >= 60)
                            return 'C';
                        if ($s >= 50)
                            return 'D';
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
                    <td>{{ $detail->thesis->pembimbing1->name }}</td>
                    <td class="text-center">{{ $revP1->score_presentation ?? '-' }}</td>
                    <td class="text-center">{{ $revP1->score_explanation ?? '-' }}</td>
                    <td class="text-center">{{ $revP1->score_writing ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $scoreP1 ? number_format($scoreP1, 1) : '-' }}</td>
                    <td rowspan="3" class="text-center font-bold">{{ number_format($totalScore, 1) }}</td>
                    <td rowspan="3" class="text-center font-bold" style="font-size: 12px;">
                        {{ number_format($finalScore, 1) }}</td>
                    <td rowspan="3" class="text-center font-bold" style="font-size: 14px;">{{ $finalGrade }}</td>
                </tr>
                <tr>
                    <td>{{ $detail->examiner1->name }}</td>
                    <td class="text-center">{{ $revE1->score_presentation ?? '-' }}</td>
                    <td class="text-center">{{ $revE1->score_explanation ?? '-' }}</td>
                    <td class="text-center">{{ $revE1->score_writing ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $scoreE1 ? number_format($scoreE1, 1) : '-' }}</td>
                </tr>
                <tr>
                    <td>{{ $detail->examiner2->name }}</td>
                    <td class="text-center">{{ $revE2->score_presentation ?? '-' }}</td>
                    <td class="text-center">{{ $revE2->score_explanation ?? '-' }}</td>
                    <td class="text-center">{{ $revE2->score_writing ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $scoreE2 ? number_format($scoreE2, 1) : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td></td>
                <td class="sign-cell">
                    <p>Subang, {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
                    <p>BAAK,</p>
                    <div class="sign-space"></div>
                    <p><strong>(Dina Yuli Nurida, S.AN., M.AP)</strong></p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
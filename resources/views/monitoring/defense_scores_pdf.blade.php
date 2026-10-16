<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekapitulasi Nilai Sidang Skripsi</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 10mm 8mm 12mm 8mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #1e293b;
            line-height: 1.3;
        }

        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            table-layout: fixed;
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 4px;
            text-align: left;
        }

        table.main-table th {
            background-color: #f1f5f9;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 7.2pt;
            letter-spacing: 0.3px;
            color: #0f172a;
            vertical-align: middle;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        tr.student-row {
            page-break-inside: avoid !important;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        /* Column widths for main table (Total: 100%) */
        .col-no { width: 3.5%; }
        .col-npm { width: 9%; }
        .col-student { width: 25.5%; }
        .col-penelaah { width: 17%; }
        .col-persentasi { width: 7%; }
        .col-penjelasan { width: 7.5%; }
        .col-penulisan { width: 7.5%; }
        .col-jml { width: 7%; }
        .col-total { width: 5.5%; }
        .col-akhir { width: 5.5%; }
        .col-huruf { width: 5%; }

        /* Nested table inside td.nested-container (takes columns 4 to 8: 17 + 7 + 7.5 + 7.5 + 7 = 46%) */
        td.nested-container {
            padding: 0 !important;
            margin: 0 !important;
            vertical-align: top;
            border: 1px solid #cbd5e1;
        }

        table.nested-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0 !important;
            padding: 0 !important;
            border: none;
        }

        table.nested-table td {
            padding: 5px 4px;
            font-size: 8pt;
            border-top: none;
            border-left: none;
            border-right: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        table.nested-table tr td:last-child {
            border-right: none;
        }

        table.nested-table tr.last-tr td {
            border-bottom: none;
        }

        /* Proportions for inner 5 columns (Total: 100% of 46%) */
        .inner-penelaah { width: 36.95%; }
        .inner-persentasi { width: 15.22%; }
        .inner-penjelasan { width: 16.30%; }
        .inner-penulisan { width: 16.30%; }
        .inner-jml { width: 15.23%; }

        .kop-surat {
            width: 100%;
            margin-bottom: 12px;
            text-align: center;
        }

        .kop-img {
            width: 100%;
            height: auto;
            display: block;
        }

        .kop-table-wrapper {
            border-bottom: 3px double #000;
            padding-bottom: 5px;
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
            font-size: 15px;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
        }

        .kop-surat .faculty-name {
            font-size: 19px;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
        }

        .kop-surat .accreditation {
            font-size: 9.5px;
            font-weight: bold;
            margin: 2px 0;
        }

        .kop-surat .address {
            font-size: 8.5px;
            margin: 1px 0;
        }

        .kop-surat .email {
            font-size: 8.5px;
            margin: 1px 0;
        }

        .report-title {
            text-align: center;
            margin-top: 8px;
            margin-bottom: 14px;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 12pt;
            letter-spacing: 0.5px;
        }

        .grade-a { color: #047857; font-weight: 800; font-size: 9.5pt; }
        .grade-b { color: #1d4ed8; font-weight: 800; font-size: 9.5pt; }
        .grade-c { color: #b45309; font-weight: 800; font-size: 9.5pt; }
        .grade-d { color: #b91c1c; font-weight: 800; font-size: 9.5pt; }
        .grade-e { color: #6b7280; font-weight: 800; font-size: 9.5pt; }

        .footer {
            margin-top: 25px;
            page-break-inside: avoid;
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
            height: 60px;
        }
    </style>
</head>

<body>
    @php
        if (empty($kopBase64)) {
            $kopCandidates = [
                public_path('images/kop_fasilkom.png'),
                public_path('kop_fasilkom.png'),
                base_path('public/images/kop_fasilkom.png'),
                base_path('public/kop_fasilkom.png'),
                resource_path('images/kop_fasilkom.png'),
            ];
            foreach ($kopCandidates as $cand) {
                if (file_exists($cand) && is_readable($cand)) {
                    $kopBase64 = base64_encode(file_get_contents($cand));
                    break;
                }
            }
        }
    @endphp

    <div class="kop-surat">
        @if(!empty($kopBase64))
            <img src="data:image/png;base64,{{ $kopBase64 }}" class="kop-img" alt="Kop Surat FASILKOM UNSUB">
        @elseif(file_exists(public_path('kop_fasilkom.png')))
            <img src="{{ public_path('kop_fasilkom.png') }}" class="kop-img" alt="Kop Surat FASILKOM UNSUB">
        @elseif(file_exists(public_path('images/kop_fasilkom.png')))
            <img src="{{ public_path('images/kop_fasilkom.png') }}" class="kop-img" alt="Kop Surat FASILKOM UNSUB">
        @else
            <div class="kop-table-wrapper">
                <table>
                    <tr>
                        <td class="logo-cell">
                            @if(file_exists(public_path('logo_unsub.png')))
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo_unsub.png'))) }}"
                                    class="logo-img">
                            @endif
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
        @endif
    </div>

    <div class="report-title">
        Rekapitulasi Nilai Sidang Skripsi
        @if($wave)
            <br><span style="font-size: 10pt; font-weight: bold; color: #334155;">{{ strtoupper($wave->name) }}</span>
        @endif
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" class="text-center col-no">NO</th>
                <th rowspan="2" class="text-center col-npm">NPM</th>
                <th rowspan="2" class="col-student">NAMA MAHASISWA &amp; JUDUL</th>
                <th rowspan="2" class="col-penelaah">TIM PENELAAH</th>
                <th colspan="3" class="text-center">KOMPONEN NILAI</th>
                <th rowspan="2" class="text-center col-jml">JML NILAI</th>
                <th rowspan="2" class="text-center col-total">TOTAL</th>
                <th rowspan="2" class="text-center col-akhir">NILAI AKHIR</th>
                <th rowspan="2" class="text-center col-huruf">NILAI HURUF</th>
            </tr>
            <tr>
                <th class="text-center col-persentasi">PERSENTASI (25%)</th>
                <th class="text-center col-penjelasan">PENJELASAN NASKAH (40%)</th>
                <th class="text-center col-penulisan">PENULISAN NASKAH (35%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($defenseDetails as $index => $detail)
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
                    $finalScore = $scores->count() > 0 ? $totalScore / $scores->count() : null;

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
                    $finalGrade = $finalScore !== null ? $getGrade($finalScore) : '-';
                @endphp
                <tr class="student-row">
                    <td class="text-center col-no font-bold">{{ $index + 1 }}</td>
                    <td class="text-center col-npm font-bold">{{ $detail->thesis->student->identifier }}</td>
                    <td class="col-student">
                        <div style="font-weight: bold; font-size: 8.5pt; text-transform: uppercase; color: #0f172a;">{{ $detail->thesis->student->name }}</div>
                        <div style="font-style: italic; color: #475569; font-size: 7.5pt; margin-top: 3px; line-height: 1.25;">"{{ $detail->thesis->title }}"</div>
                    </td>
                    <td colspan="5" class="nested-container">
                        <table class="nested-table">
                            <tr>
                                <td class="inner-penelaah">{{ $detail->thesis->pembimbing1?->name ?? '-' }}</td>
                                <td class="text-center inner-persentasi">{{ $revP1->score_presentation ?? '-' }}</td>
                                <td class="text-center inner-penjelasan">{{ $revP1->score_explanation ?? '-' }}</td>
                                <td class="text-center inner-penulisan">{{ $revP1->score_writing ?? '-' }}</td>
                                <td class="text-center font-bold inner-jml">{{ $scoreP1 !== null ? number_format($scoreP1, 1) : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="inner-penelaah">{{ $detail->examiner1?->name ?? '-' }}</td>
                                <td class="text-center inner-persentasi">{{ $revE1->score_presentation ?? '-' }}</td>
                                <td class="text-center inner-penjelasan">{{ $revE1->score_explanation ?? '-' }}</td>
                                <td class="text-center inner-penulisan">{{ $revE1->score_writing ?? '-' }}</td>
                                <td class="text-center font-bold inner-jml">{{ $scoreE1 !== null ? number_format($scoreE1, 1) : '-' }}</td>
                            </tr>
                            <tr class="last-tr">
                                <td class="inner-penelaah">{{ $detail->examiner2?->name ?? '-' }}</td>
                                <td class="text-center inner-persentasi">{{ $revE2->score_presentation ?? '-' }}</td>
                                <td class="text-center inner-penjelasan">{{ $revE2->score_explanation ?? '-' }}</td>
                                <td class="text-center inner-penulisan">{{ $revE2->score_writing ?? '-' }}</td>
                                <td class="text-center font-bold inner-jml">{{ $scoreE2 !== null ? number_format($scoreE2, 1) : '-' }}</td>
                            </tr>
                        </table>
                    </td>
                    <td class="text-center font-bold col-total">{{ $scores->count() > 0 ? number_format($totalScore, 1) : '-' }}</td>
                    <td class="text-center font-bold col-akhir" style="font-size: 9.5pt;">
                        {{ $finalScore !== null ? number_format($finalScore, 1) : '-' }}
                    </td>
                    <td class="text-center font-bold col-huruf {{ $finalGrade === 'A' ? 'grade-a' : ($finalGrade === 'B' ? 'grade-b' : ($finalGrade === 'C' ? 'grade-c' : ($finalGrade === 'D' ? 'grade-d' : '')) ) }}">
                        {{ $finalGrade }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 24px; color: #94a3b8; font-style: italic;">
                        Belum ada data nilai sidang skripsi untuk gelombang / filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td></td>
                <td class="sign-cell">
                    <p style="margin: 0; font-size: 8.5pt;">Subang, {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
                    <p style="margin: 3px 0 0 0; font-weight: bold; font-size: 8.5pt;">BAAK,</p>
                    <div class="sign-space"></div>
                    <p style="margin: 0; font-weight: bold; font-size: 8.5pt;">(Dina Yuli Nurida, S.AN., M.AP)</p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
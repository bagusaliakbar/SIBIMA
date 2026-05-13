<!DOCTYPE html>
<html>

<head>
    <title>Berita Acara Sidang Skripsi</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
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
            margin-bottom: 0;
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

        .content {
            padding: 0 40px;
        }

        .title {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }

        .score-table {
            margin-top: 20px;
        }

        .score-table th,
        .score-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .score-table th {
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 50px;
        }

        .signature-table {
            width: 100%;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            padding-top: 40px;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            width: 200px;
            display: inline-block;
        }

        .page-break {
            page-break-after: always;
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

    <div class="content">
        <div class="title">
            BERITA ACARA SIDANG SKRIPSI
        </div>

        <p>Pada hari ini,
            <strong>{{ \Carbon\Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('l') }}</strong>
            tanggal
            <strong>{{ \Carbon\Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('d F Y') }}</strong>,
            telah dilaksanakan Sidang Skripsi bagi mahasiswa:</p>

        <table class="info-table">
            <tr>
                <td style="width: 150px;">Nama Mahasiswa</td>
                <td style="width: 20px;">:</td>
                <td><strong>{{ $detail->thesis->student->name }}</strong></td>
            </tr>
            <tr>
                <td>NPM</td>
                <td>:</td>
                <td>{{ $detail->thesis->student->identifier }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td>Informatika</td>
            </tr>
            <tr>
                <td>Judul Skripsi</td>
                <td>:</td>
                <td>"{{ $detail->thesis->final_title ?: $detail->thesis->title }}"</td>
            </tr>
        </table>

        <p>Dengan hasil penilaian sebagai berikut:</p>

        <table class="score-table">
            <thead>
                <tr>
                    <th style="width: 40px;">NO</th>
                    <th>KOMPONEN PENILAIAN TUGAS AKHIR</th>
                    <th style="width: 80px;">Bobot (%)</th>
                    <th style="width: 100px;">Nilai (0-100)</th>
                    <th style="width: 100px;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td style="text-align: left;">
                        <strong>Presentasi</strong><br>
                        a. Penyajian materi presentasi<br>
                        b. Penggunaan bahasa saat presentasi
                    </td>
                    <td>25</td>
                    <td>{{ number_format($avgPres, 1) }}</td>
                    <td>{{ number_format($avgPres * 0.25, 2) }}</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td style="text-align: left;">
                        <strong>Kemampuan Menjelaskan Naskah Skripsi</strong><br>
                        a. Relevansi teori dengan masalah<br>
                        b. Argumentasi teoritis dalam penyusunan kerangka berpikir<br>
                        c. Kedalaman dan keluasan teori keilmuan yang relevan<br>
                        d. Teknik pengumpulan dan keabsahan instrumen analisis data<br>
                        e. Pembahasan hasil penelitian, penarikan kesimpulan dan pengajuan saran
                    </td>
                    <td>40</td>
                    <td>{{ number_format($avgExpl, 1) }}</td>
                    <td>{{ number_format($avgExpl * 0.40, 2) }}</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td style="text-align: left;">
                        <strong>Penulisan Naskah Skripsi</strong><br>
                        a. Struktur, bahasa, logika dan penulisan<br>
                        b. Orisinalitas
                    </td>
                    <td>35</td>
                    <td>{{ number_format($avgWrit, 1) }}</td>
                    <td>{{ number_format($avgWrit * 0.35, 2) }}</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td colspan="4" style="text-align: left;">Jumlah Skor</td>
                    <td>{{ number_format($avgPres + $avgExpl + $avgWrit, 1) }}</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td colspan="4" style="text-align: left;">Rata-rata Skor</td>
                    <td>{{ number_format(($avgPres + $avgExpl + $avgWrit) / 3, 1) }}</td>
                </tr>
                <tr style="font-weight: bold; background-color: #f2f2f2;">
                    <td colspan="4" style="text-align: left;">Nilai Akhir</td>
                    <td>{{ number_format($finalScore, 1) }}</td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 10px;">
            <table style="width: auto; border: none;">
                <tr>
                    <td style="border: none; padding: 2px 0;">Rata-rata Nilai</td>
                    <td style="border: none; padding: 2px 10px;">:</td>
                    <td style="border: none; padding: 2px 0;">{{ number_format($finalScore, 1) }}</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 2px 0;">Dinyatakan dengan indeks nilai</td>
                    <td style="border: none; padding: 2px 10px;">:</td>
                    <td style="border: none; padding: 2px 0;"><strong>{{ $finalGrade }}</strong></td>
                </tr>
            </table>
        </div>

        <p>Berdasarkan hasil sidang tersebut, mahasiswa dinyatakan:</p>
        <div
            style="border: 1px solid #000; padding: 10px; text-align: center; font-weight: bold; font-size: 18px; margin: 20px 0;">
            @if($finalScore >= 60)
                LULUS
            @else
                TIDAK LULUS / MENGULANG
            @endif
        </div>

        <div class="footer" style="margin-top: 20px;">
            <div style="text-align: right; margin-bottom: 20px;">
                Subang, {{ now()->locale('id')->translatedFormat('d F Y') }}
            </div>

            <div style="text-align: center; margin-bottom: 40px;">
                Mengetahui,
            </div>

            <table class="signature-table">
                <tr>
                    <td>
                        Ketua Program Studi,<br>
                        <div style="margin-top: 60px;"></div>
                        <strong>( Bagus Ali Akbar, S.SI., M.Kom )</strong>
                    </td>
                    <td>
                        Dosen Pembimbing,<br>
                        <div style="margin-top: 20px;">
                            @if($detail->thesis->pembimbing1 && $detail->thesis->pembimbing1->signature)
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $detail->thesis->pembimbing1->signature))) }}"
                                    style="height: 50px; width: auto;">
                            @elseif($detail->thesis->pembimbing1 && $detail->thesis->pembimbing1->signature_token)
                                <img
                                    src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(50)->generate(url('/verify-signature/' . $detail->thesis->pembimbing1->signature_token))) !!} ">
                            @else
                                <div style="height: 50px;"></div>
                            @endif
                        </div>
                        <strong>( {{ $detail->thesis->pembimbing1->name }} )</strong>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
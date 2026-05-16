<!DOCTYPE html>
<html>

<head>
    <title>Berita Acara Seminar Proposal/Hasil</title>
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
            BERITA ACARA SEMINAR PROPOSAL/HASIL
        </div>

        <p>Pada hari ini,
            <strong>{{ \Carbon\Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('l') }}</strong>
            tanggal
            <strong>{{ \Carbon\Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('d F Y') }}</strong>,
            telah dilaksanakan Seminar Proposal/Hasil bagi mahasiswa:
        </p>

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
                <td>"{{ $detail->thesis->title }}"</td>
            </tr>
        </table>

        <p>Berdasarkan hasil seminar tersebut, dosen penguji memberikan catatan revisi yang harus diselesaikan oleh
            mahasiswa sebagai syarat untuk melanjutkan ke tahapan berikutnya.</p>

        <div style="margin-top: 20px;">
            <p><strong>Status Kelulusan Seminar:</strong></p>
            <div
                style="border: 2px solid #000; padding: 15px; text-align: center; font-weight: bold; font-size: 18px; margin: 10px 0;">
                @if($detail->isAllRevisionsApproved())
                    LULUS (REVISI TELAH DISETUJUI)
                @else
                    LULUS BERSYARAT (MENUNGGU PENYELESAIAN REVISI)
                @endif
            </div>
        </div>

        <div class="footer" style="margin-top: 20px;">
            <div style="text-align: right; margin-bottom: 20px;">
                Subang, {{ now()->locale('id')->translatedFormat('d F Y') }}
            </div>

            <table class="signature-table">
                <tr>
                    <td>
                        Penguji I,<br>
                        <div style="margin-top: 10px;">
                            @if($detail->examiner1 && $detail->examiner1->signature)
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $detail->examiner1->signature))) }}"
                                    style="height: 50px; width: auto;">
                            @elseif($detail->examiner1 && $detail->examiner1->signature_token)
                                <img
                                    src="data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(50)->generate(url('/verify-signature/' . $detail->examiner1->signature_token))) }}">
                            @else
                                <div style="height: 50px;"></div>
                            @endif
                        </div>
                        <strong>( {{ $detail->examiner1->name }} )</strong>
                    </td>
                    <td>
                        Penguji II,<br>
                        <div style="margin-top: 10px;">
                            @if($detail->examiner2 && $detail->examiner2->signature)
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $detail->examiner2->signature))) }}"
                                    style="height: 50px; width: auto;">
                            @elseif($detail->examiner2 && $detail->examiner2->signature_token)
                                <img
                                    src="data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(50)->generate(url('/verify-signature/' . $detail->examiner2->signature_token))) }}">
                            @else
                                <div style="height: 50px;"></div>
                            @endif
                        </div>
                        <strong>( {{ $detail->examiner2->name }} )</strong>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 40px;">
                        Mengetahui,<br>
                        Ketua Program Studi,<br>
                        <div style="margin-top: 60px;"></div>
                        <strong>( Bagus Ali Akbar, S.SI., M.Kom )</strong>
                    </td>
                    <td style="padding-top: 40px;">
                        <br>
                        Dosen Pembimbing,<br>
                        <div style="margin-top: 10px;">
                            @php
                                $p1 = $detail->thesis->pembimbing1;
                            @endphp
                            @if($p1 && $p1->signature)
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/' . $p1->signature))) }}"
                                    style="height: 50px; width: auto;">
                            @elseif($p1 && $p1->signature_token)
                                <img
                                    src="data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(50)->generate(url('/verify-signature/' . $p1->signature_token))) }}">
                            @else
                                <div style="height: 50px;"></div>
                            @endif
                        </div>
                        <strong>( {{ $p1 ? $p1->name : '-' }} )</strong>
                    </td>
                </tr>
            </table>

            <!-- Document Verification QR -->
            <div style="margin-top: 40px; border-top: 1px solid #eee; pt-10px;">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="border: none; width: 65px; padding: 0;">
                            @if($detail->verification_token)
                                <img
                                    src="data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(60)->margin(0)->generate(route('document.verify', $detail->verification_token))) }}">
                            @else
                                <div style="width: 60px; height: 60px; background: #eee;"></div>
                            @endif
                        </td>
                        <td style="border: none; vertical-align: middle; text-align: left; padding-left: 10px;">
                            <div style="font-size: 8px; color: #666; line-height: 1.2;">
                                Dokumen ini diterbitkan secara elektronik oleh SIBIMA Fasilkom Unsub.<br>
                                Keaslian dokumen dapat diverifikasi dengan memindai QR Code di samping atau
                                mengunjungi:<br>
                                <span
                                    style="color: #4f46e5;">{{ $detail->verification_token ? route('document.verify', $detail->verification_token) : 'Link Verifikasi Tidak Tersedia' }}</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
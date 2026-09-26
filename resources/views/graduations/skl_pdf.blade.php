<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Lulus - {{ $graduation->student->name }}</title>
    <style>
        @page {
            margin: 2cm 2.2cm 2cm 2.2cm;
            size: A4 portrait;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.35;
            color: #111;
            font-size: 11pt;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat Resmi */
        .kop-surat {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 22px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-table td {
            border: none !important;
            padding: 0 !important;
            vertical-align: middle;
        }

        .logo-cell {
            width: 75px;
            text-align: left;
        }

        .logo-img {
            width: 70px;
            height: auto;
        }

        .info-cell {
            text-align: center;
            padding-right: 75px !important;
        }

        .univ-name {
            font-size: 15pt;
            font-weight: normal;
            letter-spacing: 0.5px;
            margin: 0;
            padding: 0;
        }

        .faculty-name {
            font-size: 17pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 2px 0;
            padding: 0;
        }

        .accreditation {
            font-size: 9.5pt;
            font-weight: bold;
            margin: 1px 0;
        }

        .address, .email {
            font-size: 8.5pt;
            margin: 1px 0;
        }

        /* Judul Dokumen */
        .title-block {
            text-align: center;
            margin-bottom: 22px;
        }

        .title-block h2 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0 0 4px 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .title-block p {
            margin: 0;
            font-size: 10.5pt;
            font-weight: bold;
        }

        /* Isi Surat */
        .intro-text {
            text-align: justify;
            margin-bottom: 14px;
            text-indent: 35px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            margin-left: 20px;
        }

        .data-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 11pt;
        }

        .data-table .label {
            width: 190px;
        }

        .data-table .separator {
            width: 15px;
            text-align: center;
        }

        .data-table .value {
            font-weight: bold;
        }

        .lulus-box {
            text-align: center;
            margin: 14px 0;
            padding: 6px;
        }

        .lulus-box span {
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 5px;
            border-bottom: 2px solid #000;
            padding-bottom: 2px;
        }

        .closing-text {
            text-align: justify;
            margin-top: 14px;
            margin-bottom: 25px;
            text-indent: 35px;
        }

        /* Tanda Tangan & QR Code */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .signature-table td {
            vertical-align: top;
            border: none;
            padding: 0;
        }

        .qr-section {
            width: 45%;
            font-size: 8.5pt;
            color: #444;
            line-height: 1.25;
        }

        .qr-card {
            border: 1px solid #ccc;
            padding: 8px 10px;
            border-radius: 6px;
            background-color: #fafafa;
            display: inline-block;
        }

        .qr-card table {
            border-collapse: collapse;
        }

        .qr-card td {
            vertical-align: middle;
        }

        .signature-section {
            width: 55%;
            text-align: left;
            padding-left: 80px;
        }

        .signer-title {
            margin-bottom: 5px;
            font-size: 10.5pt;
        }

        .signer-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 55px;
            font-size: 11pt;
        }

        .signer-nip {
            font-size: 10pt;
            margin-top: 2px;
        }

        /* Footer Watermark */
        .system-footer {
            position: fixed;
            bottom: -15px;
            left: 0;
            right: 0;
            font-size: 7.5pt;
            color: #777;
            text-align: center;
            border-top: 0.5px solid #ddd;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <!-- KOP SURAT -->
    <div class="kop-surat">
        <table class="kop-table">
            <tr>
                <td class="logo-cell">
                    @if(file_exists(public_path('logo_unsub.png')))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo_unsub.png'))) }}" class="logo-img">
                    @endif
                </td>
                <td class="info-cell">
                    <div class="univ-name">UNIVERSITAS SUBANG</div>
                    <div class="faculty-name">FAKULTAS ILMU KOMPUTER</div>
                    <div class="accreditation">Akreditasi BAIK SEKALI No. 110/SK/LAM-INFOKOM/Ak/S/VIII/2025</div>
                    <div class="address">Jalan R.A Kartini KM 3 Telp (0260) 411415 Subang</div>
                    <div class="email">E-Mail: <span style="color: blue; text-decoration: underline;">fasilkom@unsub.ac.id</span> | Website: <span style="color: blue; text-decoration: underline;">https://fasilkomunsub.com</span></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- JUDUL SURAT -->
    <div class="title-block">
        <h2>SURAT KETERANGAN LULUS</h2>
        <p>Nomor: {{ $graduation->skl_number }}</p>
    </div>

    <!-- PARAGRAF PEMBUKA -->
    <div class="intro-text">
        Dekan Fakultas Ilmu Komputer Universitas Subang dengan ini menerangkan dengan sesungguhnya bahwa mahasiswa di bawah ini:
    </div>

    <!-- BIODATA MAHASISWA -->
    <table class="data-table">
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="separator">:</td>
            <td class="value">{{ strtoupper($graduation->student->name) }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Pokok Mahasiswa (NPM)</td>
            <td class="separator">:</td>
            <td class="value">{{ $graduation->student->identifier ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Program Studi</td>
            <td class="separator">:</td>
            <td class="value">
                @php
                    $identifier = strtoupper(trim($graduation->student->identifier ?? ''));
                    $prodiName = str_starts_with($identifier, 'D1A') ? 'Sistem Informasi' : (str_starts_with($identifier, 'D1B') ? 'Teknik Informatika' : 'Ilmu Komputer');
                @endphp
                {{ $prodiName }}
            </td>
        </tr>
        <tr>
            <td class="label">Jenjang Pendidikan</td>
            <td class="separator">:</td>
            <td class="value">Strata Satu (S1)</td>
        </tr>
        <tr>
            <td class="label">Tempat, Tanggal Lahir</td>
            <td class="separator">:</td>
            <td class="value">
                {{ $graduation->student->birth_date ? $graduation->student->birth_date->locale('id')->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
    </table>

    <div class="intro-text" style="margin-top: 10px; margin-bottom: 8px;">
        Telah menyelesaikan seluruh kewajiban akademik, dinyatakan bebas tanggungan program studi/perpustakaan, dan telah lulus Sidang Skripsi pada:
    </div>

    <!-- BOX LULUS -->
    <div class="lulus-box">
        <span>L U L U S</span>
    </div>

    <table class="data-table" style="margin-top: 10px;">
        <tr>
            <td class="label">Tanggal Kelulusan / Yudisium</td>
            <td class="separator">:</td>
            <td class="value">{{ $graduation->formatted_graduation_date }}</td>
        </tr>
        <tr>
            <td class="label">Judul Skripsi</td>
            <td class="separator">:</td>
            <td class="value" style="font-weight: normal; font-style: italic;">
                "{{ $graduation->thesis->display_title }}"
            </td>
        </tr>
        <tr>
            <td class="label">Dosen Pembimbing 1</td>
            <td class="separator">:</td>
            <td class="value" style="font-weight: normal;">{{ $graduation->thesis->pembimbing1->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Dosen Pembimbing 2</td>
            <td class="separator">:</td>
            <td class="value" style="font-weight: normal;">{{ $graduation->thesis->pembimbing2->name ?? '-' }}</td>
        </tr>
        @if($graduation->gpa)
        <tr>
            <td class="label">Indeks Prestasi Kumulatif (IPK)</td>
            <td class="separator">:</td>
            <td class="value">{{ number_format($graduation->gpa, 2) }}</td>
        </tr>
        @endif
        @if($graduation->predicate)
        <tr>
            <td class="label">Predikat Kelulusan</td>
            <td class="separator">:</td>
            <td class="value">{{ $graduation->predicate }}</td>
        </tr>
        @endif
    </table>

    <!-- PENUTUP -->
    <div class="closing-text">
        Surat Keterangan Lulus (SKL) ini diterbitkan secara sah dan berlaku sebagai bukti kelulusan sementara yang dapat dipergunakan sebagaimana mestinya sebelum Ijazah dan Transkrip Nilai asli diterbitkan.
    </div>

    <!-- TANDA TANGAN & QR CODE -->
    <table class="signature-table">
        <tr>
            <td class="qr-section">
                <div class="qr-card">
                    <table>
                        <tr>
                            <td style="padding-right: 10px;">
                                <img src="data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(65)->margin(0)->generate(route('skl.verify', $graduation->verification_token))) }}">
                            </td>
                            <td>
                                <strong style="color: #0c1427; font-size: 8.5pt;">DOKUMEN RESMI DIGITAL</strong><br>
                                <span style="font-size: 7.5pt; color: #555;">Pindai QR Code untuk verifikasi keabsahan dokumen SKL ini secara daring di sistem SIBIMA UNSUB.</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td class="signature-section">
                <div>Subang, {{ $graduation->approved_at ? $graduation->approved_at->locale('id')->translatedFormat('d F Y') : now()->locale('id')->translatedFormat('d F Y') }}</div>
                <div class="signer-title">
                    Ketua Program Studi {{ $prodiName }},
                </div>
                
                @if($kaprodi && $kaprodi->signature)
                    <div style="height: 55px; margin-top: 5px;">
                        <img src="{{ $kaprodi->signature }}" style="max-height: 50px; max-width: 140px;" alt="Tanda Tangan">
                    </div>
                    <div class="signer-name" style="margin-top: 5px;">{{ $kaprodi->name }}</div>
                @else
                    <div class="signer-name">{{ $kaprodi->name ?? 'Ketua Program Studi' }}</div>
                @endif
                <div class="signer-nip">NIDN. {{ $kaprodi->identifier ?? '-' }}</div>
            </td>
        </tr>
    </table>

    <!-- FOOTER WATERMARK -->
    <div class="system-footer">
        Dicetak otomatis melalui SIBIMA (Sistem Informasi Bimbingan Mahasiswa) FASILKOM UNSUB | Token Keamanan: {{ substr($graduation->verification_token, 0, 16) }}...
    </div>
</body>
</html>

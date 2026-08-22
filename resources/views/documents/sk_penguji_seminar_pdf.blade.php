<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
            font-size: 12pt;
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
        
        .content { padding: 0 50px; }
        .title-block { text-align: center; margin-bottom: 25px; }
        .title-block h2 { text-decoration: underline; margin-bottom: 0; font-size: 14pt; }
        .title-block p { margin-top: 5px; font-weight: bold; }
        
        .section-text { margin-bottom: 15px; text-align: justify; }
        .data-table { width: 100%; margin-bottom: 15px; }
        .data-table td { padding: 3px 0; vertical-align: top; }
        .data-table td:first-child { width: 150px; }
        .data-table td:nth-child(2) { width: 15px; text-align: center; }

        .main-table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10pt; }
        .main-table th, .main-table td { border: 1px solid black; padding: 6px; vertical-align: top; }
        .main-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }

        .footer { margin-top: 40px; float: right; width: 250px; }
    </style>
</head>
<body>
    <div class="kop-surat">
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

    <div class="content">
        <div class="title-block">
            <h2>SURAT KEPUTUSAN DEKAN FAKULTAS ILMU KOMPUTER</h2>
            <p>Nomor: {{ $letterNumber }}</p>
            <p style="margin-top: 10px;">Tentang:<br>PENETAPAN TIM PENGUJI SEMINAR SKRIPSI MAHASISWA<br>FAKULTAS ILMU KOMPUTER UNIVERSITAS SUBANG</p>
        </div>

        <div class="decree-text">
            Dekan Fakultas Ilmu Komputer Universitas Subang, setelah memperhatikan usulan Koordinator Skripsi, dengan ini menetapkan Tim Penguji Seminar Skripsi yang akan dilaksanakan pada:
        </div>

        <table style="width: 100%; margin-bottom: 10px;">
            <tr>
                <td style="width: 120px;">Hari / Tanggal</td>
                <td style="width: 10px;">:</td>
                <td>{{ \Carbon\Carbon::parse($schedule->date)->locale('id')->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td>Tempat</td>
                <td>:</td>
                <td>{{ $schedule->location ?: '-' }}</td>
            </tr>
        </table>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 150px;">Mahasiswa (NPM)</th>
                    <th>Judul Skripsi</th>
                    <th style="width: 150px;">Tim Penguji</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($schedule->details as $detail)
                    @if($detail->thesis_id)
                    <tr>
                        <td style="text-align: center;">{{ $no++ }}</td>
                        <td><strong>{{ $detail->thesis->student->name }}</strong><br>({{ $detail->thesis->student->identifier }})</td>
                        <td style="font-style: italic;">{{ $detail->thesis->title }}</td>
                        <td>
                            1. {{ $schedule->chairman->name }} (Ketua)<br>
                            2. {{ $detail->examiner1->name }} (Anggota I)<br>
                            3. {{ $detail->examiner2->name }} (Anggota II)
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <div class="decree-text" style="margin-top: 15px;">
            Keputusan ini mulai berlaku sejak tanggal ditetapkan dengan ketentuan apabila di kemudian hari terdapat kekeliruan dalam keputusan ini akan diadakan perbaikan sebagaimana mestinya.
        </div>

        <table class="footer-table">
            <tr>
                <td style="width: 60%;"></td>
                <td>
                    Ditetapkan di : Subang<br>
                    Pada Tanggal : {{ now()->locale('id')->translatedFormat('d F Y') }}<br>
                    Dekan,<br><br><br><br><br>
                    <strong><u>( Nama Dekan )</u></strong><br>
                    NIP/NIK. .........................
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

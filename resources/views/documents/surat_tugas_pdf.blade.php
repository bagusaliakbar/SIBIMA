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
        .kop-surat table { width: 100%; border: none; }
        .kop-surat td { border: none !important; padding: 0 !important; vertical-align: middle; }
        .kop-surat .logo-cell { width: 80px; text-align: left; }
        .kop-surat .logo-img { width: 70px; height: auto; }
        .kop-surat .info-cell { text-align: center; padding-right: 80px !important; }
        .kop-surat .univ-name { font-size: 16px; margin: 0; }
        .kop-surat .faculty-name { font-size: 18px; margin: 0; font-weight: bold; }
        .kop-surat .address { font-size: 9px; margin: 1px 0; }
        
        .content { padding: 0 50px; }
        .title-block { text-align: center; margin-bottom: 25px; }
        .title-block h2 { text-decoration: underline; margin-bottom: 0; font-size: 14pt; }
        .title-block p { margin-top: 5px; font-weight: bold; }
        
        .section-text { margin-bottom: 15px; text-align: justify; }
        .data-table { width: 100%; margin-bottom: 15px; }
        .data-table td { padding: 3px 0; vertical-align: top; }
        .data-table td:first-child { width: 150px; }
        .data-table td:nth-child(2) { width: 15px; text-align: center; }

        .examiner-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .examiner-table th, .examiner-table td { border: 1px solid black; padding: 5px; text-align: left; }
        .examiner-table th { background-color: #f2f2f2; text-align: center; }

        .footer { margin-top: 40px; float: right; width: 250px; }
    </style>
</head>
<body>
    <div class="kop-surat">
        <table>
            <tr>
                <td class="logo-cell">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/logo.png'))) }}" class="logo-img">
                </td>
                <td class="info-cell">
                    <div class="univ-name">YAYASAN PENDIDIKAN SANG ADIPATI SUTAJAYA</div>
                    <div class="faculty-name">UNIVERSITAS SUBANG</div>
                    <div class="faculty-name text-lg">FAKULTAS ILMU KOMPUTER</div>
                    <div class="address">Jl. R.A. Kartini No. 37 Telp. (0260) 414571 Fax. (0260) 414571 Subang 41211</div>
                    <div class="address text-[8px]">Website: www.unsub.ac.id | Email: fik@unsub.ac.id</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="content">
        <div class="title-block">
            <h2>SURAT TUGAS</h2>
            <p>Nomor: {{ $letterNumber }}</p>
        </div>

        <div class="section-text">
            Dekan Fakultas Ilmu Komputer Universitas Subang, dengan ini menugaskan kepada Dosen yang namanya tercantum di bawah ini untuk menjadi <strong>Tim Penguji Seminar Skripsi</strong> mahasiswa:
        </div>

        <table class="data-table">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><strong>{{ $detail->thesis->student->name }}</strong></td>
            </tr>
            <tr>
                <td>NPM</td>
                <td>:</td>
                <td>{{ $detail->thesis->student->identifier }}</td>
            </tr>
            <tr>
                <td>Judul Skripsi</td>
                <td>:</td>
                <td><em>{{ $detail->thesis->title }}</em></td>
            </tr>
        </table>

        <div class="section-text">Pelaksanaan seminar akan dilaksanakan pada:</div>
        <table class="data-table">
            <tr>
                <td>Hari / Tanggal</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($detail->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($detail->end_time)->format('H:i') }} WIB</td>
            </tr>
            <tr>
                <td>Tempat</td>
                <td>:</td>
                <td>{{ $detail->schedule->location ?: '-' }}</td>
            </tr>
        </table>

        <div class="section-text">Adapun Tim Penguji adalah sebagai berikut:</div>
        <table class="examiner-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Nama Dosen</th>
                    <th>Jabatan dalam Tim</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>{{ $detail->schedule->chairman->name }}</td>
                    <td>Ketua Penguji</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td>{{ $detail->examiner1->name }}</td>
                    <td>Anggota Penguji I</td>
                </tr>
                <tr>
                    <td style="text-align: center;">3</td>
                    <td>{{ $detail->examiner2->name }}</td>
                    <td>Anggota Penguji II</td>
                </tr>
            </tbody>
        </table>

        <div class="section-text" style="margin-top: 15px;">
            Demikian surat tugas ini diberikan agar dapat dilaksanakan dengan penuh tanggung jawab.
        </div>

        <div class="footer">
            Subang, {{ now()->locale('id')->translatedFormat('d F Y') }}<br>
            Dekan,<br><br><br><br><br>
            <strong><u>( Nama Dekan )</u></strong><br>
            NIP/NIK. .........................
        </div>
    </div>
</body>
</html>

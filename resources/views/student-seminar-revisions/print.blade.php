<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Pernyataan Revisi</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11pt; color: #000; line-height: 1.4; }
        .kop-surat { width: 100%; border-bottom: 4px double #000; padding-bottom: 10px; margin-bottom: 25px; }
        .kop-surat table { width: 100%; border-collapse: collapse; }
        .kop-surat .logo { width: 85px; text-align: left; vertical-align: middle; }
        .kop-surat .text { text-align: center; vertical-align: middle; }
        .kop-surat .text h1 { font-size: 14pt; margin: 0; font-weight: normal; letter-spacing: 1px; }
        .kop-surat .text h2 { font-size: 19pt; margin: 2px 0; font-weight: bold; }
        .kop-surat .text p { font-size: 8.5pt; margin: 1px 0; color: #333; }
        
        .title { text-align: center; font-size: 13pt; font-weight: bold; margin-bottom: 25px; text-decoration: none; }
        .opening { margin-bottom: 15px; text-align: left; }
        
        .biodata { margin-bottom: 25px; }
        .biodata table { width: 100%; border-collapse: collapse; }
        .biodata td { padding: 4px 0; vertical-align: top; font-weight: bold; }
        .biodata .label { width: 130px; }
        .biodata .colon { width: 20px; }
        
        .opini-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; table-layout: fixed; }
        .opini-table th { background-color: #d1d5db; border: 1.5px solid #000; padding: 10px; font-size: 13pt; font-weight: bold; text-align: center; }
        .opini-table td { border: 1.5px solid #000; padding: 20px; height: 450px; vertical-align: top; word-wrap: break-word; }
        
        .footer { width: 100%; margin-top: 10px; }
        .footer-content { float: right; width: 260px; text-align: left; }
        .footer-content p { margin: 3px 0; font-size: 11pt; }
        .signature-space { height: 90px; }
        
        .note { font-size: 10pt; font-style: italic; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="kop-surat">
        <table>
            <tr>
                <td class="logo">
                    @php
                        $logoPath = public_path('logo_unsub.png');
                        $logoData = "";
                        if(file_exists($logoPath)) {
                            $logoData = base64_encode(file_get_contents($logoPath));
                        }
                    @endphp
                    @if($logoData)
                        <img src="data:image/png;base64,{{ $logoData }}" width="85">
                    @endif
                </td>
                <td class="text">
                    <div style="margin-bottom: 2px;">UNIVERSITAS SUBANG</div>
                    <div style="font-size: 19pt; font-weight: bold; margin-bottom: 2px;">FAKULTAS ILMU KOMPUTER</div>
                    <p>Akreditasi BAIK SEKALI No. 110/SK/LAM-INFOKOM/Ak/S/VIII/2025</p>
                    <p>Jalan R.A Kartini KM 3 Telp (0260) 411415 Subang</p>
                    <p>E-Mail : <span style="color: #0000EE; text-decoration: underline;">fasilkom@unsub.ac.id</span></p>
                </td>
            </tr>
        </table>
    </div>

    <div class="title">PERNYATAAN REVISI</div>

    <div class="opening">
        Yang bertandatangan di bawah ini, penguji seminar skripsi Fakultas Ilmu Komputer Universitas Subang menerangkan bahwa :
    </div>

    <div class="biodata">
        <table>
            <tr>
                <td class="label">NPM</td>
                <td class="colon">:</td>
                <td>{{ $revision->detail->thesis->student->identifier }}</td>
            </tr>
            <tr>
                <td class="label">Nama</td>
                <td class="colon">:</td>
                <td>{{ $revision->detail->thesis->student->name }}</td>
            </tr>
            <tr>
                <td class="label">Judul Skripsi</td>
                <td class="colon">:</td>
                <td>{{ $revision->detail->thesis->title }}</td>
            </tr>
        </table>
    </div>

    <table class="opini-table">
        <thead>
            <tr>
                <th>Opini</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-size: 10pt; line-height: 1.6;">
                    {!! nl2br(e($firstMessage->message)) !!}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="footer-content">
            <p>Subang, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <p>Penguji</p>
            <div class="signature-space"></div>
            <p><strong>{{ $revision->examiner->name }}</strong></p>
        </div>
    </div>

    <div style="clear: both;"></div>
    <div class="note">
        Catatan : Mohon di isi dengan rapi
    </div>
</body>
</html>

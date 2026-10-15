<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Pernyataan Revisi Sidang</title>
    <style>
        @page { margin: 1.2cm 1.5cm 1cm 1.5cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10pt; color: #000; line-height: 1.35; }
        .kop-surat { width: 100%; margin-bottom: 12px; text-align: center; }
        .kop-img { width: 100%; height: auto; display: block; }
        .kop-table-wrapper { border-bottom: 4px double #000; padding-bottom: 8px; }
        .kop-surat table { width: 100%; border-collapse: collapse; }
        .kop-surat .logo { width: 80px; text-align: left; vertical-align: middle; }
        .kop-surat .text { text-align: center; vertical-align: middle; }
        .kop-surat .text h1 { font-size: 13pt; margin: 0; font-weight: normal; letter-spacing: 1px; }
        .kop-surat .text h2 { font-size: 17pt; margin: 2px 0; font-weight: bold; }
        .kop-surat .text p { font-size: 8pt; margin: 1px 0; color: #333; }
        
        .title { text-align: center; font-size: 12.5pt; font-weight: bold; margin-bottom: 12px; text-decoration: none; }
        .opening { margin-bottom: 10px; text-align: left; font-size: 10pt; }
        
        .biodata { margin-bottom: 12px; }
        .biodata table { width: 100%; border-collapse: collapse; }
        .biodata td { padding: 2.5px 0; vertical-align: top; font-weight: bold; font-size: 9.5pt; }
        .biodata .label { width: 130px; }
        .biodata .colon { width: 20px; }
        
        .opini-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; table-layout: fixed; page-break-inside: avoid; }
        .opini-table th { background-color: #d1d5db; border: 1.5px solid #000; padding: 7px 10px; font-size: 11pt; font-weight: bold; text-align: center; }
        .opini-table td { border: 1.5px solid #000; padding: 12px 15px; height: 210px; vertical-align: top; word-wrap: break-word; }
        
        .footer { width: 100%; margin-top: 10px; page-break-inside: avoid; }
        .footer-content { float: right; width: 260px; text-align: left; }
        .footer-content p { margin: 2px 0; font-size: 10pt; }
        .signature-space { height: 60px; }
        
        .note { font-size: 8.5pt; font-style: italic; margin-top: 15px; }
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
        @endif
    </div>

    <div class="title">PERNYATAAN REVISI HASIL SIDANG</div>

    <div class="opening">
        Yang bertandatangan di bawah ini, penguji sidang skripsi Fakultas Ilmu Komputer Universitas Subang menerangkan bahwa :
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
                <th>Opini / Catatan Revisi Sidang</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-size: 10pt; line-height: 1.6;">
                    @php
                        $lines = explode("\n", str_replace("\r", "", (string) $firstMessage->message));
                        $parsedPoints = [];
                        $parsedNotes = [];
                        $isNumbered = false;
                        foreach ($lines as $line) {
                            $trimmed = trim($line);
                            if ($trimmed === '') continue;
                            if (preg_match('/^(\d+)[\.\)]\s*(.+)$/u', $trimmed, $m)) {
                                $isNumbered = true;
                                $parsedPoints[] = ['num' => $m[1], 'text' => $m[2]];
                            } elseif ($isNumbered && !empty($parsedPoints)) {
                                $parsedNotes[] = $trimmed;
                            }
                        }
                    @endphp

                    @if($isNumbered && count($parsedPoints) > 0)
                        <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 12px;">
                            @foreach($parsedPoints as $p)
                                <tr>
                                    <td style="width: 26px; vertical-align: top; border: none; padding: 4px 0; font-weight: bold; font-size: 10pt; height: auto;">
                                        {{ $p['num'] }}.
                                    </td>
                                    <td style="vertical-align: top; border: none; padding: 4px 0; font-size: 10pt; line-height: 1.5; height: auto;">
                                        {{ $p['text'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        @if(!empty($parsedNotes))
                            <div style="margin-top: 15px; padding-top: 10px; border-top: 1px dashed #999; font-size: 9.5pt; font-style: italic; color: #333;">
                                {!! nl2br(e(implode("\n", $parsedNotes))) !!}
                            </div>
                        @endif
                    @else
                        {!! nl2br(e($firstMessage->message)) !!}
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="footer-content">
            <p>Subang, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
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

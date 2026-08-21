<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Mahasiswa Belum Mengajukan Judul</title>
    <style>
        @page {
            size: landscape;
            margin: 1cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #333;
            line-height: 1.4;
        }
        .kop-surat {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .kop-surat table {
            width: 100%;
            border: none;
        }
        .kop-surat td {
            border: none !important;
            padding: 0 !important;
            vertical-align: middle;
        }
        .kop-surat .logo-cell {
            width: 90px;
            text-align: left;
        }
        .kop-surat .logo-img {
            width: 80px;
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
            font-size: 11px;
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
            margin-bottom: 15px;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 13pt;
            text-decoration: underline;
        }
        .meta-info {
            margin-bottom: 12px;
            font-size: 9pt;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
            vertical-align: middle;
        }
        table.data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            font-size: 8.5pt;
            text-transform: uppercase;
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            width: 100%;
        }
        .footer-table {
            width: 100%;
            border: none;
        }
        .footer-table td {
            border: none;
            width: 50%;
        }
        .signature-container {
            text-align: right;
            padding-right: 40px;
        }
        .signature-box {
            display: inline-block;
            text-align: center;
        }
        .signature-img {
            height: 80px;
            margin: 5px 0;
        }
        .qr-code {
            height: 70px;
            margin: 5px 0;
        }
        .badge-critical {
            color: #dc2626;
            font-weight: bold;
        }
        .badge-warning {
            color: #d97706;
            font-weight: bold;
        }
        .badge-normal {
            color: #16a34a;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="kop-surat">
        <table>
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
                    <div class="email">E-Mail : <span style="color: blue; text-decoration: underline;">fasilkom@unsub.ac.id</span></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="report-title">
        Daftar Mahasiswa Terdaftar Belum Mengajukan Judul Skripsi
    </div>

    <div class="meta-info">
        <strong>Total Mahasiswa:</strong> {{ $students->count() }} Orang &nbsp;|&nbsp; 
        <strong>Kriteria:</strong> 
        {{ $entryYear ? "Angkatan $entryYear" : "Semua Angkatan" }}
        @if($semesterFilter === 'critical')
            - (Khusus Semester Kritis &ge; 13)
        @elseif($semesterFilter === 'warning')
            - (Khusus Semester Perhatian 7&ndash;12)
        @elseif($semesterFilter === 'normal')
            - (Khusus Semester Normal &lt; 7)
        @endif
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 170px;">Nama Mahasiswa</th>
                <th style="width: 85px;">NPM</th>
                <th style="width: 60px;">Angkatan</th>
                <th style="width: 85px;">Semester</th>
                <th style="width: 150px;">Email</th>
                <th style="width: 100px;">No. WhatsApp</th>
                <th style="width: 90px;">Tgl Daftar</th>
                <th style="width: 75px;">Lama Akun</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
                @php
                    $semester = $student->current_semester ?? '-';
                    $isCritical = $student->is_critical_semester;
                    $isWarning = $semester >= 7 && !$isCritical;
                    $daysSinceCreation = $student->created_at ? (int) $student->created_at->diffInDays(now()) : 0;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td><strong>{{ strtoupper($student->name) }}</strong></td>
                    <td style="text-align: center; font-family: monospace;">{{ $student->identifier ?? '-' }}</td>
                    <td style="text-align: center;">{{ $student->entry_year ?? '-' }}</td>
                    <td style="text-align: center;">
                        @if($isCritical)
                            <span class="badge-critical">Sem {{ $semester }} (Kritis)</span>
                        @elseif($isWarning)
                            <span class="badge-warning">Sem {{ $semester }} (Perhatian)</span>
                        @else
                            <span class="badge-normal">Sem {{ $semester }}</span>
                        @endif
                    </td>
                    <td>{{ $student->email }}</td>
                    <td style="text-align: center;">{{ $student->phone_number ?? '-' }}</td>
                    <td style="text-align: center;">{{ $student->created_at ? $student->created_at->format('d/m/Y') : '-' }}</td>
                    <td style="text-align: center;">{{ $daysSinceCreation }} Hari</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px; color: #666;">
                        Tidak ada data mahasiswa yang belum mengajukan judul sesuai kriteria filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td style="font-size: 8pt; color: #666; vertical-align: bottom;">
                    Dicetak pada: {{ now()->locale('id')->translatedFormat('d F Y H:i') }} WIB melalui Sistem SIBIMA
                </td>
                <td class="signature-container">
                    <div class="signature-box">
                        <p style="margin-bottom: 5px;">Subang, {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
                        <p style="margin-bottom: 10px;">Mengetahui,<br>Ketua Program Studi</p>
                        
                        <div style="min-height: 80px;">
                            @if(isset($kaprodi) && $kaprodi && $kaprodi->signature)
                                <img src="{{ $kaprodi->decrypted_signature }}" class="signature-img">
                            @elseif(isset($kaprodi) && $kaprodi && $kaprodi->signature_token)
                                <img src="data:image/png;base64, {!! base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(200)->generate(url('/verify-signature/' . $kaprodi->signature_token))) !!} " class="qr-code">
                            @else
                                <br><br><br>
                            @endif
                        </div>
                        
                        <p style="margin-top: 5px;"><strong>( {{ (isset($kaprodi) && $kaprodi) ? $kaprodi->name : 'Ketua Program Studi' }} )</strong></p>
                        <p style="font-size: 8pt; color: #666; margin-top: -3px;">NIDN: {{ (isset($kaprodi) && $kaprodi) ? $kaprodi->identifier : '........................' }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

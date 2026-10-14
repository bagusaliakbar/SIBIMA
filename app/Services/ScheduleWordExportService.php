<?php

namespace App\Services;

use App\Models\SeminarSchedule;
use App\Models\ThesisDefenseSchedule;
use App\Models\User;
use App\Models\Wave;
use Carbon\Carbon;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Common\ErrorCorrectionLevel;

class ScheduleWordExportService
{
    /**
     * Generate Word (.docx) document for a single seminar schedule.
     */
    public function exportSeminar(SeminarSchedule $schedule, ?User $kaprodi = null): string
    {
        return $this->generateSingleScheduleDoc($schedule, 'seminar', $kaprodi);
    }

    /**
     * Generate Word (.docx) document for a batch of seminar schedules.
     */
    public function exportSeminarBatch($schedules, ?Wave $wave = null, ?User $kaprodi = null): string
    {
        return $this->generateBatchScheduleDoc($schedules, 'seminar', $wave, $kaprodi);
    }

    /**
     * Generate Word (.docx) document for a single thesis defense schedule.
     */
    public function exportDefense(ThesisDefenseSchedule $schedule, ?User $kaprodi = null): string
    {
        return $this->generateSingleScheduleDoc($schedule, 'defense', $kaprodi);
    }

    /**
     * Generate Word (.docx) document for a batch of thesis defense schedules.
     */
    public function exportDefenseBatch($schedules, ?Wave $wave = null, ?User $kaprodi = null): string
    {
        return $this->generateBatchScheduleDoc($schedules, 'defense', $wave, $kaprodi);
    }

    /**
     * Generate a single schedule docx.
     */
    protected function generateSingleScheduleDoc($schedule, string $type, ?User $kaprodi = null): string
    {
        $kaprodi = $kaprodi ?? (User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first());
        $tempImages = [];

        try {
            $phpWord = $this->createPhpWordInstance();
            $section = $this->createLandscapeSection($phpWord);

            $this->buildScheduleContent($section, $schedule, $type, $kaprodi, $tempImages);

            return $this->saveToTempDocx($phpWord);
        } finally {
            $this->cleanupTempImages($tempImages);
        }
    }

    /**
     * Generate a batch schedules docx with page breaks between schedules.
     */
    protected function generateBatchScheduleDoc($schedules, string $type, ?Wave $wave = null, ?User $kaprodi = null): string
    {
        $kaprodi = $kaprodi ?? (User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first());
        $tempImages = [];

        try {
            $phpWord = $this->createPhpWordInstance();
            $section = $this->createLandscapeSection($phpWord);

            $isFirst = true;
            foreach ($schedules as $schedule) {
                if (!$isFirst) {
                    $section->addPageBreak();
                }
                $isFirst = false;

                $this->buildScheduleContent($section, $schedule, $type, $kaprodi, $tempImages);
            }

            return $this->saveToTempDocx($phpWord);
        } finally {
            $this->cleanupTempImages($tempImages);
        }
    }

    /**
     * Create configured PhpWord instance.
     */
    protected function createPhpWordInstance(): PhpWord
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(8);
        return $phpWord;
    }

    /**
     * Add A4 Landscape section with standard margins.
     */
    protected function createLandscapeSection(PhpWord $phpWord)
    {
        return $phpWord->addSection([
            'orientation'   => 'landscape',
            'pageSizeW'     => Converter::cmToTwip(29.7),
            'pageSizeH'     => Converter::cmToTwip(21.0),
            'marginTop'     => Converter::cmToTwip(0.8),
            'marginBottom'  => Converter::cmToTwip(0.8),
            'marginLeft'    => Converter::cmToTwip(1.2),
            'marginRight'   => Converter::cmToTwip(1.2),
        ]);
    }

    /**
     * Build the schedule layout content for one room.
     */
    protected function buildScheduleContent($section, $schedule, string $type, ?User $kaprodi, array &$tempImages): void
    {
        // Total printable width: 29.7cm - 2.4cm = 27.3cm = 15,477 twips
        $totalWidth = 15477;

        // 1. KOP SURAT RESMI FASILKOM UNSUB
        $this->addKopSurat($section, $totalWidth);

        // 2. JUDUL DOKUMEN
        $isDefense = ($type === 'defense');
        $mainTitle = $isDefense ? 'JADWAL SIDANG SKRIPSI' : 'JADWAL SEMINAR SKRIPSI';
        $subTitle = strtoupper($schedule->title);

        $section->addText($mainTitle, [
            'name' => 'Arial',
            'size' => 12,
            'bold' => true,
            'color' => '0F172A',
        ], [
            'alignment' => Jc::CENTER,
            'spaceBefore' => 60,
            'spaceAfter' => 20,
        ]);

        $section->addText($subTitle, [
            'name' => 'Arial',
            'size' => 9,
            'bold' => true,
            'color' => '475569',
        ], [
            'alignment' => Jc::CENTER,
            'spaceBefore' => 0,
            'spaceAfter' => 80,
        ]);

        // 3. METADATA INFO CARD
        $this->addMetaCard($section, $schedule, $totalWidth);

        // 4. TABEL SESI PRESENTASI
        $this->addScheduleTable($section, $schedule, $totalWidth);

        // 5. FOOTER & TANDA TANGAN KAPRODI
        $this->addFooterAndSignature($section, $schedule, $kaprodi, $totalWidth, $tempImages);
    }

    /**
     * Render Kop Surat Header with logo and faculty text.
     */
    protected function addKopSurat($section, int $totalWidth): void
    {
        $kopTable = $section->addTable([
            'borderBottomSize' => 18,
            'borderBottomColor' => '000000',
            'cellMarginBottom' => 60,
            'cellMarginTop' => 0,
            'cellMarginLeft' => 0,
            'cellMarginRight' => 0,
        ]);

        $kopTable->addRow();

        // Logo cell (left)
        $logoWidth = 1400;
        $logoCell = $kopTable->addCell($logoWidth, ['valign' => 'center']);
        $logoPath = public_path('logo_unsub.png');
        if (file_exists($logoPath)) {
            $logoCell->addImage($logoPath, [
                'width' => 52,
                'height' => 52,
                'alignment' => Jc::LEFT,
            ]);
        }

        // Info cell (right)
        $infoWidth = $totalWidth - $logoWidth;
        $infoCell = $kopTable->addCell($infoWidth, ['valign' => 'center']);
        
        $infoCell->addText('UNIVERSITAS SUBANG', [
            'name' => 'Times New Roman',
            'size' => 12,
            'bold' => true,
            'color' => '000000',
        ], ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 0]);

        $infoCell->addText('FAKULTAS ILMU KOMPUTER', [
            'name' => 'Times New Roman',
            'size' => 15,
            'bold' => true,
            'color' => '000000',
        ], ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 0]);

        $infoCell->addText('Akreditasi BAIK SEKALI No. 110/SK/LAM-INFOKOM/Ak/S/VIII/2025', [
            'name' => 'Times New Roman',
            'size' => 8.5,
            'bold' => true,
            'color' => '000000',
        ], ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 0]);

        $infoCell->addText('Jalan R.A Kartini KM 3 Telp (0260) 411415 Subang', [
            'name' => 'Times New Roman',
            'size' => 8,
            'color' => '000000',
        ], ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 0]);

        $infoCell->addText('E-Mail : fasilkom@unsub.ac.id', [
            'name' => 'Times New Roman',
            'size' => 8,
            'color' => '0000FF',
            'underline' => 'single',
        ], ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 40]);
    }

    /**
     * Render Metadata Info Card.
     */
    protected function addMetaCard($section, $schedule, int $totalWidth): void
    {
        $metaTable = $section->addTable([
            'borderColor' => 'E2E8F0',
            'borderSize' => 6,
            'cellMarginTop' => 60,
            'cellMarginBottom' => 60,
            'cellMarginLeft' => 80,
            'cellMarginRight' => 80,
        ]);

        $metaTable->addRow();

        $leftWidth = 10060;
        $rightWidth = $totalWidth - $leftWidth;

        $metaLeft = $metaTable->addCell($leftWidth, ['bgColor' => 'F8FAFC', 'valign' => 'center']);
        $metaRight = $metaTable->addCell($rightWidth, ['bgColor' => 'F8FAFC', 'valign' => 'center']);

        $hariTanggal = Carbon::parse($schedule->date)->locale('id')->translatedFormat('l, d F Y');
        $metaLeft->addText('Hari / Tanggal  : ' . $hariTanggal, ['bold' => true, 'size' => 8, 'color' => '0F172A'], ['spaceBefore' => 0, 'spaceAfter' => 20]);
        $metaLeft->addText('Ketua Sidang    : ' . ($schedule->chairman?->name ?? '-'), ['bold' => true, 'size' => 8, 'color' => '0F172A'], ['spaceBefore' => 0, 'spaceAfter' => 20]);
        $metaLeft->addText('Moderator        : ' . ($schedule->moderator?->name ?? '-'), ['bold' => true, 'size' => 8, 'color' => '0F172A'], ['spaceBefore' => 0, 'spaceAfter' => 0]);

        $location = strtoupper($schedule->location ?: 'BELUM DITENTUKAN');
        $metaRight->addText('RUANGAN: ' . $location, ['bold' => true, 'size' => 8.5, 'color' => '1E3A8A'], ['alignment' => Jc::RIGHT, 'spaceBefore' => 0, 'spaceAfter' => 20]);
        if ($schedule->meeting_link) {
            $metaRight->addText('Link: ' . $schedule->meeting_link, ['size' => 7, 'color' => '2563EB', 'underline' => 'single'], ['alignment' => Jc::RIGHT, 'spaceBefore' => 0, 'spaceAfter' => 0]);
        }

        // Small spacing before table
        $section->addTextBreak(1, ['size' => 4]);
    }

    /**
     * Render Table of Presentation Sessions.
     */
    protected function addScheduleTable($section, $schedule, int $totalWidth): void
    {
        // Proportions: NO: 500, WAKTU: 1350, NPM: 1350, NAMA: 2650, JUDUL: 5150, PEMBIMBING: 2300, PENGUJI: 2177 (Total 15477)
        $wNo = 500;
        $wWaktu = 1350;
        $wNpm = 1350;
        $wNama = 2650;
        $wJudul = 5150;
        $wPembimbing = 2300;
        $wPenguji = $totalWidth - ($wNo + $wWaktu + $wNpm + $wNama + $wJudul + $wPembimbing);

        $table = $section->addTable([
            'borderColor' => 'CBD5E1',
            'borderSize'  => 6,
            'cellMarginTop' => 60,
            'cellMarginBottom' => 60,
            'cellMarginLeft' => 80,
            'cellMarginRight' => 80,
        ]);

        // Header Row
        $table->addRow(null, ['tblHeader' => true, 'cantSplit' => true]);
        $thStyle = ['bgColor' => '0F172A', 'valign' => 'center'];
        $thFont = ['name' => 'Arial', 'size' => 7.5, 'bold' => true, 'color' => 'FFFFFF'];
        $thCenter = ['alignment' => Jc::CENTER, 'spaceBefore' => 20, 'spaceAfter' => 20];

        $table->addCell($wNo, $thStyle)->addText('NO', $thFont, $thCenter);
        $table->addCell($wWaktu, $thStyle)->addText('WAKTU', $thFont, $thCenter);
        $table->addCell($wNpm, $thStyle)->addText('NPM', $thFont, $thCenter);
        $table->addCell($wNama, $thStyle)->addText('NAMA MAHASISWA', $thFont, $thCenter);
        $table->addCell($wJudul, $thStyle)->addText('JUDUL SKRIPSI', $thFont, $thCenter);
        $table->addCell($wPembimbing, $thStyle)->addText('PEMBIMBING', $thFont, $thCenter);
        $table->addCell($wPenguji, $thStyle)->addText('PENGUJI', $thFont, $thCenter);

        $studentNo = 1;
        $rowIndex = 0;

        foreach ($schedule->details as $detail) {
            $isEven = ($rowIndex++ % 2 === 1);
            $bg = $isEven ? 'F8FAFC' : 'FFFFFF';

            if ($detail->thesis_id && $detail->thesis) {
                $table->addRow(null, ['cantSplit' => true]);
                $tdStyle = ['bgColor' => $bg, 'valign' => 'top'];

                // 1. NO
                $table->addCell($wNo, $tdStyle)->addText((string)$studentNo++, [
                    'name' => 'Arial',
                    'size' => 7.5,
                    'bold' => true,
                    'color' => '475569',
                ], ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 0]);

                // 2. WAKTU
                $startTime = Carbon::parse($detail->start_time)->format('H.i');
                $endTime = Carbon::parse($detail->end_time)->format('H.i');
                $table->addCell($wWaktu, $tdStyle)->addText("{$startTime} - {$endTime}", [
                    'name' => 'Arial',
                    'size' => 7.2,
                    'bold' => true,
                    'color' => '0F172A',
                ], ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 0]);

                // 3. NPM
                $npm = $detail->thesis->student->identifier ?? '-';
                $table->addCell($wNpm, $tdStyle)->addText($npm, [
                    'name' => 'Arial',
                    'size' => 7.2,
                    'bold' => true,
                    'color' => '334155',
                ], ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 0]);

                // 4. NAMA MAHASISWA
                $studentName = strtoupper($detail->thesis->student->name ?? '-');
                $table->addCell($wNama, $tdStyle)->addText($studentName, [
                    'name' => 'Arial',
                    'size' => 7.8,
                    'bold' => true,
                    'color' => '0F172A',
                ], ['alignment' => Jc::LEFT, 'spaceBefore' => 0, 'spaceAfter' => 0]);

                // 5. JUDUL SKRIPSI
                $titleText = $detail->thesis->display_title ?? $detail->thesis->title ?? '-';
                $table->addCell($wJudul, $tdStyle)->addText($titleText, [
                    'name' => 'Arial',
                    'size' => 7.2,
                    'color' => '334155',
                ], ['alignment' => Jc::LEFT, 'spaceBefore' => 0, 'spaceAfter' => 0]);

                // 6. PEMBIMBING
                $pembimbingCell = $table->addCell($wPembimbing, $tdStyle);
                $p1 = $detail->thesis->pembimbing1?->name ?? '-';
                $pembimbingCell->addText('1. ' . $p1, [
                    'name' => 'Arial',
                    'size' => 7.2,
                    'color' => '1E293B',
                ], ['spaceBefore' => 0, 'spaceAfter' => 20]);
                if ($detail->thesis->pembimbing2) {
                    $pembimbingCell->addText('2. ' . $detail->thesis->pembimbing2->name, [
                        'name' => 'Arial',
                        'size' => 7.2,
                        'color' => '1E293B',
                    ], ['spaceBefore' => 0, 'spaceAfter' => 0]);
                }

                // 7. PENGUJI
                $examinerCell = $table->addCell($wPenguji, $tdStyle);
                if ($detail->examiner1) {
                    $examinerCell->addText('1. ' . $detail->examiner1->name, [
                        'name' => 'Arial',
                        'size' => 7.2,
                        'color' => '1E293B',
                    ], ['spaceBefore' => 0, 'spaceAfter' => 20]);
                }
                if ($detail->examiner2) {
                    $examinerCell->addText('2. ' . $detail->examiner2->name, [
                        'name' => 'Arial',
                        'size' => 7.2,
                        'color' => '1E293B',
                    ], ['spaceBefore' => 0, 'spaceAfter' => 0]);
                }
                if (!$detail->examiner1 && !$detail->examiner2) {
                    $examinerCell->addText('-', [
                        'name' => 'Arial',
                        'size' => 7.2,
                        'color' => '64748B',
                    ], ['spaceBefore' => 0, 'spaceAfter' => 0]);
                }
            } else {
                // Non-thesis activity row (e.g. Istirahat, Pembukaan)
                $table->addRow(null, ['cantSplit' => true]);
                $actStyle = ['bgColor' => 'F1F5F9', 'valign' => 'center'];

                $table->addCell($wNo, $actStyle)->addText('#', [
                    'name' => 'Arial',
                    'size' => 7.5,
                    'bold' => true,
                    'color' => '64748B',
                ], ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 0]);

                $startTime = Carbon::parse($detail->start_time)->format('H.i');
                $endTime = Carbon::parse($detail->end_time)->format('H.i');
                $table->addCell($wWaktu, $actStyle)->addText("{$startTime} - {$endTime}", [
                    'name' => 'Arial',
                    'size' => 7.2,
                    'bold' => true,
                    'color' => '0F172A',
                ], ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 0]);

                $mergedWidth = $totalWidth - ($wNo + $wWaktu);
                $table->addCell($mergedWidth, array_merge($actStyle, ['gridSpan' => 5]))->addText(strtoupper($detail->activity_name), [
                    'name' => 'Arial',
                    'size' => 7.5,
                    'bold' => true,
                    'color' => '334155',
                ], ['alignment' => Jc::LEFT, 'spaceBefore' => 20, 'spaceAfter' => 20]);
            }
        }
    }

    /**
     * Render Footer information and Kaprodi signature block.
     */
    protected function addFooterAndSignature($section, $schedule, ?User $kaprodi, int $totalWidth, array &$tempImages): void
    {
        $section->addTextBreak(1, ['size' => 6]);

        $footerTable = $section->addTable([
            'cellMarginTop' => 40,
            'cellMarginBottom' => 40,
            'cellMarginLeft' => 60,
            'cellMarginRight' => 60,
        ]);

        $footerTable->addRow(null, ['cantSplit' => true]);

        $sysWidth = 10477;
        $sigWidth = $totalWidth - $sysWidth;

        $sysCell = $footerTable->addCell($sysWidth, ['valign' => 'top']);
        $sigCell = $footerTable->addCell($sigWidth, ['valign' => 'top']);

        // System info on the left
        $sysCell->addText('Sistem Informasi Bimbingan Mahasiswa (SIBIMA)', [
            'name' => 'Arial',
            'size' => 7,
            'bold' => true,
            'color' => '64748B',
        ], ['spaceBefore' => 0, 'spaceAfter' => 10]);

        $sysCell->addText('Fakultas Ilmu Komputer - Universitas Subang', [
            'name' => 'Arial',
            'size' => 7,
            'color' => '64748B',
        ], ['spaceBefore' => 0, 'spaceAfter' => 10]);

        $printTime = now()->locale('id')->translatedFormat('d F Y, H:i');
        $sysCell->addText("Dicetak secara otomatis pada: {$printTime} WIB", [
            'name' => 'Arial',
            'size' => 7,
            'color' => '64748B',
        ], ['spaceBefore' => 0, 'spaceAfter' => 0]);

        // Signature on the right
        $scheduleDateFormatted = Carbon::parse($schedule->date)->locale('id')->translatedFormat('d F Y');
        $sigCell->addText("Subang, {$scheduleDateFormatted}", [
            'name' => 'Arial',
            'size' => 8.5,
            'color' => '0F172A',
        ], ['spaceBefore' => 0, 'spaceAfter' => 15]);

        $sigCell->addText("Mengetahui,\nKetua Program Studi", [
            'name' => 'Arial',
            'size' => 8.5,
            'bold' => true,
            'color' => '0F172A',
        ], ['spaceBefore' => 0, 'spaceAfter' => 30]);

        // Handle signature image, QR code, or blank space
        $sigRendered = false;

        if ($kaprodi && $kaprodi->decrypted_signature) {
            $rawSignature = $kaprodi->decrypted_signature;
            if (preg_match('/^data:image\/(\w+);base64,/', $rawSignature, $matches)) {
                $ext = strtolower($matches[1]);
                $base64Data = substr($rawSignature, strpos($rawSignature, ',') + 1);
                $decoded = base64_decode($base64Data);
                if ($decoded !== false) {
                    $tempSigPath = tempnam(sys_get_temp_dir(), 'sig_') . '.' . $ext;
                    file_put_contents($tempSigPath, $decoded);
                    $tempImages[] = $tempSigPath;

                    $sigCell->addImage($tempSigPath, [
                        'height' => 50,
                        'alignment' => Jc::LEFT,
                    ]);
                    $sigRendered = true;
                }
            }
        }

        if (!$sigRendered && $kaprodi && !empty($kaprodi->signature_token)) {
            $qrPath = $this->generateQrPng($kaprodi->signature_token);
            if ($qrPath) {
                $tempImages[] = $qrPath;
                $sigCell->addImage($qrPath, [
                    'width' => 45,
                    'height' => 45,
                    'alignment' => Jc::LEFT,
                ]);
                $sigRendered = true;
            }
        }

        if (!$sigRendered) {
            $sigCell->addTextBreak(3);
        }

        $kaprodiName = $kaprodi ? $kaprodi->name : '..................................................';
        $sigCell->addText("( {$kaprodiName} )", [
            'name' => 'Arial',
            'size' => 9,
            'bold' => true,
            'underline' => 'single',
            'color' => '0F172A',
        ], ['spaceBefore' => 30, 'spaceAfter' => 10]);

        if ($kaprodi && $kaprodi->identifier && is_numeric($kaprodi->identifier)) {
            $sigCell->addText("NIDN / NIP. {$kaprodi->identifier}", [
                'name' => 'Arial',
                'size' => 7.5,
                'color' => '64748B',
            ], ['spaceBefore' => 0, 'spaceAfter' => 0]);
        }
    }

    /**
     * Generate a crisp QR code PNG using GD and BaconQrCode matrix.
     */
    protected function generateQrPng(string $signatureToken): ?string
    {
        try {
            $url = url('/verify-signature/' . $signatureToken);
            $qr = Encoder::encode($url, ErrorCorrectionLevel::M());
            $matrix = $qr->getMatrix();
            $width = $matrix->getWidth();
            $scale = 4;
            $padding = 2;
            $imgSize = ($width + $padding * 2) * $scale;

            $im = imagecreatetruecolor($imgSize, $imgSize);
            $white = imagecolorallocate($im, 255, 255, 255);
            $black = imagecolorallocate($im, 0, 0, 0);
            imagefill($im, 0, 0, $white);

            for ($y = 0; $y < $width; $y++) {
                for ($x = 0; $x < $width; $x++) {
                    if ($matrix->get($x, $y) === 1) {
                        imagefilledrectangle(
                            $im,
                            ($x + $padding) * $scale,
                            ($y + $padding) * $scale,
                            ($x + $padding + 1) * $scale - 1,
                            ($y + $padding + 1) * $scale - 1,
                            $black
                        );
                    }
                }
            }

            $tempPath = tempnam(sys_get_temp_dir(), 'qr_') . '.png';
            imagepng($im, $tempPath);


            return $tempPath;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Save PhpWord document to a temp file and return the path.
     */
    protected function saveToTempDocx(PhpWord $phpWord): string
    {
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $tempFile = tempnam(sys_get_temp_dir(), 'sibima_sched_') . '.docx';
        $writer->save($tempFile);
        return $tempFile;
    }

    /**
     * Clean up temporary image files generated during export.
     */
    protected function cleanupTempImages(array $tempImages): void
    {
        foreach ($tempImages as $path) {
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }
}

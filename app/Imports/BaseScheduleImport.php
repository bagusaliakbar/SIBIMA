<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Thesis;
use App\Models\Wave;
use App\Services\ScheduleService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

abstract class BaseScheduleImport implements ToCollection
{
    protected $waveId;
    protected $scheduleService;
    protected array $dosenCache = [];
    
    public int $schedulesCount = 0;
    public int $sessionsCount = 0;
    public int $skippedCount = 0;
    public array $skippedDetails = [];

    public function __construct($waveId = null)
    {
        $this->waveId = $waveId ?: Wave::getCurrentActive()?->id;
        $this->scheduleService = app(ScheduleService::class);
    }

    abstract protected function getScheduleModel(): string;
    abstract protected function getDetailModel(): string;
    abstract protected function getScheduleTypeLabel(): string;

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            $this->skippedDetails[] = [
                'row' => 0,
                'identifier' => '-',
                'reason' => 'File Excel kosong atau tidak memiliki baris data.'
            ];
            return;
        }

        // Map column headers dynamically or fallback to standard indices
        $headerRow = $rows->first();
        $colMap = $this->resolveColumnMapping($headerRow);

        $scheduleGroups = [];
        $rowNumber = 0;

        foreach ($rows as $index => $row) {
            $rowNumber++;

            // Skip header row
            if ($index === 0 && $this->isHeaderRow($row)) {
                continue;
            }

            // Check if row is completely empty
            $isEmpty = true;
            foreach ($row as $cell) {
                if ($cell !== null && trim((string)$cell) !== '') {
                    $isEmpty = false;
                    break;
                }
            }
            if ($isEmpty) {
                continue; // Ignore blank lines
            }

            // Extract cells based on resolved map
            $titleRaw = $this->getCellValue($row, $colMap['title']);
            $dateRaw = $this->getCellValue($row, $colMap['date']);
            $locationRaw = $this->getCellValue($row, $colMap['location']);
            $meetingLinkRaw = $this->getCellValue($row, $colMap['meeting_link']);
            $chairmanRaw = $this->getCellValue($row, $colMap['chairman']);
            $moderatorRaw = $this->getCellValue($row, $colMap['moderator']);
            $startTimeRaw = $this->getCellValue($row, $colMap['start_time']);
            $endTimeRaw = $this->getCellValue($row, $colMap['end_time']);
            $npmRaw = $this->getCellValue($row, $colMap['npm']);
            $activityRaw = $this->getCellValue($row, $colMap['activity']);
            $examiner1Raw = $this->getCellValue($row, $colMap['examiner1']);
            $examiner2Raw = $this->getCellValue($row, $colMap['examiner2']);

            // Parse Date
            $parsedDate = $this->parseDate($dateRaw);
            if (!$parsedDate) {
                $this->skippedCount++;
                $this->skippedDetails[] = [
                    'row' => $rowNumber,
                    'identifier' => $npmRaw ?: $titleRaw ?: '-',
                    'reason' => "Format tanggal '{$dateRaw}' tidak valid (Gunakan format YYYY-MM-DD)."
                ];
                continue;
            }

            // Parse Start & End Time
            $startTime = $this->parseTime($startTimeRaw);
            $endTime = $this->parseTime($endTimeRaw);
            if (!$startTime || !$endTime) {
                $this->skippedCount++;
                $this->skippedDetails[] = [
                    'row' => $rowNumber,
                    'identifier' => $npmRaw ?: $titleRaw ?: '-',
                    'reason' => "Format waktu mulai ('{$startTimeRaw}') atau selesai ('{$endTimeRaw}') tidak valid (Gunakan format HH:mm)."
                ];
                continue;
            }

            // Must have either NPM or Activity Name
            if (empty($npmRaw) && empty($activityRaw)) {
                $this->skippedCount++;
                $this->skippedDetails[] = [
                    'row' => $rowNumber,
                    'identifier' => '-',
                    'reason' => 'NPM Mahasiswa atau Kegiatan Khusus harus diisi pada baris sesi.'
                ];
                continue;
            }

            // Resolve Student & Thesis
            $thesis = null;
            $student = null;
            if (!empty($npmRaw)) {
                $resolved = $this->findStudentAndThesis($npmRaw);
                $student = $resolved['student'];
                $thesis = $resolved['thesis'];

                if (!$student) {
                    $this->skippedCount++;
                    $this->skippedDetails[] = [
                        'row' => $rowNumber,
                        'identifier' => $npmRaw,
                        'reason' => "Mahasiswa dengan NPM '{$npmRaw}' tidak ditemukan di sistem."
                    ];
                    continue;
                }

                if (!$thesis) {
                    $this->skippedCount++;
                    $this->skippedDetails[] = [
                        'row' => $rowNumber,
                        'identifier' => $npmRaw,
                        'reason' => "Mahasiswa {$student->name} ({$npmRaw}) belum memiliki data skripsi di sistem."
                    ];
                    continue;
                }

                // Check if student already scheduled in this schedule type
                $detailModel = $this->getDetailModel();
                $alreadyScheduled = $detailModel::where('thesis_id', $thesis->id)->exists();
                if ($alreadyScheduled) {
                    $this->skippedCount++;
                    $this->skippedDetails[] = [
                        'row' => $rowNumber,
                        'identifier' => $npmRaw,
                        'reason' => "Mahasiswa {$student->name} ({$npmRaw}) sudah memiliki jadwal {$this->getScheduleTypeLabel()} di sistem."
                    ];
                    continue;
                }
            }

            // Resolve Examiners, Chairman, Moderator
            $chairman = $this->findDosen($chairmanRaw);
            $moderator = $this->findDosen($moderatorRaw);
            $examiner1 = $this->findDosen($examiner1Raw);
            $examiner2 = $this->findDosen($examiner2Raw);

            // Grouping Key: title + date + location
            $title = !empty($titleRaw) ? trim($titleRaw) : "Jadwal " . ucfirst($this->getScheduleTypeLabel()) . " " . Carbon::parse($parsedDate)->format('d/m/Y');
            $location = !empty($locationRaw) ? trim($locationRaw) : "Ruang Sidang";

            $groupKey = strtolower(trim($title)) . '___' . $parsedDate . '___' . strtolower(trim($location));

            if (!isset($scheduleGroups[$groupKey])) {
                $scheduleGroups[$groupKey] = [
                    'title' => $title,
                    'date' => $parsedDate,
                    'location' => $location,
                    'meeting_link' => !empty($meetingLinkRaw) ? trim($meetingLinkRaw) : null,
                    'chairman_id' => $chairman?->id,
                    'moderator_id' => $moderator?->id,
                    'sessions' => [],
                ];
            } else {
                // Update chairman / moderator if previously empty
                if (!$scheduleGroups[$groupKey]['chairman_id'] && $chairman) {
                    $scheduleGroups[$groupKey]['chairman_id'] = $chairman->id;
                }
                if (!$scheduleGroups[$groupKey]['moderator_id'] && $moderator) {
                    $scheduleGroups[$groupKey]['moderator_id'] = $moderator->id;
                }
                if (!$scheduleGroups[$groupKey]['meeting_link'] && !empty($meetingLinkRaw)) {
                    $scheduleGroups[$groupKey]['meeting_link'] = trim($meetingLinkRaw);
                }
            }

            $scheduleGroups[$groupKey]['sessions'][] = [
                'row_number' => $rowNumber,
                'thesis_id' => $thesis?->id,
                'activity_name' => $thesis ? null : (!empty($activityRaw) ? trim($activityRaw) : 'Sesi Ujian'),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'examiner1_id' => $examiner1?->id,
                'examiner2_id' => $examiner2?->id,
            ];
        }

        // Persist grouped schedules to database
        $this->persistSchedules($scheduleGroups);
    }

    protected function persistSchedules(array $scheduleGroups)
    {
        $scheduleModel = $this->getScheduleModel();
        $detailModel = $this->getDetailModel();

        foreach ($scheduleGroups as $group) {
            if (empty($group['sessions'])) {
                continue;
            }

            try {
                $schedule = $this->scheduleService->storeSchedule(
                    $scheduleModel,
                    $detailModel,
                    [
                        'title' => $group['title'],
                        'date' => $group['date'],
                        'chairman_id' => $group['chairman_id'],
                        'moderator_id' => $group['moderator_id'],
                        'location' => $group['location'],
                        'meeting_link' => $group['meeting_link'],
                        'details' => $group['sessions'],
                    ],
                    $this->waveId
                );

                $this->schedulesCount++;
                $this->sessionsCount += count($group['sessions']);
            } catch (\Throwable $e) {
                $this->skippedCount += count($group['sessions']);
                $this->skippedDetails[] = [
                    'row' => '-',
                    'identifier' => $group['title'],
                    'reason' => 'Gagal menyimpan jadwal ke database: ' . $e->getMessage()
                ];
            }
        }
    }

    protected function isHeaderRow($row): bool
    {
        $firstCell = strtolower(trim((string)($row[0] ?? '')));
        return str_contains($firstCell, 'sesi') 
            || str_contains($firstCell, 'judul') 
            || str_contains($firstCell, 'nama') 
            || str_contains($firstCell, 'tanggal')
            || str_contains($firstCell, 'tgl');
    }

    protected function resolveColumnMapping($row): array
    {
        $map = [
            'title' => 0,
            'date' => 1,
            'location' => 2,
            'meeting_link' => 3,
            'chairman' => 4,
            'moderator' => 5,
            'start_time' => 6,
            'end_time' => 7,
            'npm' => 8,
            'activity' => 9,
            'examiner1' => 10,
            'examiner2' => 11,
        ];

        if (!$row) return $map;

        foreach ($row as $colIdx => $headerVal) {
            if ($headerVal === null) continue;
            $h = strtolower(trim((string)$headerVal));

            if (str_contains($h, 'sesi') || str_contains($h, 'judul')) $map['title'] = $colIdx;
            elseif (str_contains($h, 'tanggal') || str_contains($h, 'tgl') || str_contains($h, 'date')) $map['date'] = $colIdx;
            elseif (str_contains($h, 'lokasi') || str_contains($h, 'ruang')) $map['location'] = $colIdx;
            elseif (str_contains($h, 'tautan') || str_contains($h, 'link') || str_contains($h, 'zoom') || str_contains($h, 'meet')) $map['meeting_link'] = $colIdx;
            elseif (str_contains($h, 'ketua')) $map['chairman'] = $colIdx;
            elseif (str_contains($h, 'moderator') || str_contains($h, 'sekretaris')) $map['moderator'] = $colIdx;
            elseif (str_contains($h, 'mulai') || str_contains($h, 'start')) $map['start_time'] = $colIdx;
            elseif (str_contains($h, 'selesai') || str_contains($h, 'end') || str_contains($h, 'akhir')) $map['end_time'] = $colIdx;
            elseif (str_contains($h, 'npm') || str_contains($h, 'nim')) $map['npm'] = $colIdx;
            elseif (str_contains($h, 'kegiatan') || str_contains($h, 'aktivitas') || str_contains($h, 'acara')) $map['activity'] = $colIdx;
            elseif (str_contains($h, 'penguji 1') || str_contains($h, 'penguji_1') || str_contains($h, 'u1')) $map['examiner1'] = $colIdx;
            elseif (str_contains($h, 'penguji 2') || str_contains($h, 'penguji_2') || str_contains($h, 'u2')) $map['examiner2'] = $colIdx;
        }

        return $map;
    }

    protected function getCellValue($row, $index): string
    {
        if (!isset($row[$index])) return '';
        return trim((string)$row[$index]);
    }

    protected function parseDate($value): ?string
    {
        if ($value === null || trim((string)$value) === '') return null;
        $trimmed = trim((string)$value);

        if (is_numeric($trimmed)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $trimmed)->format('Y-m-d');
            } catch (\Throwable $e) {}
        }

        try {
            if (preg_match('/^(\d{1,2})[\/\.-](\d{1,2})[\/\.-](\d{4})$/', $trimmed, $m)) {
                return Carbon::createFromDate($m[3], $m[2], $m[1])->format('Y-m-d');
            }
            return Carbon::parse($trimmed)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function parseTime($value): ?string
    {
        if ($value === null || trim((string)$value) === '') return null;
        $trimmed = trim((string)$value);

        if (is_numeric($trimmed) && (float) $trimmed < 1.0) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $trimmed)->format('H:i');
            } catch (\Throwable $e) {}
        }

        $trimmed = str_replace('.', ':', $trimmed);
        if (preg_match('/^(\d{1,2}):(\d{2})/', $trimmed, $m)) {
            $hour = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            return $hour . ':' . $m[2];
        }

        try {
            return Carbon::parse($trimmed)->format('H:i');
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function findDosen($value): ?User
    {
        if (empty($value)) return null;
        $trimmed = trim((string)$value);
        if ($trimmed === '' || $trimmed === '-') return null;

        if (isset($this->dosenCache[$trimmed])) {
            return $this->dosenCache[$trimmed];
        }

        // 1. By NIDN / identifier
        $dosen = User::where('role', 'dosen')->where('identifier', $trimmed)->first();

        // 2. By exact name (case-insensitive)
        if (!$dosen) {
            $dosen = User::where('role', 'dosen')->whereRaw('LOWER(name) = ?', [strtolower($trimmed)])->first();
        }

        // 3. By cleaned name without academic titles
        if (!$dosen) {
            $clean = preg_replace('/\b(dr|prof|ir|m\.kom|s\.kom|m\.t|s\.t|m\.sc|m\.pd|ph\.d|s\.pd|s\.si)\b/i', '', $trimmed);
            $clean = trim(preg_replace('/[,\.]/', ' ', $clean));
            if (strlen($clean) >= 3) {
                $dosen = User::where('role', 'dosen')->where('name', 'like', '%' . $clean . '%')->first();
            }
        }

        $this->dosenCache[$trimmed] = $dosen;
        return $dosen;
    }

    protected function findStudentAndThesis($npm): array
    {
        if (empty($npm)) return ['student' => null, 'thesis' => null];
        $trimmed = trim((string)$npm);
        if ($trimmed === '' || $trimmed === '-') return ['student' => null, 'thesis' => null];

        // 1. By NPM identifier
        $student = User::where('role', 'mahasiswa')->where('identifier', $trimmed)->first();

        // 2. By Name fallback
        if (!$student) {
            $student = User::where('role', 'mahasiswa')->where('name', 'like', '%' . $trimmed . '%')->first();
        }

        if (!$student) {
            return ['student' => null, 'thesis' => null];
        }

        $thesis = Thesis::where('student_id', $student->id)->latest()->first();
        return ['student' => $student, 'thesis' => $thesis];
    }
}

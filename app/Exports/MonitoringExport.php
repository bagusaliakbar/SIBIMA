<?php

namespace App\Exports;

use App\Models\Thesis;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\SeminarScheduleDetail;
use App\Models\ThesisDefenseScheduleDetail;
use App\Models\Wave;
use App\Services\MonitoringService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MonitoringExport extends DefaultValueBinder implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithCustomStartCell, 
    WithColumnWidths, 
    WithStyles, 
    WithTitle, 
    WithEvents, 
    WithCustomValueBinder
{
    protected string $type;
    protected $startDate;
    protected $endDate;
    protected $accStatus;
    protected $pembimbingId;
    protected $entryYear;
    protected $search;
    protected $graduationStatus;
    protected $waveId;
    protected int $rowNumber = 0;
    protected int $totalCount = 0;

    public function __construct(
        $type = 'akademik', 
        $startDate = null, 
        $endDate = null, 
        $accStatus = 'all', 
        $pembimbingId = null, 
        $entryYear = null, 
        $search = null,
        $graduationStatus = 'active',
        $waveId = null
    ) {
        $this->type = $type ?? 'akademik';
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->accStatus = $accStatus ?? 'all';
        $this->pembimbingId = $pembimbingId;
        $this->entryYear = $entryYear;
        $this->search = $search;
        $this->graduationStatus = $graduationStatus ?? 'active';
        $this->waveId = $waveId;
    }

    public function collection()
    {
        // 1. Beban Kinerja Dosen Komprehensif (Bimbingan P1/P2 + Menguji Seminar + Menguji Sidang)
        if ($this->type === 'dosen' || $this->type === 'beban_dosen') {
            $query = User::where('role', 'dosen')->withCount(['thesesAsP1', 'thesesAsP2']);
            if ($this->pembimbingId) {
                $query->where('id', $this->pembimbingId);
            }
            if ($this->search) {
                $query->where('name', 'like', "%{$this->search}%");
            }
            $dosens = $query->orderBy('name')->get();

            // Load counts of examiner roles
            $dosens->each(function($dosen) {
                $seminarQuery = SeminarScheduleDetail::where(function($q) use ($dosen) {
                    $q->where('examiner1_id', $dosen->id)->orWhere('examiner2_id', $dosen->id);
                });
                if ($this->waveId) {
                    $seminarQuery->whereHas('schedule', fn($q) => $q->where('wave_id', $this->waveId));
                }
                if ($this->startDate && $this->endDate) {
                    $seminarQuery->whereBetween('created_at', [$this->startDate, $this->endDate]);
                }
                $dosen->seminar_examiner_count = $seminarQuery->count();

                $defenseQuery = ThesisDefenseScheduleDetail::where(function($q) use ($dosen) {
                    $q->where('examiner1_id', $dosen->id)->orWhere('examiner2_id', $dosen->id);
                });
                if ($this->waveId) {
                    $defenseQuery->whereHas('schedule', fn($q) => $q->where('wave_id', $this->waveId));
                }
                if ($this->startDate && $this->endDate) {
                    $defenseQuery->whereBetween('created_at', [$this->startDate, $this->endDate]);
                }
                $dosen->defense_examiner_count = $defenseQuery->count();

                $dosen->total_workload = ($dosen->theses_as_p1_count ?? 0) 
                    + ($dosen->theses_as_p2_count ?? 0) 
                    + $dosen->seminar_examiner_count 
                    + $dosen->defense_examiner_count;
            });

            $this->totalCount = $dosens->count();
            return $dosens;
        }

        // 2. Rekap Nilai Sidang & Yudisium
        if ($this->type === 'nilai_sidang') {
            $query = ThesisDefenseScheduleDetail::with([
                'thesis.student', 
                'thesis.pembimbing1', 
                'thesis.pembimbing2', 
                'schedule.wave', 
                'examiner1', 
                'examiner2', 
                'revisions'
            ])->whereHas('thesis');

            if ($this->waveId) {
                $query->whereHas('schedule', fn($q) => $q->where('wave_id', $this->waveId));
            }
            if ($this->entryYear) {
                $query->whereHas('thesis.student', fn($q) => $q->where('entry_year', $this->entryYear));
            }
            if ($this->pembimbingId) {
                $query->whereHas('thesis', function($q) {
                    $q->where('pembimbing1_id', $this->pembimbingId)->orWhere('pembimbing2_id', $this->pembimbingId);
                });
            }
            if ($this->startDate && $this->endDate) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
            }
            if ($this->search) {
                $query->whereHas('thesis.student', function($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('identifier', 'like', "%{$this->search}%");
                });
            }

            $res = $query->orderBy('id', 'desc')->get();
            $this->totalCount = $res->count();
            return $res;
        }

        // 3. Kepatuhan Bimbingan & Early Warning Pasif
        if ($this->type === 'kepatuhan') {
            $query = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
                ->where('status', 'active')
                ->withMentoringCounts();

            if ($this->pembimbingId) {
                $query->where(function($q) {
                    $q->where('pembimbing1_id', $this->pembimbingId)->orWhere('pembimbing2_id', $this->pembimbingId);
                });
            }
            if ($this->entryYear) {
                $query->whereHas('student', fn($q) => $q->where('entry_year', $this->entryYear));
            }
            if ($this->search) {
                $query->search($this->search);
            }

            $res = $query->get()->sortByDesc(fn($t) => $t->days_since_last_mentoring ?? 0)->values();
            $this->totalCount = $res->count();
            return $res;
        }

        // 4. Distribusi Topik & Bidang Peminatan Skripsi
        if ($this->type === 'topik') {
            $query = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])->whereNotNull('title');

            if ($this->graduationStatus === 'graduated') {
                $query->graduated();
            } elseif ($this->graduationStatus === 'active') {
                $query->notGraduated();
            }
            if ($this->entryYear) {
                $query->whereHas('student', fn($q) => $q->where('entry_year', $this->entryYear));
            }
            if ($this->pembimbingId) {
                $query->where(function($q) {
                    $q->where('pembimbing1_id', $this->pembimbingId)->orWhere('pembimbing2_id', $this->pembimbingId);
                });
            }
            if ($this->startDate && $this->endDate) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
            }
            if ($this->search) {
                $query->search($this->search);
            }

            $res = $query->orderBy('topic')->orderBy('created_at', 'desc')->get();
            $this->totalCount = $res->count();
            return $res;
        }

        // 5. Analisis Waktu & Durasi Masa Studi
        if ($this->type === 'waktu') {
            $waves = Wave::with(['defenseApplications.thesis' => function($q) {
                $q->where('status', 'completed');
            }])->get();

            $list = collect();
            foreach ($waves as $wave) {
                $completed = $wave->defenseApplications->map(fn($app) => $app->thesis)->filter(fn($t) => $t && $t->status === 'completed');
                $count = $completed->count();
                $avgMonths = 0;
                $onTimeCount = 0;
                $lateCount = 0;

                if ($count > 0) {
                    $totalDays = $completed->sum(fn($t) => $t->created_at->diffInDays($t->updated_at));
                    $avgMonths = round(($totalDays / $count) / 30, 1);
                    foreach ($completed as $t) {
                        if ($t->student && $t->student->entry_year) {
                            ($t->updated_at->year - $t->student->entry_year <= 4) ? $onTimeCount++ : $lateCount++;
                        }
                    }
                }

                $list->push((object)[
                    'wave_name' => $wave->name,
                    'period' => ($wave->start_date?->format('d/m/Y') ?? '-') . ' s/d ' . ($wave->end_date?->format('d/m/Y') ?? '-'),
                    'completed_count' => $count,
                    'avg_duration_months' => $avgMonths,
                    'on_time_count' => $onTimeCount,
                    'late_count' => $lateCount,
                    'on_time_percent' => $count > 0 ? round(($onTimeCount / $count) * 100, 1) . '%' : '0%',
                ]);
            }

            $this->totalCount = $list->count();
            return $list;
        }

        // 6. Audit Log Aktivitas
        if ($this->type === 'logs') {
            $query = ActivityLog::with('user')
                ->when($this->startDate && $this->endDate, function($q) {
                    $q->whereBetween('created_at', [$this->startDate, $this->endDate]);
                })
                ->when($this->search, function($q) {
                    $q->where('description', 'like', "%{$this->search}%");
                });
            $res = $query->latest()->get();
            $this->totalCount = $res->count();
            return $res;
        }

        // 7. Monitoring Theses (akademik, mahasiswa, kelulusan)
        $query = Thesis::with(['student', 'pembimbing1', 'pembimbing2', 'graduation'])
            ->withMentoringCounts();

        if ($this->type === 'kelulusan' || $this->graduationStatus === 'graduated') {
            $query->graduated();
        } elseif ($this->graduationStatus === 'active') {
            $query->notGraduated();
        }

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }

        if (!empty($this->pembimbingId)) {
            $query->where(function($q) {
                $q->where('pembimbing1_id', $this->pembimbingId)
                  ->orWhere('pembimbing2_id', $this->pembimbingId);
            });
        }

        if (!empty($this->entryYear)) {
            $query->whereHas('student', function($q) {
                $q->where('entry_year', $this->entryYear);
            });
        }

        if (!empty($this->accStatus) && $this->accStatus !== 'all') {
            $query->accStatus($this->accStatus);
        }

        if (!empty($this->search)) {
            $query->search($this->search);
        }

        $res = $query->orderBy('created_at', 'desc')->get();
        $this->totalCount = $res->count();
        return $res;
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function title(): string
    {
        return match($this->type) {
            'dosen', 'beban_dosen' => 'Beban Kinerja Dosen',
            'nilai_sidang' => 'Rekap Nilai Sidang',
            'kepatuhan' => 'Kepatuhan Bimbingan',
            'topik' => 'Distribusi Topik',
            'waktu' => 'Analisis Waktu & Durasi',
            'logs' => 'Log Aktivitas',
            'kelulusan' => 'Data Kelulusan',
            default => match($this->graduationStatus) {
                'graduated' => 'Mahasiswa Lulus',
                'all' => 'Semua Mahasiswa',
                default => 'Mahasiswa Aktif',
            }
        };
    }

    public function headings(): array
    {
        switch ($this->type) {
            case 'dosen':
            case 'beban_dosen':
                return [
                    'NO', 
                    'NAMA DOSEN', 
                    'NIP / NIDN', 
                    'PEMBIMBING 1', 
                    'PEMBIMBING 2', 
                    'PENGUJI SEMINAR', 
                    'PENGUJI SIDANG', 
                    'TOTAL BEBAN KINERJA'
                ];
            case 'nilai_sidang':
                return [
                    'NO',
                    'NPM',
                    'NAMA MAHASISWA',
                    'ANGKATAN',
                    'JUDUL SKRIPSI',
                    'TIM PENGUJI & PEMBIMBING',
                    'NILAI P1',
                    'NILAI PENGUJI 1',
                    'NILAI PENGUJI 2',
                    'TOTAL NILAI',
                    'NILAI AKHIR (ANGKA)',
                    'NILAI AKHIR (HURUF)',
                    'STATUS KELULUSAN'
                ];
            case 'kepatuhan':
                return [
                    'NO',
                    'NPM',
                    'NAMA MAHASISWA',
                    'ANGKATAN',
                    'PEMBIMBING 1',
                    'PEMBIMBING 2',
                    'SESI P1',
                    'SESI P2',
                    'TOTAL SESI',
                    'HARI SEJAK BIMBINGAN TERAKHIR',
                    'STATUS RISIKO KEAKTIFAN',
                    'KESIAPAN KUOTA SEMINAR/SIDANG',
                    'NO. WHATSAPP'
                ];
            case 'topik':
                return [
                    'NO',
                    'NPM',
                    'NAMA MAHASISWA',
                    'ANGKATAN',
                    'BIDANG / TOPIK PEMINATAN',
                    'JUDUL SKRIPSI',
                    'PEMBIMBING 1',
                    'PEMBIMBING 2',
                    'STATUS SKRIPSI'
                ];
            case 'waktu':
                return [
                    'NO',
                    'NAMA GELOMBANG',
                    'PERIODE TANGGAL',
                    'TOTAL MAHASISWA LULUS',
                    'RATA-RATA DURASI (BULAN)',
                    'LULUS TEPAT WAKTU (<=4 THN)',
                    'LULUS TERLAMBAT (>4 THN)',
                    'PERSENTASE KELULUSAN TEPAT WAKTU'
                ];
            case 'logs':
                return ['NO', 'WAKTU', 'PENGGUNA', 'AKTIVITAS', 'DESKRIPSI'];
            case 'kelulusan':
                return [
                    'NO', 
                    'NPM', 
                    'NAMA MAHASISWA', 
                    'ANGKATAN', 
                    'JUDUL SKRIPSI', 
                    'PEMBIMBING 1', 
                    'PEMBIMBING 2', 
                    'TANGGAL SELESAI', 
                    'MASA STUDI (THN)', 
                    'STATUS KELULUSAN'
                ];
            default:
                return [
                    'NO',
                    'NPM',
                    'NAMA MAHASISWA',
                    'ANGKATAN',
                    'JUDUL SKRIPSI',
                    'PEMBIMBING 1',
                    'PEMBIMBING 2',
                    'SESI P1',
                    'SESI P2',
                    'TOTAL SESI',
                    'STATUS ACC SEMINAR (UP)',
                    'STATUS ACC SIDANG',
                    'STATUS KELULUSAN',
                    'STATUS BIMBINGAN',
                ];
        }
    }

    public function map($item): array
    {
        $this->rowNumber++;

        // 1. Beban Dosen
        if ($this->type === 'dosen' || $this->type === 'beban_dosen') {
            return [
                $this->rowNumber,
                $item->name,
                $item->identifier ?? '-',
                $item->theses_as_p1_count ?? 0,
                $item->theses_as_p2_count ?? 0,
                $item->seminar_examiner_count ?? 0,
                $item->defense_examiner_count ?? 0,
                $item->total_workload ?? 0,
            ];
        }

        // 2. Nilai Sidang & Yudisium
        if ($this->type === 'nilai_sidang') {
            $monitoringService = app(MonitoringService::class);
            $scores = $monitoringService->calculateDefenseScores($item);

            $p1Name = $item->thesis->pembimbing1->name ?? '-';
            $e1Name = $item->examiner1->name ?? '-';
            $e2Name = $item->examiner2->name ?? '-';
            $teamText = "P1: {$p1Name}\nE1: {$e1Name}\nE2: {$e2Name}";

            $scoreP1 = $scores['scoreP1'] !== null ? number_format($scores['scoreP1'], 1) : '-';
            $scoreE1 = $scores['scoreE1'] !== null ? number_format($scores['scoreE1'], 1) : '-';
            $scoreE2 = $scores['scoreE2'] !== null ? number_format($scores['scoreE2'], 1) : '-';

            $rawScores = collect([$scores['scoreP1'], $scores['scoreE1'], $scores['scoreE2']])->filter(fn($s) => $s !== null);
            $totalScoreVal = $rawScores->sum();
            $totalScoreStr = $rawScores->count() > 0 ? number_format($totalScoreVal, 1) : '-';

            $finalScore = $scores['finalScore'] > 0 ? number_format($scores['finalScore'], 1) : '-';
            $finalGrade = $scores['finalGrade'] ?? '-';

            $passStatus = match(true) {
                $scores['finalScore'] >= 60 => 'LULUS (MEMENUHI SYARAT)',
                $scores['finalScore'] > 0 => 'TIDAK LULUS',
                default => 'BELUM LENGKAP DINILAI',
            };

            return [
                $this->rowNumber,
                $item->thesis->student->identifier ?? '-',
                $item->thesis->student->name ?? '-',
                $item->thesis->student->entry_year ?? '-',
                $item->thesis->final_title ?? $item->thesis->title ?? '-',
                $teamText,
                $scoreP1,
                $scoreE1,
                $scoreE2,
                $totalScoreStr,
                $finalScore,
                $finalGrade,
                $passStatus
            ];
        }

        // 3. Kepatuhan Bimbingan & Early Warning
        if ($this->type === 'kepatuhan') {
            $days = $item->days_since_last_mentoring;
            $riskLabel = match(true) {
                $days === null => 'Belum Pernah Bimbingan',
                $days > 14 => "KRITIS ({$days} Hari Pasif)",
                $days > 7 => "PERINGATAN ({$days} Hari Pasif)",
                default => "Aktif ({$days} Hari Lalu)",
            };

            $p1 = $item->sessions_p1 ?? 0;
            $p2 = $item->sessions_p2 ?? 0;
            $tot = $item->total_sessions ?? ($p1 + $p2);

            $quotaLabel = match(true) {
                $tot >= 16 => 'Lengkap (Siap Sidang >=16x)',
                $tot >= 8 => 'Memenuhi Seminar (>=8x)',
                default => "Belum Cukup ({$tot}/8x)",
            };

            return [
                $this->rowNumber,
                $item->student->identifier ?? '-',
                $item->student->name ?? '-',
                $item->student->entry_year ?? '-',
                $item->pembimbing1->name ?? 'Belum Ditugaskan',
                $item->pembimbing2->name ?? 'Belum Ditugaskan',
                $p1,
                $p2,
                $tot,
                $days !== null ? $days . ' Hari' : 'Belum Ada',
                $riskLabel,
                $quotaLabel,
                $item->student->phone_number ?? '-'
            ];
        }

        // 4. Distribusi Topik & Peminatan Skripsi
        if ($this->type === 'topik') {
            return [
                $this->rowNumber,
                $item->student->identifier ?? '-',
                $item->student->name ?? '-',
                $item->student->entry_year ?? '-',
                strtoupper($item->topic ?? 'UMUM / BELUM DITENTUKAN'),
                $item->final_title ?? $item->title ?? '-',
                $item->pembimbing1->name ?? 'Belum Ditugaskan',
                $item->pembimbing2->name ?? 'Belum Ditugaskan',
                $item->isGraduated() ? 'Lulus / Selesai' : ucfirst($item->status ?? 'Aktif')
            ];
        }

        // 5. Analisis Waktu & Durasi
        if ($this->type === 'waktu') {
            return [
                $this->rowNumber,
                $item->wave_name,
                $item->period,
                $item->completed_count,
                $item->avg_duration_months . ' Bulan',
                $item->on_time_count,
                $item->late_count,
                $item->on_time_percent
            ];
        }

        // 6. Audit Log
        if ($this->type === 'logs') {
            return [
                $this->rowNumber,
                $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-',
                $item->user?->name ?? 'System',
                strtoupper($item->activity ?? '-'),
                $item->description ?? '-',
            ];
        }

        // 7. Kelulusan
        if ($this->type === 'kelulusan') {
            $studyYears = '-';
            if ($item->student?->entry_year && $item->updated_at) {
                $studyYears = round($item->updated_at->year - $item->student->entry_year, 1);
            }

            return [
                $this->rowNumber,
                $item->student?->identifier ?? '-',
                $item->student?->name ?? '-',
                $item->student?->entry_year ?? '-',
                $item->final_title ?? $item->title ?? '-',
                $item->pembimbing1?->name ?? 'Belum Ditugaskan',
                $item->pembimbing2?->name ?? 'Belum Ditugaskan',
                $item->updated_at ? $item->updated_at->format('d/m/Y') : '-',
                $studyYears,
                'Lulus / Selesai',
            ];
        }

        // 8. Default Monitoring Akademik / Mahasiswa
        $accUpLabel = match(true) {
            $item->isAccUpFinal() => 'Siap Seminar (ACC P1 & P2)',
            ($item->acc_up_p1 && !$item->acc_up_p2) => 'ACC P1 Saja',
            (!$item->acc_up_p1 && $item->acc_up_p2) => 'ACC P2 Saja',
            default => 'Belum ACC',
        };

        $accSidangLabel = match(true) {
            $item->isAccSidangFinal() => 'Siap Sidang (ACC P1 & P2)',
            ($item->acc_sidang_p1 && !$item->acc_sidang_p2) => 'ACC P1 Saja',
            (!$item->acc_sidang_p1 && $item->acc_sidang_p2) => 'ACC P2 Saja',
            default => 'Belum ACC',
        };

        $graduationLabel = $item->isGraduated() ? 'Lulus / Selesai' : 'Aktif / Belum Lulus';

        $statusLabel = match(true) {
            $item->isGraduated() => 'Selesai',
            $item->status === 'active' => 'Bimbingan Aktif',
            $item->status === 'pending' => 'Menunggu Pembimbing',
            $item->status === 'rejected' => 'Ditolak',
            default => ucfirst($item->status ?? 'Aktif'),
        };

        $p1Sessions = $item->sessions_p1 ?? 0;
        $p2Sessions = $item->sessions_p2 ?? 0;
        $totalSessions = $item->total_sessions ?? ($p1Sessions + $p2Sessions);

        return [
            $this->rowNumber,
            $item->student?->identifier ?? '-',
            $item->student?->name ?? '-',
            $item->student?->entry_year ?? '-',
            $item->final_title ?? $item->title ?? '-',
            $item->pembimbing1?->name ?? 'Belum Ditugaskan',
            $item->pembimbing2?->name ?? 'Belum Ditugaskan',
            $p1Sessions,
            $p2Sessions,
            $totalSessions,
            $accUpLabel,
            $accSidangLabel,
            $graduationLabel,
            $statusLabel,
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        // Bind NPM / NIP / Phone explicitly as string to retain leading zeros & prevent scientific notation
        if (in_array($cell->getColumn(), ['B', 'C', 'M'])) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function columnWidths(): array
    {
        switch ($this->type) {
            case 'dosen':
            case 'beban_dosen':
                return [
                    'A' => 6,
                    'B' => 32,
                    'C' => 20,
                    'D' => 16,
                    'E' => 16,
                    'F' => 18,
                    'G' => 18,
                    'H' => 22,
                ];
            case 'nilai_sidang':
                return [
                    'A' => 6,
                    'B' => 18,
                    'C' => 30,
                    'D' => 12,
                    'E' => 45,
                    'F' => 30,
                    'G' => 12,
                    'H' => 14,
                    'I' => 14,
                    'J' => 14,
                    'K' => 18,
                    'L' => 18,
                    'M' => 24,
                ];
            case 'kepatuhan':
                return [
                    'A' => 6,
                    'B' => 18,
                    'C' => 30,
                    'D' => 12,
                    'E' => 28,
                    'F' => 28,
                    'G' => 10,
                    'H' => 10,
                    'I' => 12,
                    'J' => 24,
                    'K' => 28,
                    'L' => 28,
                    'M' => 20,
                ];
            case 'topik':
                return [
                    'A' => 6,
                    'B' => 18,
                    'C' => 30,
                    'D' => 12,
                    'E' => 28,
                    'F' => 48,
                    'G' => 28,
                    'H' => 28,
                    'I' => 18,
                ];
            case 'waktu':
                return [
                    'A' => 6,
                    'B' => 28,
                    'C' => 26,
                    'D' => 22,
                    'E' => 24,
                    'F' => 24,
                    'G' => 24,
                    'H' => 30,
                ];
            case 'logs':
                return [
                    'A' => 6,
                    'B' => 20,
                    'C' => 24,
                    'D' => 20,
                    'E' => 45,
                ];
            case 'kelulusan':
                return [
                    'A' => 6,
                    'B' => 18,
                    'C' => 32,
                    'D' => 12,
                    'E' => 48,
                    'F' => 28,
                    'G' => 28,
                    'H' => 18,
                    'I' => 18,
                    'J' => 20,
                ];
            default:
                return [
                    'A' => 6,   // NO
                    'B' => 18,  // NPM
                    'C' => 32,  // NAMA MAHASISWA
                    'D' => 12,  // ANGKATAN
                    'E' => 48,  // JUDUL SKRIPSI
                    'F' => 28,  // PEMBIMBING 1
                    'G' => 28,  // PEMBIMBING 2
                    'H' => 10,  // SESI P1
                    'I' => 10,  // SESI P2
                    'J' => 12,  // TOTAL SESI
                    'K' => 25,  // STATUS ACC UP
                    'L' => 25,  // STATUS ACC SIDANG
                    'M' => 20,  // STATUS KELULUSAN
                    'N' => 20,  // STATUS BIMBINGAN
                ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestCol = match($this->type) {
                    'dosen', 'beban_dosen', 'waktu' => 'H',
                    'nilai_sidang', 'kepatuhan' => 'M',
                    'topik', 'kelulusan' => 'J',
                    'logs' => 'E',
                    default => 'N',
                };

                $reportTitle = match($this->type) {
                    'dosen', 'beban_dosen' => 'LAPORAN BEBAN KINERJA DOSEN PEMBIMBING & PENGUJI',
                    'nilai_sidang' => 'LAPORAN REKAPITULASI NILAI SIDANG & HASIL YUDISIUM',
                    'kepatuhan' => 'LAPORAN KEPATUHAN BIMBINGAN & EARLY WARNING MAHASISWA PASIF',
                    'topik' => 'LAPORAN DISTRIBUSI TOPIK & PEMINATAN RISET SKRIPSI',
                    'waktu' => 'LAPORAN ANALISIS MASA STUDI & TREN GELOMBANG KELULUSAN',
                    'kelulusan' => 'LAPORAN REKAPITULASI KELULUSAN MAHASISWA',
                    'logs' => 'LAPORAN AUDIT LOG & AKTIVITAS SISTEM',
                    default => 'LAPORAN MONITORING PROGRES BIMBINGAN SKRIPSI MAHASISWA',
                };

                // Title Banner (Row 1)
                $sheet->mergeCells("A1:{$highestCol}1");
                $sheet->setCellValue('A1', $reportTitle);
                $sheet->getRowDimension(1)->setRowHeight(32);
                $sheet->getStyle("A1:{$highestCol}1")->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'bold' => true,
                        'size' => 13,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E293B'], // Slate-800
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Subtitle (Row 2)
                $sheet->mergeCells("A2:{$highestCol}2");
                $sheet->setCellValue('A2', 'SISTEM INFORMASI BIMBINGAN MAHASISWA (SIBIMA) - FAKULTAS ILMU KOMPUTER - UNIVERSITAS SUBANG');
                $sheet->getRowDimension(2)->setRowHeight(18);
                $sheet->getStyle("A2:{$highestCol}2")->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'bold' => true,
                        'size' => 8.5,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E293B'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Metadata Bar (Row 3)
                $sheet->mergeCells("A3:{$highestCol}3");
                $pembimbingName = 'Semua Pembimbing';
                if (!empty($this->pembimbingId)) {
                    $d = User::find($this->pembimbingId);
                    if ($d) $pembimbingName = $d->name;
                }

                $entryYearLabel = !empty($this->entryYear) ? 'Angkatan ' . $this->entryYear : 'Semua Angkatan';
                $waveName = 'Semua Gelombang';
                if (!empty($this->waveId)) {
                    $w = Wave::find($this->waveId);
                    if ($w) $waveName = $w->name;
                }

                $exportDate = now()->translatedFormat('d F Y, H:i') . ' WIB';
                $metaText = "Gelombang: {$waveName}   |   Dosen: {$pembimbingName}   |   Angkatan: {$entryYearLabel}   |   Total Data: {$this->totalCount}   |   Waktu Unduh: {$exportDate}";
                
                $sheet->setCellValue('A3', $metaText);
                $sheet->getRowDimension(3)->setRowHeight(22);
                $sheet->getStyle("A3:{$highestCol}3")->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'size' => 8.5,
                        'color' => ['rgb' => '475569'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F1F5F9'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CBD5E1'],
                        ],
                    ],
                ]);

                // Row 4: Spacer
                $sheet->getRowDimension(4)->setRowHeight(10);

                // Table Header (Row 5)
                $sheet->getRowDimension(5)->setRowHeight(28);
                $sheet->getStyle("A5:{$highestCol}5")->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'bold' => true,
                        'size' => 9.5,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '0F172A'], // Slate-900 / Navy
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '334155'],
                        ],
                    ],
                ]);

                // Data Rows (Row 6 to highestRow)
                if ($highestRow >= 6) {
                    $sheet->getStyle("A6:{$highestCol}{$highestRow}")->applyFromArray([
                        'font' => [
                            'name' => 'Segoe UI',
                            'size' => 9.5,
                            'color' => ['rgb' => '1E293B'],
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'E2E8F0'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    // Center column A (NO)
                    $sheet->getStyle("A6:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Row Heights & Zebra striping
                    for ($row = 6; $row <= $highestRow; $row++) {
                        $sheet->getRowDimension($row)->setRowHeight(30);
                        if ($row % 2 === 1) {
                            $sheet->getStyle("A{$row}:{$highestCol}{$row}")->getFill()->applyFromArray([
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8FAFC'], // Slate-50
                            ]);
                        }
                    }

                    // AutoFilter on Table Header
                    $sheet->setAutoFilter("A5:{$highestCol}{$highestRow}");
                }

                // Footer Note
                $footerRow = max($highestRow + 2, 7);
                $sheet->mergeCells("A{$footerRow}:{$highestCol}{$footerRow}");
                $sheet->setCellValue("A{$footerRow}", "* Dokumen ini diunduh secara resmi melalui Sistem Informasi SIBIMA pada " . now()->format('d/m/Y H:i:s') . ".");
                $sheet->getStyle("A{$footerRow}")->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'italic' => true,
                        'size' => 8,
                        'color' => ['rgb' => '94A3B8'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Freeze Pane below Table Header
                $sheet->freezePane('A6');

                // Enable Gridlines
                $sheet->setShowGridLines(true);
            },
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThesisRepository extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'name',
        'year',
        'title',
        'abstract',
        'pembimbing1',
        'pembimbing2',
        'file_path',
        'file_path_bab2',
        'file_path_bab3',
        'file_path_bab4',
        'file_path_bab5',
        'file_path_bab6',
    ];

    /**
     * Bootstrap model events.
     */
    protected static function booted()
    {
        static::saving(function ($repo) {
            if (!empty($repo->identifier)) {
                $extracted = self::extractYearFromIdentifier($repo->identifier);
                if ($extracted) {
                    if (empty($repo->year) || $repo->isDirty('identifier')) {
                        $repo->year = $extracted;
                    }
                }
            }
        });
    }

    /**
     * Ekstrak tahun angkatan mahasiswa secara akurat dari NPM / Identifier.
     * Contoh:
     * - D1A170017 / D1A.17.0037 => 2017
     * - D1A180004 => 2018
     * - D1A200022 => 2020
     */
    public static function extractYearFromIdentifier(?string $identifier): ?int
    {
        if (empty($identifier)) {
            return null;
        }

        $id = trim($identifier);

        // 1. Pola 4 digit tahun eksplisit (misal IF2021001, 20180123)
        if (preg_match('/(?:^|[A-Za-z\.\-_\s])(19[7-9]\d|20[0-3]\d)(?:[A-Za-z\.\-_\s]|\d{3,}|$)/', $id, $m)) {
            return (int) $m[1];
        }

        // 2. Pola standar UNSUB (D1A, D1B) atau kode huruf prodi (IF, SI, TI) diikuti 2 digit angkatan
        if (preg_match('/^(?:[A-Za-z]\d[A-Za-z]|[A-Za-z]{2,5})[\.\-_\s]*(\d{2})[\.\-_\s]*\d+/i', $id, $m)) {
            $yy = (int) $m[1];
            return ($yy >= 70) ? (1900 + $yy) : (2000 + $yy);
        }

        // 3. Pola numerik murni: 2 digit angkatan di awal (misal 211011003 -> 21 => 2021)
        if (preg_match('/^(\d{2})\d{4,}/', $id, $m)) {
            $yy = (int) $m[1];
            if ($yy >= 10 && $yy <= 35) {
                return 2000 + $yy;
            }
        }

        return null;
    }

    /**
     * Topic definitions with regex word boundaries and matching keywords.
     */
    public static function getTopicDefinitions(): array
    {
        return [
            'ai' => [
                'label' => 'AI & Data Science',
                'color' => 'purple',
                'bg' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/70 dark:text-purple-300 border-purple-200 dark:border-purple-800/60',
                'weight' => 20,
                'pattern' => '/\b(machine learning|deep learning|data mining|klasifikasi|clustering|k-means|kmeans|naive bayes|svm|support vector machine|random forest|decision tree|c4\.5|neural network|jaringan syaraf|jst|cnn|rnn|lstm|nlp|natural language processing|text mining|sentiment|sentimen|yolo|computer vision|pengolahan citra|image processing|fuzzy|algoritma genetika|genetic algorithm|knn|k-nearest|regresi|prediksi|forecasting|backpropagation)\b/i',
                'keywords' => ['machine learning', 'deep learning', 'data mining', 'klasifikasi', 'clustering', 'k-means', 'naive bayes', 'svm', 'decision tree', 'neural network', 'cnn', 'nlp', 'yolo', 'fuzzy', 'algoritma genetika']
            ],
            'spk' => [
                'label' => 'SPK / Keputusan',
                'color' => 'amber',
                'bg' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/70 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
                'weight' => 20,
                'pattern' => '/\b(spk|dss|decision support system|pendukung keputusan|pengambil keputusan|\bahp\b|analytic hierarchy|\bsaw\b|simple additive weighting|topsis|profile matching|moora|vikor|mabac|promethee|electre|copras|weighted product|\bwp\b|aras|edas|roc|rank order centroid|metode smart)\b/i',
                'keywords' => ['spk', 'pendukung keputusan', 'ahp', 'saw', 'topsis', 'profile matching', 'moora', 'vikor', 'mabac', 'promethee', 'electre', 'copras', 'weighted product']
            ],
            'iot' => [
                'label' => 'IoT & Hardware',
                'color' => 'sky',
                'bg' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/70 dark:text-sky-300 border-sky-200 dark:border-sky-800/60',
                'weight' => 18,
                'pattern' => '/\b(iot|internet of things|arduino|raspberry|esp8266|esp32|mikrokontroler|microcontroller|sensor|rfid|lora|smart home|smart farming|jaringan|mikrotik|cisco|routing|firewall|intrusion detection|ids|vpn)\b/i',
                'keywords' => ['iot', 'internet of things', 'arduino', 'raspberry', 'sensor', 'mikrokontroler', 'jaringan', 'mikrotik', 'cisco', 'keamanan']
            ],
            'ui_ux' => [
                'label' => 'UI/UX & HCD',
                'color' => 'rose',
                'bg' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/70 dark:text-rose-300 border-rose-200 dark:border-rose-800/60',
                'weight' => 16,
                'pattern' => '/\b(ui\/ux|ui\\\\ux|user interface|user experience|human-centered|human centered|hcd|design thinking|usability|heuristic evaluation|sus|system usability scale|user persona|wireframe|prototyping|user-centered)\b/i',
                'keywords' => ['ui/ux', 'user interface', 'user experience', 'human-centered', 'design thinking', 'usability', 'user persona', 'prototyping']
            ],
            'mobile' => [
                'label' => 'Mobile App',
                'color' => 'emerald',
                'bg' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',
                'weight' => 15,
                'pattern' => '/\b(android|mobile|flutter|react native|kotlin|swift|ios|smartphone|telepon pintar|perangkat bergerak)\b/i',
                'keywords' => ['android', 'mobile', 'flutter', 'react native', 'kotlin', 'swift', 'ios', 'smartphone']
            ],
            'ecommerce' => [
                'label' => 'E-Commerce / POS',
                'color' => 'blue',
                'bg' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/70 dark:text-blue-300 border-blue-200 dark:border-blue-800/60',
                'weight' => 14,
                'pattern' => '/\b(e-commerce|ecommerce|e-bisnis|ebusiness|marketplace|toko online|point of sale|\bpos\b|kasir|penjualan online|pemesanan makanan|reservasi tiket)\b/i',
                'keywords' => ['e-commerce', 'ecommerce', 'marketplace', 'toko online', 'point of sale', 'pos', 'kasir', 'penjualan online']
            ],
            'web' => [
                'label' => 'Web App & SI',
                'color' => 'indigo',
                'bg' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/70 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800/60',
                'weight' => 10,
                'pattern' => '/\b(web|website|portal|sistem informasi|aplikasi berbasis web|web-based|dashboard|inventaris|kepegawaian|akademik|arsip|pendaftaran|peminjaman|e-office|surat menyurat|pelayanan|monitoring)\b/i',
                'keywords' => ['web', 'website', 'portal', 'sistem informasi', 'aplikasi web', 'dashboard', 'monitoring', 'inventaris', 'kepegawaian']
            ],
        ];
    }

    /**
     * Auto-detect topic categories based on title keywords with smart regex word boundaries and scoring.
     */
    public static function detectTopic(?string $title): array
    {
        $title = strtolower(trim($title ?? ''));
        $definitions = self::getTopicDefinitions();

        $bestMatch = null;
        $maxScore = 0;

        foreach ($definitions as $key => $cat) {
            $count = preg_match_all($cat['pattern'], $title, $matches);
            if ($count > 0) {
                $score = $cat['weight'] + ($count * 5);
                if ($score > $maxScore) {
                    $maxScore = $score;
                    $bestMatch = [
                        'key' => $key,
                        'label' => $cat['label'],
                        'color' => $cat['color'],
                        'bg' => $cat['bg']
                    ];
                }
            }
        }

        if ($bestMatch) {
            return $bestMatch;
        }

        return [
            'key' => 'general',
            'label' => 'Sistem Informasi',
            'color' => 'slate',
            'bg' => 'bg-slate-100 text-slate-700 dark:bg-slate-800/90 dark:text-slate-300 border-slate-200 dark:border-slate-700'
        ];
    }

    /**
     * Accessor for topic badge.
     */
    public function getTopicBadgeAttribute(): array
    {
        return self::detectTopic($this->title);
    }

    /**
     * Auto-archive a completed thesis from SIBIMA into the repository catalog.
     */
    public static function archiveFromThesis(Thesis $thesis): ?self
    {
        if (!$thesis->student) {
            return null;
        }

        $student = $thesis->student;
        $title = trim($thesis->final_title ?: $thesis->title);
        if (empty($title)) {
            return null;
        }

        // Determine graduation year
        $year = null;
        if ($thesis->relationLoaded('graduation') && $thesis->graduation && $thesis->graduation->graduation_date) {
            $year = (int) \Carbon\Carbon::parse($thesis->graduation->graduation_date)->year;
        } elseif ($thesis->graduation && $thesis->graduation->graduation_date) {
            $year = (int) \Carbon\Carbon::parse($thesis->graduation->graduation_date)->year;
        } elseif ($thesis->updated_at) {
            $year = (int) $thesis->updated_at->year;
        } elseif (!empty($student->identifier)) {
            $year = self::extractYearFromIdentifier($student->identifier);
        }
        if (!$year) {
            $year = (int) date('Y');
        }

        // Find best final thesis document (approved revision or submitted defense thesis)
        $bestFile = null;
        $latestScheduleDetail = $thesis->defenseScheduleDetails()->latest('id')->first();

        if ($latestScheduleDetail) {
            $approvedRevision = $latestScheduleDetail->revisions()
                ->whereNotNull('student_file')
                ->where('student_file', '!=', '')
                ->latest('updated_at')
                ->first();
            if ($approvedRevision) {
                $bestFile = $approvedRevision->student_file;
            }
        }

        if (!$bestFile) {
            $defenseApp = $thesis->defenseApplication ?? $thesis->defenseApplications()->latest('id')->first();
            if ($defenseApp && !empty($defenseApp->file_skripsi)) {
                $bestFile = $defenseApp->file_skripsi;
            }
        }

        // Match criteria: identifier if present, or title & name
        $match = !empty($student->identifier)
            ? ['identifier' => $student->identifier]
            : ['title' => $title, 'name' => $student->name];

        $repo = self::updateOrCreate(
            $match,
            [
                'identifier' => $student->identifier,
                'name' => $student->name,
                'title' => $title,
                'year' => $year,
                'abstract' => $thesis->abstract,
                'pembimbing1' => $thesis->pembimbing1?->name,
                'pembimbing2' => $thesis->pembimbing2?->name,
                'file_path' => $bestFile ?: null,
            ]
        );

        \Illuminate\Support\Facades\Cache::forget('repo_catalog_metadata');

        try {
            ActivityLog::log(
                'Arsip Pustaka Otomatis',
                "Skripsi mahasiswa {$student->name} ({$student->identifier}) berhasil diarsipkan secara otomatis ke Katalog Pustaka.",
                'Katalog Pustaka',
                $repo
            );
        } catch (\Throwable $e) {
            // Silently continue if activity log fails
        }

        return $repo;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasActivityLog;
use App\Traits\Auditable;

class Thesis extends Model
{
    use HasActivityLog, Auditable;

    protected $fillable = [
        'student_id',
        'pembimbing1_id',
        'pembimbing2_id',
        'requested_pembimbing1_id',
        'requested_pembimbing2_id',
        'title',
        'final_title',
        'abstract',
        'status',
        'acc_up_p1',
        'acc_up_p2',
        'acc_sidang_p1',
        'acc_sidang_p2',
        'topic',
    ];

    public function getDisplayTitleAttribute()
    {
        return $this->final_title ?: $this->title;
    }

    public function getCompletedMentoringCountAttribute()
    {
        return $this->mentoringSessions()
            ->where('status', 'completed')
            ->where('is_absent', false)
            ->count();
    }

    public function getCompletedMentoringCountForDosen($dosenId)
    {
        return $this->mentoringSessions()
            ->where('dosen_id', $dosenId)
            ->where('status', 'completed')
            ->where('is_absent', false)
            ->count();
    }

    public function isAccUpFinal()
    {
        return $this->acc_up_p1 && $this->acc_up_p2;
    }

    public function isAccSidangFinal()
    {
        return $this->acc_sidang_p1 && $this->acc_sidang_p2;
    }

    public function isGraduated()
    {
        return $this->status === 'completed';
    }

    public function isOldCohort(): bool
    {
        if (!$this->student || !$this->student->entry_year) {
            return false;
        }
        $currentYear = now()->year;
        $isSecondHalf = now()->month >= 9;
        $thresholdYear = $isSecondHalf ? ($currentYear - 4) : ($currentYear - 5);
        return (int)$this->student->entry_year <= $thresholdYear;
    }

    public function isNewCohort(): bool
    {
        return !$this->isOldCohort();
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function pembimbing1()
    {
        return $this->belongsTo(User::class, 'pembimbing1_id');
    }

    public function pembimbing2()
    {
        return $this->belongsTo(User::class, 'pembimbing2_id');
    }

    public function requestedPembimbing1()
    {
        return $this->belongsTo(User::class, 'requested_pembimbing1_id');
    }

    public function requestedPembimbing2()
    {
        return $this->belongsTo(User::class, 'requested_pembimbing2_id');
    }

    public function mentoringSessions()
    {
        return $this->hasMany(MentoringSession::class);
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class);
    }

    public function seminarApplication()
    {
        return $this->hasOne(SeminarApplication::class);
    }

    public function defenseApplication()
    {
        return $this->hasOne(ThesisDefenseApplication::class);
    }

    /**
     * Calculate match score between thesis topic and lecturer research interests.
     */
    public function getMatchScore(User $dosen)
    {
        if (!$dosen->research_interests || !$this->title) {
            return 0;
        }

        $interests = explode(',', strtolower($dosen->research_interests));
        $content = strtolower($this->title . ' ' . ($this->abstract ?? ''));

        $score = 0;
        foreach ($interests as $interest) {
            $interest = trim($interest);
            if (empty($interest))
                continue;

            if (str_contains($content, $interest)) {
                $score++;
            }
        }

        return $score;
    }

    public function scopeWithMentoringCounts($query)
    {
        return $query->withCount([
            'mentoringSessions as total_sessions' => function ($q) {
                $q->where('status', 'completed')->where('is_absent', false);
            }
        ])
            ->withCount([
                'mentoringSessions as sessions_p1' => function ($q) {
                    $q->where('status', 'completed')
                        ->where('is_absent', false)
                        ->whereColumn('dosen_id', 'pembimbing1_id');
                }
            ])
            ->withCount([
                'mentoringSessions as sessions_p2' => function ($q) {
                    $q->where('status', 'completed')
                        ->where('is_absent', false)
                        ->whereColumn('dosen_id', 'pembimbing2_id');
                }
            ]);
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($q) use ($search) {
            $q->where(function ($sq) use ($search) {
                $sq->whereHas('student', function ($ssq) use ($search) {
                    $ssq->where('name', 'like', "%{$search}%")
                        ->orWhere('identifier', 'like', "%{$search}%");
                })
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('final_title', 'like', "%{$search}%");
            });
        });
    }

    public function scopeForUser($query, $user)
    {
        if ($user->role === 'dosen') {
            return $query->where(function ($q) use ($user) {
                $q->where('pembimbing1_id', $user->id)
                    ->orWhere('pembimbing2_id', $user->id);
            });
        } elseif ($user->role === 'mahasiswa') {
            return $query->where('student_id', $user->id);
        }
        return $query;
    }

    /**
     * Common academic stopwords & Indonesian conjunctions/prepositions ignored in keyword matching.
     */
    public static function getStopwords(): array
    {
        return [
            'sistem', 'informasi', 'aplikasi', 'perancangan', 'rancang', 'bangun', 'pembuatan', 
            'pengembangan', 'berbasis', 'web', 'android', 'website', 'mobile', 'dengan', 'metode', 
            'menggunakan', 'pada', 'untuk', 'studi', 'kasus', 'penerapan', 'implementasi', 'pengaruh', 
            'analisis', 'evaluasi', 'pengujian', 'penelitian', 'kajian', 'tinjauan', 'perbandingan', 
            'model', 'desa', 'kabupaten', 'kota', 'kecamatan', 'pt', 'cv', 'ud', 'tbk',
            'dan', 'atau', 'di', 'ke', 'dari', 'yang', 'dalam', 'oleh', 'serta', 'maupun', 
            'sebagai', 'terhadap', 'tentang', 'atas', 'bagi', 'antar', 'antara', 'hingga', 
            'sampai', 'sebuah', 'suatu', 'hal', 'dll', 'dkk', 'dst', 'seperti', 'agar', 
            'supaya', 'karena', 'sehingga', 'jika', 'bila'
        ];
    }

    /**
     * Calculate max title similarity score against active theses & alumni repository.
     */
    public function getMaxSimilarityScore()
    {
        $targetTitle = trim($this->final_title ?? $this->title ?? '');
        $upper = strtoupper($targetTitle);
        $placeholders = ['BELUM DIKETAHUI', 'BELUM DITENTUKAN', 'BELUM ADA JUDUL', 'BELUM ADA', '-'];
        
        if (empty($targetTitle) || in_array($upper, $placeholders) || strlen($targetTitle) < 8) {
            return 0;
        }

        return \Illuminate\Support\Facades\Cache::remember('thesis_max_sim_' . $this->id . '_' . md5($targetTitle), 1800, function() use ($targetTitle, $placeholders) {
            $maxScore = 0;
            $cleanInput = preg_replace('/[^a-z0-9\s]/', '', strtolower($targetTitle));
            $inputTokens = array_values(array_filter(explode(' ', $cleanInput)));

            if (empty($inputTokens)) return 0;

            $otherTitles = self::where('id', '!=', $this->id)
                ->when($this->student_id, fn($q) => $q->where('student_id', '!=', $this->student_id))
                ->whereNotNull('title')
                ->pluck('title')
                ->filter(fn($t) => !in_array(strtoupper(trim($t)), $placeholders) && strlen(trim($t)) >= 8);

            $repoTitles = \App\Models\ThesisRepository::whereNotNull('title')
                ->pluck('title')
                ->filter(fn($t) => !in_array(strtoupper(trim($t)), $placeholders) && strlen(trim($t)) >= 8);

            $allTitles = $otherTitles->concat($repoTitles);

            $stopwords = self::getStopwords();
            $inputFiltered = array_values(array_filter(array_diff($inputTokens, $stopwords), fn($w) => strlen($w) > 2));
            if (empty($inputFiltered)) $inputFiltered = $inputTokens;

            foreach ($allTitles as $existingTitle) {
                $cleanExisting = preg_replace('/[^a-z0-9\s]/', '', strtolower($existingTitle));
                if (trim($cleanInput) === trim($cleanExisting)) {
                    return 100;
                }
                
                $existingTokens = array_values(array_filter(explode(' ', $cleanExisting)));
                $existingFiltered = array_values(array_filter(array_diff($existingTokens, $stopwords), fn($w) => strlen($w) > 2));
                if (empty($existingFiltered)) $existingFiltered = $existingTokens;

                $intersection = array_intersect($inputFiltered, $existingFiltered);
                $union = array_unique(array_merge($inputFiltered, $existingFiltered));

                $jaccard = count($union) > 0 ? (count($intersection) / count($union)) * 100 : 0;
                $dice = (count($inputFiltered) + count($existingFiltered)) > 0 
                    ? (2 * count($intersection) / (count($inputFiltered) + count($existingFiltered))) * 100 
                    : 0;

                $similarTextPercent = 0;
                similar_text($cleanInput, $cleanExisting, $similarTextPercent);

                $tokenScore = max($jaccard, $dice);
                $finalScore = ($tokenScore * 0.60) + ($similarTextPercent * 0.40);

                if ($finalScore > $maxScore) {
                    $maxScore = $finalScore;
                }
            }

            return min(100, (int) round($maxScore));
        });
    }

    /**
     * Get top detailed similarity matches against active theses & alumni repository.
     */
    public function getDetailedSimilarityMatches()
    {
        $targetTitle = trim($this->final_title ?? $this->title ?? '');
        $upper = strtoupper($targetTitle);
        $placeholders = ['BELUM DIKETAHUI', 'BELUM DITENTUKAN', 'BELUM ADA JUDUL', 'BELUM ADA', '-'];
        
        if (empty($targetTitle) || in_array($upper, $placeholders) || strlen($targetTitle) < 8) {
            return collect();
        }

        return \Illuminate\Support\Facades\Cache::remember('thesis_sim_details_' . $this->id . '_' . md5($targetTitle), 1800, function() use ($targetTitle, $placeholders) {
            $matches = [];
            $cleanInput = preg_replace('/[^a-z0-9\s]/', '', strtolower($targetTitle));
            $inputTokens = array_values(array_filter(explode(' ', $cleanInput)));

            if (empty($inputTokens)) return collect();

            $stopwords = self::getStopwords();
            $inputFiltered = array_values(array_filter(array_diff($inputTokens, $stopwords), fn($w) => strlen($w) > 2));
            if (empty($inputFiltered)) $inputFiltered = $inputTokens;

            // Check against active theses
            $otherTheses = self::with('student')
                ->where('id', '!=', $this->id)
                ->when($this->student_id, fn($q) => $q->where('student_id', '!=', $this->student_id))
                ->whereNotNull('title')
                ->get();

            foreach ($otherTheses as $other) {
                $t = trim($other->final_title ?? $other->title);
                if (in_array(strtoupper($t), $placeholders) || strlen($t) < 8) continue;

                $cleanExisting = preg_replace('/[^a-z0-9\s]/', '', strtolower($t));
                $existingTokens = array_values(array_filter(explode(' ', $cleanExisting)));
                $existingFiltered = array_values(array_filter(array_diff($existingTokens, $stopwords), fn($w) => strlen($w) > 2));
                if (empty($existingFiltered)) $existingFiltered = $existingTokens;

                $intersection = array_values(array_unique(array_intersect($inputFiltered, $existingFiltered)));
                $union = array_unique(array_merge($inputFiltered, $existingFiltered));

                $jaccard = count($union) > 0 ? (count($intersection) / count($union)) * 100 : 0;
                $dice = (count($inputFiltered) + count($existingFiltered)) > 0 
                    ? (2 * count($intersection) / (count($inputFiltered) + count($existingFiltered))) * 100 
                    : 0;

                $similarTextPercent = 0;
                similar_text($cleanInput, $cleanExisting, $similarTextPercent);

                $tokenScore = max($jaccard, $dice);
                $finalScore = ($tokenScore * 0.60) + ($similarTextPercent * 0.40);
                $percent = min(100, (int) round($finalScore));

                if ($percent >= 30) {
                    $matches[] = [
                        'title' => $t,
                        'author' => $other->student->name ?? 'Mahasiswa',
                        'year' => $other->student->entry_year ?? ($other->created_at ? $other->created_at->format('Y') : date('Y')),
                        'percentage' => $percent,
                        'matched_words' => $intersection,
                        'source' => 'Skripsi Mahasiswa Aktif'
                    ];
                }
            }

            // Check against repository alumni
            $repos = \App\Models\ThesisRepository::whereNotNull('title')->get();
            foreach ($repos as $repo) {
                $t = trim($repo->title);
                if (in_array(strtoupper($t), $placeholders) || strlen($t) < 8) continue;

                $cleanExisting = preg_replace('/[^a-z0-9\s]/', '', strtolower($t));
                $existingTokens = array_values(array_filter(explode(' ', $cleanExisting)));
                $existingFiltered = array_values(array_filter(array_diff($existingTokens, $stopwords), fn($w) => strlen($w) > 2));
                if (empty($existingFiltered)) $existingFiltered = $existingTokens;

                $intersection = array_values(array_unique(array_intersect($inputFiltered, $existingFiltered)));
                $union = array_unique(array_merge($inputFiltered, $existingFiltered));

                $jaccard = count($union) > 0 ? (count($intersection) / count($union)) * 100 : 0;
                $dice = (count($inputFiltered) + count($existingFiltered)) > 0 
                    ? (2 * count($intersection) / (count($inputFiltered) + count($existingFiltered))) * 100 
                    : 0;

                $similarTextPercent = 0;
                similar_text($cleanInput, $cleanExisting, $similarTextPercent);

                $tokenScore = max($jaccard, $dice);
                $finalScore = ($tokenScore * 0.60) + ($similarTextPercent * 0.40);
                $percent = min(100, (int) round($finalScore));

                if ($percent >= 30) {
                    $matches[] = [
                        'title' => $t,
                        'author' => $repo->name ?? 'Alumni',
                        'year' => $repo->year ?? date('Y'),
                        'percentage' => $percent,
                        'matched_words' => $intersection,
                        'source' => 'Arsip Alumni FASILKOM'
                    ];
                }
            }

            usort($matches, fn($a, $b) => $b['percentage'] <=> $a['percentage']);

            return collect(array_slice($matches, 0, 5));
        });
    }

    /**
     * Get comprehensive data cleanliness and validation audit for this thesis proposal.
     */
    public function getAuditCleanData($dosens = null)
    {
        $simScore = $this->getMaxSimilarityScore();
        $matches = $this->getDetailedSimilarityMatches();

        $title = trim($this->final_title ?? $this->title ?? '');
        $abstract = trim($this->abstract ?? '');
        $wordCount = !empty($abstract) ? count(preg_split('/\s+/', $abstract)) : 0;

        $issues = [];
        $strengths = [];

        // 1. Title uniqueness check
        if ($simScore >= 66) {
            $issues[] = "Tingkat kemiripan judul tinggi ({$simScore}%). Terindikasi mirip dengan skripsi yang sudah ada.";
        } elseif ($simScore >= 35) {
            $issues[] = "Tingkat kemiripan judul moderat ({$simScore}%). Perlu peninjauan variasi topik.";
        } else {
            $strengths[] = "Judul orisinal & unik (kemiripan {$simScore}%).";
        }

        // 2. Abstract completeness
        if (empty($abstract)) {
            $issues[] = "Deskripsi / abstrak pengajuan masih kosong.";
        } elseif ($wordCount < 20) {
            $issues[] = "Deskripsi terlalu singkat ({$wordCount} kata). Minimal disarankan 30-50 kata.";
        } else {
            $strengths[] = "Deskripsi / rencana penelitian memadai ({$wordCount} kata).";
        }

        // 3. Supervisor Evaluation
        $reqP1 = $this->requestedPembimbing1;
        $reqP2 = $this->requestedPembimbing2;

        $reqP1Status = null;
        if ($reqP1) {
            $p1Workload = $reqP1->total_workload ?? ($reqP1->thesesAsP1()->where('status', 'active')->count() + $reqP1->thesesAsP2()->where('status', 'active')->count());
            $p1Max = $reqP1->max_quota ?? 10;
            $p1Match = $this->getMatchScore($reqP1);
            $p1Full = $p1Workload >= $p1Max;

            $reqP1Status = [
                'id' => $reqP1->id,
                'name' => $reqP1->name,
                'workload' => $p1Workload,
                'max_quota' => $p1Max,
                'is_full' => $p1Full,
                'match_score' => $p1Match,
            ];

            if ($p1Full) {
                $issues[] = "Usulan Pembimbing 1 ({$reqP1->name}) kuotanya sudah penuh ({$p1Workload}/{$p1Max}).";
            } else {
                $strengths[] = "Usulan Pembimbing 1 ({$reqP1->name}) kuota tersedia ({$p1Workload}/{$p1Max}).";
            }
        } else {
            $issues[] = "Mahasiswa belum memilih usulan Pembimbing 1.";
        }

        $reqP2Status = null;
        if ($reqP2) {
            $p2Workload = $reqP2->total_workload ?? ($reqP2->thesesAsP1()->where('status', 'active')->count() + $reqP2->thesesAsP2()->where('status', 'active')->count());
            $p2Max = $reqP2->max_quota ?? 10;
            $p2Match = $this->getMatchScore($reqP2);
            $p2Full = $p2Workload >= $p2Max;

            $reqP2Status = [
                'id' => $reqP2->id,
                'name' => $reqP2->name,
                'workload' => $p2Workload,
                'max_quota' => $p2Max,
                'is_full' => $p2Full,
                'match_score' => $p2Match,
            ];

            if ($p2Full) {
                $issues[] = "Usulan Pembimbing 2 ({$reqP2->name}) kuotanya sudah penuh ({$p2Workload}/{$p2Max}).";
            } else {
                $strengths[] = "Usulan Pembimbing 2 ({$reqP2->name}) kuota tersedia ({$p2Workload}/{$p2Max}).";
            }
        } else {
            $issues[] = "Mahasiswa belum memilih usulan Pembimbing 2.";
        }

        // 4. Smart Recommendations
        $recommendedP1 = null;
        $recommendedP2 = null;

        if ($dosens) {
            $rankedDosens = collect($dosens)->map(function ($d) {
                $score = $this->getMatchScore($d);
                $isFull = ($d->total_workload ?? 0) >= ($d->max_quota ?? 10);
                $avail = max(0, ($d->max_quota ?? 10) - ($d->total_workload ?? 0));
                return [
                    'dosen' => $d,
                    'match_score' => $score,
                    'available_quota' => $avail,
                    'is_full' => $isFull,
                    'rank' => ($score * 10) + $avail - ($isFull ? 1000 : 0)
                ];
            })->sortByDesc('rank')->values();

            $availableRanked = $rankedDosens->filter(fn($item) => !$item['is_full']);

            if ($availableRanked->isNotEmpty()) {
                $first = $availableRanked->first();
                $recommendedP1 = [
                    'id' => $first['dosen']->id,
                    'name' => $first['dosen']->name,
                    'match_score' => $first['match_score'],
                    'workload' => $first['dosen']->total_workload ?? 0,
                    'max_quota' => $first['dosen']->max_quota ?? 10,
                ];

                $second = $availableRanked->skip(1)->first();
                if ($second) {
                    $recommendedP2 = [
                        'id' => $second['dosen']->id,
                        'name' => $second['dosen']->name,
                        'match_score' => $second['match_score'],
                        'workload' => $second['dosen']->total_workload ?? 0,
                        'max_quota' => $second['dosen']->max_quota ?? 10,
                    ];
                }
            }
        }

        // Overall Category
        if ($simScore >= 66) {
            $category = 'critical';
            $categoryLabel = 'Kemiripan Judul Kritis';
        } elseif (count($issues) > 0 || $simScore >= 35) {
            $category = 'warning';
            $categoryLabel = 'Perlu Peninjauan';
        } else {
            $category = 'clean';
            $categoryLabel = '100% Bersih & Siap Ditugaskan';
        }

        return [
            'id' => $this->id,
            'student_name' => $this->student->name ?? 'Mahasiswa',
            'student_identifier' => $this->student->identifier ?? '-',
            'entry_year' => $this->student->entry_year ?? '-',
            'avatar_url' => $this->student->avatar_url ?? null,
            'title' => $title,
            'abstract' => $abstract,
            'word_count' => $wordCount,
            'similarity_score' => $simScore,
            'similarity_matches' => $matches->values()->toArray(),
            'category' => $category,
            'category_label' => $categoryLabel,
            'issues' => $issues,
            'strengths' => $strengths,
            'req_p1' => $reqP1Status,
            'req_p2' => $reqP2Status,
            'recommended_p1' => $recommendedP1,
            'recommended_p2' => $recommendedP2,
            'created_at' => $this->created_at ? $this->created_at->format('d M Y') : '-',
        ];
    }
}

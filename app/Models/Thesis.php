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

        return \Illuminate\Support\Facades\Cache::remember('thesis_sim_score_' . $this->id . '_' . md5($targetTitle), 1800, function() use ($targetTitle, $placeholders) {
            $maxScore = 0;
            $cleanInput = preg_replace('/[^a-z0-9\s]/', '', strtolower($targetTitle));
            $inputTokens = array_values(array_filter(explode(' ', $cleanInput)));

            if (empty($inputTokens)) return 0;

            $otherTitles = self::where('id', '!=', $this->id)
                ->whereNotNull('title')
                ->pluck('title')
                ->filter(fn($t) => !in_array(strtoupper(trim($t)), $placeholders) && strlen(trim($t)) >= 8);

            $repoTitles = \App\Models\ThesisRepository::whereNotNull('title')
                ->pluck('title')
                ->filter(fn($t) => !in_array(strtoupper(trim($t)), $placeholders) && strlen(trim($t)) >= 8);

            $allTitles = $otherTitles->concat($repoTitles);

            $stopwords = ['sistem', 'informasi', 'aplikasi', 'perancangan', 'rancang', 'bangun', 'pembuatan', 'pengembangan', 'berbasis', 'web', 'android', 'website', 'mobile', 'dengan', 'metode', 'menggunakan', 'pada', 'untuk', 'studi', 'kasus', 'penerapan', 'implementasi', 'pengaruh', 'analisis', 'evaluasi', 'pengujian', 'desa', 'kabupaten', 'kota', 'kecamatan', 'pt', 'cv'];
            $inputFiltered = array_values(array_diff($inputTokens, $stopwords));
            if (empty($inputFiltered)) $inputFiltered = $inputTokens;

            foreach ($allTitles as $existingTitle) {
                $cleanExisting = preg_replace('/[^a-z0-9\s]/', '', strtolower($existingTitle));
                if (trim($cleanInput) === trim($cleanExisting)) {
                    return 100;
                }
                
                $existingTokens = array_values(array_filter(explode(' ', $cleanExisting)));
                $existingFiltered = array_values(array_diff($existingTokens, $stopwords));
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

            $stopwords = ['sistem', 'informasi', 'aplikasi', 'perancangan', 'rancang', 'bangun', 'pembuatan', 'pengembangan', 'berbasis', 'web', 'android', 'website', 'mobile', 'dengan', 'metode', 'menggunakan', 'pada', 'untuk', 'studi', 'kasus', 'penerapan', 'implementasi', 'pengaruh', 'analisis', 'evaluasi', 'pengujian', 'desa', 'kabupaten', 'kota', 'kecamatan', 'pt', 'cv'];
            $inputFiltered = array_values(array_diff($inputTokens, $stopwords));
            if (empty($inputFiltered)) $inputFiltered = $inputTokens;

            // Check against active theses
            $otherTheses = self::with('student')
                ->where('id', '!=', $this->id)
                ->whereNotNull('title')
                ->get();

            foreach ($otherTheses as $other) {
                $t = trim($other->final_title ?? $other->title);
                if (in_array(strtoupper($t), $placeholders) || strlen($t) < 8) continue;

                $cleanExisting = preg_replace('/[^a-z0-9\s]/', '', strtolower($t));
                $existingTokens = array_values(array_filter(explode(' ', $cleanExisting)));
                $existingFiltered = array_values(array_diff($existingTokens, $stopwords));
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
                $existingFiltered = array_values(array_diff($existingTokens, $stopwords));
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
}

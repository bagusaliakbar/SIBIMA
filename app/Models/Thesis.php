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
        return $this->mentoringSessions()->where('status', 'completed')->count();
    }

    public function getCompletedMentoringCountForDosen($dosenId)
    {
        return $this->mentoringSessions()
            ->where('dosen_id', $dosenId)
            ->where('status', 'completed')
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
}

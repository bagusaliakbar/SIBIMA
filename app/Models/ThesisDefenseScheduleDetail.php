<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasActivityLog;

class ThesisDefenseScheduleDetail extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'thesis_defense_schedule_id',
        'thesis_id',
        'activity_name',
        'start_time',
        'end_time',
        'examiner1_id',
        'examiner2_id',
        'order',
        'verification_token'
    ];

    protected static function booted()
    {
        static::creating(function ($detail) {
            if (!$detail->verification_token) {
                $detail->verification_token = \Illuminate\Support\Str::random(32);
            }
        });
    }

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function schedule()
    {
        return $this->belongsTo(ThesisDefenseSchedule::class, 'thesis_defense_schedule_id');
    }

    public function thesis()
    {
        return $this->belongsTo(Thesis::class);
    }

    public function examiner1()
    {
        return $this->belongsTo(User::class, 'examiner1_id');
    }

    public function examiner2()
    {
        return $this->belongsTo(User::class, 'examiner2_id');
    }

    public function revisions()
    {
        return $this->hasMany(ThesisDefenseRevision::class, 'thesis_defense_schedule_detail_id');
    }

    public function getRevisionFor($examinerId)
    {
        return $this->revisions->where('examiner_id', $examinerId)->first();
    }

    public function isRevisionAllApproved()
    {
        if (!$this->thesis) return false;

        $requiredIds = array_unique(array_filter([
            $this->examiner1_id,
            $this->examiner2_id,
            $this->thesis->pembimbing1_id
        ]));

        if (count($requiredIds) === 0) return false;

        $loaded = $this->relationLoaded('revisions');

        foreach ($requiredIds as $id) {
            $rev = $loaded
                ? $this->revisions->firstWhere('examiner_id', $id)
                : $this->revisions()->where('examiner_id', $id)->first();
            if (!$rev || $rev->status !== 'approved') {
                return false;
            }
        }

        return true;
    }

    public function isRevisionStarted()
    {
        if ($this->relationLoaded('revisions')) {
            return $this->revisions->isNotEmpty();
        }

        return $this->revisions()->exists();
    }

    public function isGradingComplete()
    {
        return $this->isGraded();
    }

    public function isGraded()
    {
        if ($this->relationLoaded('revisions')) {
            $rev1 = $this->revisions->firstWhere('examiner_id', $this->examiner1_id);
            $rev2 = $this->revisions->firstWhere('examiner_id', $this->examiner2_id);
            $revP1 = $this->thesis ? $this->revisions->firstWhere('examiner_id', $this->thesis->pembimbing1_id) : null;
        } else {
            $rev1 = $this->revisions()->where('examiner_id', $this->examiner1_id)->first();
            $rev2 = $this->revisions()->where('examiner_id', $this->examiner2_id)->first();
            $revP1 = $this->thesis ? $this->revisions()->where('examiner_id', $this->thesis->pembimbing1_id)->first() : null;
        }

        return ($rev1 && $rev1->isGraded()) &&
               ($rev2 && $rev2->isGraded()) &&
               ($revP1 && $revP1->isGraded());
    }

    public function isRevisionComplete()
    {
        if ($this->relationLoaded('revisions')) {
            $rev1 = $this->revisions->firstWhere('examiner_id', $this->examiner1_id);
            $rev2 = $this->revisions->firstWhere('examiner_id', $this->examiner2_id);
        } else {
            $rev1 = $this->revisions()->where('examiner_id', $this->examiner1_id)->first();
            $rev2 = $this->revisions()->where('examiner_id', $this->examiner2_id)->first();
        }

        return ($rev1 && $rev1->isApproved()) &&
               ($rev2 && $rev2->isApproved());
    }
}

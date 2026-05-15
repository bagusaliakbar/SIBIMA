<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasActivityLog;

class SeminarScheduleDetail extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'seminar_schedule_id', 
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
        return $this->belongsTo(SeminarSchedule::class, 'seminar_schedule_id');
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
        return $this->hasMany(SeminarRevision::class, 'seminar_schedule_detail_id');
    }

    public function getRevisionFor($examinerId)
    {
        return $this->revisions->where('examiner_id', $examinerId)->first();
    }

    public function isAllRevisionsApproved()
    {
        $rev1 = $this->getRevisionFor($this->examiner1_id);
        $rev2 = $this->getRevisionFor($this->examiner2_id);
        
        return ($rev1 && $rev1->status === 'approved') && ($rev2 && $rev2->status === 'approved');
    }

    public function isRevisionStarted()
    {
        return $this->revisions->count() > 0;
    }

    public function isGraded()
    {
        $rev1 = $this->getRevisionFor($this->examiner1_id);
        $rev2 = $this->getRevisionFor($this->examiner2_id);
        
        return ($rev1 && $rev1->isGraded()) && ($rev2 && $rev2->isGraded());
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thesis extends Model
{
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class MentoringSession extends Model
{
    use Auditable;

    protected $fillable = [
        'thesis_id',
        'dosen_id',
        'scheduled_at',
        'topic',
        'type',
        'location',
        'status',
        'student_attendance_status',
        'student_attendance_reason',
        'student_confirmed_at',
        'is_absent',
        'notes',
        'feedback',
        'document_path',
        'document_original_name',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'student_confirmed_at' => 'datetime',
    ];

    public function isStudentConfirmed(): bool
    {
        return in_array($this->student_attendance_status, ['attending', 'permission']);
    }

    public function isStudentAttending(): bool
    {
        return $this->student_attendance_status === 'attending';
    }

    public function isStudentPermission(): bool
    {
        return $this->student_attendance_status === 'permission';
    }

    public function thesis()
    {
        return $this->belongsTo(Thesis::class);
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($q) use ($search) {
            $q->where(function ($sq) use ($search) {
                $sq->whereHas('thesis.student', function ($ssq) use ($search) {
                    $ssq->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('dosen', function ($ssq) use ($search) {
                    $ssq->where('name', 'like', "%{$search}%");
                })
                ->orWhere('topic', 'like', "%{$search}%");
            });
        });
    }

    public function scopeForUser($query, $user)
    {
        if ($user->role === 'dosen') {
            return $query->where('dosen_id', $user->id);
        } elseif ($user->role === 'mahasiswa') {
            return $query->whereHas('thesis', function ($q) use ($user) {
                $q->where('student_id', $user->id);
            });
        }
        return $query;
    }
}

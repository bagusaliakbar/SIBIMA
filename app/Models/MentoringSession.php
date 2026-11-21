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
        'scheduled_end_at',
        'topic',
        'type',
        'location',
        'status',
        'student_attendance_status',
        'student_arrival_time',
        'student_attendance_reason',
        'student_confirmed_at',
        'is_absent',
        'notes',
        'feedback',
        'feedback_document_url',
        'document_path',
        'document_original_name',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'scheduled_end_at' => 'datetime',
        'student_confirmed_at' => 'datetime',
    ];

    /**
     * Format time range as "09:00 - 11:00 WIB" (or "09:00 WIB" for legacy sessions without end time).
     */
    public function getTimeRangeAttribute(): string
    {
        if (!$this->scheduled_at) {
            return '-';
        }

        $start = $this->scheduled_at->format('H:i');

        if ($this->scheduled_end_at) {
            return "{$start} - {$this->scheduled_end_at->format('H:i')} WIB";
        }

        return "{$start} WIB";
    }

    /**
     * Format full Indonesian date & time range without trailing "WIB"
     * (e.g. "Senin, 24 November 2026 09:00 - 11:00") for WA templates & notifications.
     */
    public function getFormattedDateTimeAttribute(): string
    {
        if (!$this->scheduled_at) {
            return '-';
        }

        $start = $this->scheduled_at->copy()->locale('id')->translatedFormat('l, d F Y H:i');

        if ($this->scheduled_end_at) {
            return "{$start} - {$this->scheduled_end_at->format('H:i')}";
        }

        return $start;
    }

    /**
     * Format compact date & time range (e.g. "24/11/2026 09:00 - 11:00") for activity logs.
     */
    public function getCompactDateTimeAttribute(): string
    {
        if (!$this->scheduled_at) {
            return '-';
        }

        $start = $this->scheduled_at->format('d/m/Y H:i');

        if ($this->scheduled_end_at) {
            return "{$start} - {$this->scheduled_end_at->format('H:i')}";
        }

        return $start;
    }

    public function getFormattedArrivalTimeAttribute(): ?string
    {
        if (empty($this->student_arrival_time)) {
            return null;
        }

        return $this->student_arrival_time . ' WIB';
    }

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

    public function hasFeedbackDocument(): bool
    {
        return !empty($this->feedback_document_url);
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

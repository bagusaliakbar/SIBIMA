<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeminarRevision extends Model
{
    protected $fillable = [
        'seminar_schedule_detail_id',
        'examiner_id',
        'revision_notes',
        'revision_file',
        'student_notes',
        'student_file',
        'resubmitted_at',
        'status',
        'score_presentation',
        'score_explanation',
        'score_writing',
    ];

    public function getTotalScoreAttribute()
    {
        return ($this->score_presentation * 0.25) + ($this->score_explanation * 0.40) + ($this->score_writing * 0.35);
    }

    public function isGraded(): bool
    {
        return $this->score_presentation !== null;
    }

    public function detail(): BelongsTo
    {
        return $this->belongsTo(SeminarScheduleDetail::class, 'seminar_schedule_detail_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SeminarRevisionMessage::class, 'seminar_revision_id')->orderBy('created_at', 'asc');
    }

    public function examiner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'examiner_id');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isResubmitted(): bool
    {
        return $this->status === 'resubmitted';
    }
}

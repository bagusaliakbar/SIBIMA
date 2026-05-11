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
    ];

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
}

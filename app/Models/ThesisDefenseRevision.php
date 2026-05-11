<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThesisDefenseRevision extends Model
{
    protected $fillable = [
        'thesis_defense_schedule_detail_id',
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

    public function detail(): BelongsTo
    {
        return $this->belongsTo(ThesisDefenseScheduleDetail::class, 'thesis_defense_schedule_detail_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ThesisDefenseRevisionMessage::class, 'thesis_defense_revision_id')->orderBy('created_at', 'asc');
    }

    public function examiner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'examiner_id');
    }
}

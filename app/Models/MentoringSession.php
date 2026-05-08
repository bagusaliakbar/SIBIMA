<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentoringSession extends Model
{
    protected $fillable = [
        'thesis_id',
        'dosen_id',
        'scheduled_at',
        'topic',
        'type',
        'location',
        'status',
        'is_absent',
        'notes',
        'feedback',
        'document_path',
        'document_original_name',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function thesis()
    {
        return $this->belongsTo(Thesis::class);
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }
}

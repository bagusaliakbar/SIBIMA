<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    protected $fillable = [
        'thesis_id',
        'date',
        'progress_notes',
        'lecturer_notes',
        'is_approved',
    ];

    protected $casts = [
        'date' => 'date',
        'is_approved' => 'boolean',
    ];

    public function thesis()
    {
        return $this->belongsTo(Thesis::class);
    }
}

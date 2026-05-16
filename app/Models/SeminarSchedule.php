<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeminarSchedule extends Model
{
    protected $fillable = ['title', 'date', 'chairman_id', 'moderator_id', 'location', 'created_by', 'wave_id', 'meeting_link'];

    protected $casts = [
        'date' => 'date',
    ];

    public function wave()
    {
        return $this->belongsTo(Wave::class);
    }

    public function chairman()
    {
        return $this->belongsTo(User::class, 'chairman_id');
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(SeminarScheduleDetail::class)->orderBy('order');
    }
}

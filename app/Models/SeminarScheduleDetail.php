<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeminarScheduleDetail extends Model
{
    protected $fillable = [
        'seminar_schedule_id', 
        'thesis_id', 
        'activity_name', 
        'start_time', 
        'end_time', 
        'examiner1_id', 
        'examiner2_id', 
        'order'
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThesisDefenseScheduleDetail extends Model
{
    protected $fillable = [
        'thesis_defense_schedule_id',
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
        return $this->belongsTo(ThesisDefenseSchedule::class, 'thesis_defense_schedule_id');
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
}

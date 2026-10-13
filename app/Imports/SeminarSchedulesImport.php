<?php

namespace App\Imports;

use App\Models\SeminarSchedule;
use App\Models\SeminarScheduleDetail;

class SeminarSchedulesImport extends BaseScheduleImport
{
    protected function getScheduleModel(): string
    {
        return SeminarSchedule::class;
    }

    protected function getDetailModel(): string
    {
        return SeminarScheduleDetail::class;
    }

    protected function getScheduleTypeLabel(): string
    {
        return 'seminar proposal';
    }
}

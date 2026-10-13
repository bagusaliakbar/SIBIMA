<?php

namespace App\Imports;

use App\Models\ThesisDefenseSchedule;
use App\Models\ThesisDefenseScheduleDetail;

class ThesisDefenseSchedulesImport extends BaseScheduleImport
{
    protected function getScheduleModel(): string
    {
        return ThesisDefenseSchedule::class;
    }

    protected function getDetailModel(): string
    {
        return ThesisDefenseScheduleDetail::class;
    }

    protected function getScheduleTypeLabel(): string
    {
        return 'sidang skripsi';
    }
}

<?php

namespace App\Exports;

use App\Models\SeminarApplication;

class SeminarApplicationsExport extends BaseApplicationExport
{
    protected function getModelClass(): string
    {
        return SeminarApplication::class;
    }

    public function title(): string
    {
        return 'Validasi Seminar';
    }

    protected function getReportTitle(): string
    {
        return 'DATA VALIDASI PENDAFTARAN SEMINAR PROPOSAL';
    }

    protected function getHeaderColor(): string
    {
        return '312E81'; // Deep Indigo-900 (Professional SIBIMA Seminar Theme)
    }
}

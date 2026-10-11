<?php

namespace App\Exports;

use App\Models\ThesisDefenseApplication;

class ThesisDefenseApplicationsExport extends BaseApplicationExport
{
    protected function getModelClass(): string
    {
        return ThesisDefenseApplication::class;
    }

    public function title(): string
    {
        return 'Validasi Sidang';
    }

    protected function getReportTitle(): string
    {
        return 'DATA VALIDASI PENDAFTARAN SIDANG SKRIPSI';
    }

    protected function getHeaderColor(): string
    {
        return '065F46'; // Deep Emerald-800 (Professional SIBIMA Defense Theme)
    }
}

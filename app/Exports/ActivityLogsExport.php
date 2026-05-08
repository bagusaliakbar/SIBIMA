<?php

namespace App\Exports;

use App\Models\ActivityLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ActivityLogsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $search;
    protected $module;

    public function __construct($search = null, $module = null)
    {
        $this->search = $search;
        $this->module = $module;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ActivityLog::with('user')
            ->when($this->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('activity', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($uq) use ($search) {
                          $uq->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($this->module, function ($query, $module) {
                return $query->where('module', $module);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Waktu',
            'Pengguna',
            'Peran',
            'Aktivitas',
            'Deskripsi',
            'Modul',
            'IP Address',
            'User Agent'
        ];
    }

    public function map($log): array
    {
        return [
            $log->created_at->format('Y-m-d H:i:s'),
            $log->user ? $log->user->name : 'Guest/System',
            $log->user ? ucfirst($log->user->role) : '-',
            $log->activity,
            $log->description,
            $log->module ?: 'General',
            $log->ip_address,
            $log->user_agent
        ];
    }
}

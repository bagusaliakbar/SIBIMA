<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait HasActivityLog
{
    protected static function bootHasActivityLog()
    {
        static::created(function ($model) {
            self::logActivity($model, 'Created', "Menambahkan data " . self::getModelLabel($model));
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);
            
            if (empty($changes)) return;

            $oldData = array_intersect_key($model->getOriginal(), $changes);
            
            self::logActivity($model, 'Updated', "Memperbarui data " . self::getModelLabel($model), [
                'before' => $oldData,
                'after' => $changes
            ]);
        });

        static::deleted(function ($model) {
            self::logActivity($model, 'Deleted', "Menghapus data " . self::getModelLabel($model));
        });
    }

    protected static function logActivity($model, $activity, $description, $properties = null)
    {
        if (!Auth::check()) return;

        $module = self::getModelLabel($model);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => $activity,
            'description' => $description,
            'module' => $module,
            'subject_type' => get_class($model),
            'subject_id' => $model->id,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    protected static function getModelLabel($model)
    {
        $className = class_basename($model);
        
        $labels = [
            'Thesis' => 'Skripsi',
            'User' => 'Pengguna',
            'SeminarSchedule' => 'Jadwal Seminar',
            'ThesisDefenseSchedule' => 'Jadwal Sidang',
            'Wave' => 'Gelombang',
            'SeminarScheduleDetail' => 'Detail Seminar',
            'ThesisDefenseScheduleDetail' => 'Detail Sidang',
            'SeminarRevision' => 'Revisi Seminar',
            'ThesisDefenseRevision' => 'Revisi Sidang',
        ];

        return $labels[$className] ?? $className;
    }
}

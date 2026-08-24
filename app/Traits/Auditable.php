<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait Auditable
{
    /**
     * Boot the Auditable trait for a model.
     *
     * @return void
     */
    public static function bootAuditable()
    {
        static::updated(function ($model) {
            $oldData = [];
            $newData = [];

            // Identify which fields actually changed
            foreach ($model->getChanges() as $key => $value) {
                // Ignore timestamp fields if they are the only things that changed
                if (in_array($key, ['updated_at', 'created_at', 'last_login_at'])) {
                    continue;
                }

                $oldData[$key] = $model->getOriginal($key);
                $newData[$key] = $value;
            }

            // Only log if there are actual data changes
            if (!empty($newData)) {
                ActivityLog::log(
                    activity: 'Data Diperbarui',
                    description: 'Terdapat pembaruan data pada ' . class_basename($model) . ' ID: ' . $model->id,
                    module: class_basename($model),
                    subject: $model,
                    properties: [
                        'before' => $oldData,
                        'after' => $newData,
                    ]
                );
            }
        });

        static::created(function ($model) {
            ActivityLog::log(
                activity: 'Data Dibuat',
                description: 'Data baru berhasil ditambahkan pada ' . class_basename($model) . ' ID: ' . $model->id,
                module: class_basename($model),
                subject: $model,
                properties: [
                    'after' => $model->getAttributes(),
                ]
            );
        });

        static::deleted(function ($model) {
            ActivityLog::log(
                activity: 'Data Dihapus',
                description: 'Data telah dihapus dari ' . class_basename($model) . ' ID: ' . $model->id,
                module: class_basename($model),
                subject: $model,
                properties: [
                    'before' => $model->getAttributes(),
                ]
            );
        });
    }
}

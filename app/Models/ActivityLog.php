<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity',
        'description',
        'module',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the user that performed the activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject of the activity.
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Static helper to log an activity.
     */
    public static function log($activity, $description = null, $module = null, $subject = null, $properties = null)
    {
        return self::create([
            'user_id'      => \Illuminate\Support\Facades\Auth::id(),
            'activity'     => $activity,
            'description'  => $description,
            'module'       => $module,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject ? $subject->id : null,
            'properties'   => $properties,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);
    }
}

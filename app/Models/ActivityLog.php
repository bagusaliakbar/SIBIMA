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
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the user that performed the activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Static helper to log an activity.
     */
    public static function log($activity, $description = null, $module = null)
    {
        return self::create([
            'user_id'     => \Illuminate\Support\Facades\Auth::id(),
            'activity'    => $activity,
            'description' => $description,
            'module'      => $module,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }
}

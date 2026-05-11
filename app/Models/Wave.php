<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wave extends Model
{
    protected $fillable = ['name', 'is_active', 'description', 'start_date', 'end_date'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    public static function active()
    {
        $today = now()->toDateString();
        return self::where('is_active', true)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();
    }
}

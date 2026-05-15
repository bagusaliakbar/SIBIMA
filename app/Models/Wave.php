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
            ->orderBy('id', 'desc')
            ->first();
    }

    public static function getCurrentActive()
    {
        return self::active() 
            ?: self::where('is_active', true)->orderBy('end_date', 'desc')->first() 
            ?: self::orderBy('id', 'desc')->first();
    }

    public function defenseApplications()
    {
        return $this->hasMany(ThesisDefenseApplication::class);
    }

    public function seminarApplications()
    {
        return $this->hasMany(SeminarApplication::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThesisDefenseTemplate extends Model
{
    protected $fillable = [
        'title',
        'file_path',
        'original_name',
        'is_active',
    ];
}

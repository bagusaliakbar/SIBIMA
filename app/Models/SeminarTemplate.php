<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeminarTemplate extends Model
{
    protected $fillable = [
        'title',
        'file_path',
        'original_name',
        'is_active',
    ];
}

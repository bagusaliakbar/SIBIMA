<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterSetting extends Model
{
    protected $fillable = [
        'type',
        'title',
        'format',
        'last_number'
    ];
}

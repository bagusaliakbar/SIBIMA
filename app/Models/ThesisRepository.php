<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThesisRepository extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'name',
        'year',
        'title',
        'abstract',
        'pembimbing1',
        'pembimbing2',
        'file_path',
    ];
}

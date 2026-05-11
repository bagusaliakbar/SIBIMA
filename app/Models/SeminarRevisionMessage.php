<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeminarRevisionMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'seminar_revision_id',
        'sender_id',
        'message',
        'file_path',
    ];

    public function revision()
    {
        return $this->belongsTo(SeminarRevision::class, 'seminar_revision_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThesisDefenseRevisionMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'thesis_defense_revision_id',
        'sender_id',
        'message',
        'file_path',
    ];

    public function revision()
    {
        return $this->belongsTo(ThesisDefenseRevision::class, 'thesis_defense_revision_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}

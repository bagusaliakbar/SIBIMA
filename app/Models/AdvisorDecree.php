<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AdvisorDecree extends Model
{
    use HasFactory;

    protected $fillable = [
        'decree_number',
        'title',
        'academic_year',
        'semester',
        'target_type',
        'dosen_id',
        'wave_id',
        'decree_date',
        'signatory_title',
        'signatory_name',
        'signatory_identifier',
        'signer_user_id',
        'theses_data',
        'total_students',
        'verification_token',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'decree_date' => 'date',
        'theses_data' => 'array',
        'total_students' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function signer()
    {
        return $this->belongsTo(User::class, 'signer_user_id');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function wave()
    {
        return $this->belongsTo(Wave::class, 'wave_id');
    }

    public function getFormattedDecreeDateAttribute(): string
    {
        return $this->decree_date ? $this->decree_date->locale('id')->translatedFormat('d F Y') : '-';
    }

    public function getVerificationUrlAttribute(): string
    {
        return route('sk-pembimbing.verify', $this->verification_token);
    }
}

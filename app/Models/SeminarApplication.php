<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeminarApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'thesis_id',
        'wave_id',
        'file_acc_pembimbing',
        'file_pembayaran',
        'file_kartu_bimbingan',
        'file_skripsi',
        'file_formulir',
        'status',
        'admin_feedback',
        'file_reviews',
    ];

    protected $casts = [
        'file_reviews' => 'array',
    ];

    public function thesis()
    {
        return $this->belongsTo(Thesis::class);
    }

    public function wave()
    {
        return $this->belongsTo(Wave::class);
    }
}

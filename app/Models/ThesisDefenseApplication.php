<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThesisDefenseApplication extends Model
{
    protected $fillable = [
        'thesis_id',
        'wave_id',
        'file_formulir',
        'file_transkrip',
        'file_acc_pembimbing',
        'file_logbook',
        'file_pembayaran',
        'file_skripsi',
        'file_ktm',
        'file_pkkmb_univ',
        'file_pkkmb_fak',
        'file_makrab',
        'file_cisco',
        'file_workshop',
        'file_organisasi',
        'file_toefl',
        'file_kewirausahaan',
        'file_tahsin',
        'file_komputer',
        'file_perpus_pinjam',
        'file_perpus_sumbang',
        'file_ijazah',
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

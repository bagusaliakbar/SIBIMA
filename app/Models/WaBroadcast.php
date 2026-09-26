<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaBroadcast extends Model
{
    protected $fillable = [
        'sender_id',
        'title',
        'target_type',
        'target_filter',
        'message_template',
        'delay_seconds',
        'total_recipients',
        'successful_count',
        'failed_count',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'target_filter' => 'array',
        'sent_at' => 'datetime',
        'delay_seconds' => 'integer',
        'total_recipients' => 'integer',
        'successful_count' => 'integer',
        'failed_count' => 'integer',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function logs()
    {
        return $this->hasMany(WaBroadcastLog::class, 'wa_broadcast_id');
    }

    public function getTargetTypeLabelAttribute(): string
    {
        return match ($this->target_type) {
            'mahasiswa_belum_seminar' => 'Mahasiswa Belum Seminar Proposal',
            'mahasiswa_bimbingan_pasif' => 'Mahasiswa Bimbingan Pasif / Mangkir',
            'mahasiswa_kritis' => 'Mahasiswa Semester Kritis (13-14+)',
            'mahasiswa_belum_sidang' => 'Mahasiswa Belum Mendaftar Sidang',
            'mahasiswa_belum_skripsi' => 'Mahasiswa Belum Mengajukan Judul',
            'dosen_pembimbing_aktif' => 'Seluruh Dosen Pembimbing Aktif',
            'dosen_penguji_gelombang' => 'Dosen Penguji di Gelombang Tertentu',
            'custom' => 'Daftar Kontak Pilihan Khusus',
            default => ucwords(str_replace('_', ' ', $this->target_type)),
        };
    }
}

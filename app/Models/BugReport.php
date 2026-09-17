<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BugReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'title',
        'category',
        'severity',
        'status',
        'page_url',
        'description',
        'steps_to_reproduce',
        'device_info',
        'attachment_path',
        'admin_notes',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Pelapor bug (Mahasiswa / Dosen / User)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Admin / Kaprodi yang menyelesaikan / merespons
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Scope untuk status terbuka
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope untuk status sedang diproses
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope untuk status selesai
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Label status bahasa Indonesia
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'Menunggu Review',
            'in_progress' => 'Sedang Ditangani',
            'resolved' => 'Selesai / Teratasi',
            'rejected' => 'Ditolak / Bukan Bug',
            default => ucfirst($this->status),
        };
    }

    /**
     * Label severity bahasa Indonesia
     */
    public function getSeverityLabelAttribute(): string
    {
        return match ($this->severity) {
            'low' => 'Rendah (Minor)',
            'medium' => 'Sedang (Normal)',
            'high' => 'Tinggi (Mayor)',
            'critical' => 'Kritis (Fatal)',
            default => ucfirst($this->severity),
        };
    }

    /**
     * Label category bahasa Indonesia
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'ui_ux' => 'Tampilan / Desain (UI/UX)',
            'functionality' => 'Fungsi / Fitur Eror',
            'performance' => 'Kinerja / Lambat',
            'security' => 'Keamanan / Akses',
            'data_error' => 'Ketidaksesuaian Data',
            'other' => 'Lainnya',
            default => ucfirst($this->category),
        };
    }

    /**
     * URL Lampiran screenshot
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }

        return Storage::url($this->attachment_path);
    }
}

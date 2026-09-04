<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuidanceDocument extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'file_path',
        'original_name',
        'file_size',
        'file_extension',
        'download_count',
        'is_active',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'download_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if (!$bytes || $bytes <= 0) {
            return '—';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1) . ' ' . $units[$i];
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'panduan_skripsi' => 'Buku Panduan',
            'format_template' => 'Template Dokumen',
            'pedoman_bimbingan' => 'Pedoman Bimbingan',
            'lainnya' => 'Berkas Pendukung',
            default => 'Umum',
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'panduan_skripsi' => 'indigo',
            'format_template' => 'emerald',
            'pedoman_bimbingan' => 'amber',
            'lainnya' => 'slate',
            default => 'slate',
        };
    }

    public function getIconTypeAttribute(): string
    {
        $ext = strtolower($this->file_extension ?: pathinfo($this->original_name, PATHINFO_EXTENSION));

        if (in_array($ext, ['pdf'])) {
            return 'pdf';
        } elseif (in_array($ext, ['doc', 'docx', 'odt', 'rtf'])) {
            return 'word';
        } elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) {
            return 'excel';
        } elseif (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])) {
            return 'archive';
        }

        return 'file';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCategory($query, $category)
    {
        if (!empty($category) && $category !== 'all') {
            return $query->where('category', $category);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            return $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%");
            });
        }
        return $query;
    }
}

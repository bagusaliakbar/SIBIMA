<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'category',
        'target_role',
        'order',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForRole($query, $role = null)
    {
        if (!$role) {
            return $query->whereIn('target_role', ['all', 'mahasiswa']);
        }

        if (in_array($role, ['admin', 'kaprodi'])) {
            return $query;
        }

        return $query->where(function ($q) use ($role) {
            $q->where('target_role', 'all')
              ->orWhere('target_role', $role);
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    public static function categories(): array
    {
        return [
            'bimbingan' => [
                'name' => 'Bimbingan & Logbook',
                'color' => 'orange',
            ],
            'seminar' => [
                'name' => 'Seminar Proposal (UP)',
                'color' => 'emerald',
            ],
            'sidang' => [
                'name' => 'Sidang Akhir & Nilai',
                'color' => 'indigo',
            ],
            'revisi' => [
                'name' => 'Revisi & Berkas',
                'color' => 'amber',
            ],
            'teknis' => [
                'name' => 'Akun & Teknis',
                'color' => 'blue',
            ],
            'umum' => [
                'name' => 'Umum',
                'color' => 'slate',
            ],
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categories()[$this->category]['name'] ?? ucfirst($this->category);
    }
}

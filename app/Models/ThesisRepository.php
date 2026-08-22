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

    /**
     * Auto-detect topic categories based on title keywords.
     */
    public function getTopicBadgeAttribute(): array
    {
        $title = strtolower($this->title ?? '');

        if (str_contains($title, 'android') || str_contains($title, 'mobile') || str_contains($title, 'flutter') || str_contains($title, 'ios')) {
            return ['label' => 'Mobile App', 'color' => 'emerald', 'bg' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800'];
        }
        if (str_contains($title, 'machine learning') || str_contains($title, 'deep learning') || str_contains($title, 'klasifikasi') || str_contains($title, 'clustering') || str_contains($title, 'k-means') || str_contains($title, 'naive bayes') || str_contains($title, 'neural network') || str_contains($title, 'cnn') || str_contains($title, 'nlp') || str_contains($title, 'yolo')) {
            return ['label' => 'AI / Data Science', 'color' => 'purple', 'bg' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300 border-purple-200 dark:border-purple-800'];
        }
        if (str_contains($title, 'spk') || str_contains($title, 'pendukung keputusan') || str_contains($title, 'ahp') || str_contains($title, 'saw') || str_contains($title, 'topsis') || str_contains($title, 'smart') || str_contains($title, 'profile matching') || str_contains($title, 'moora') || str_contains($title, 'vikor')) {
            return ['label' => 'SPK / Keputusan', 'color' => 'amber', 'bg' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border-amber-200 dark:border-amber-800'];
        }
        if (str_contains($title, 'ui/ux') || str_contains($title, 'ui ') || str_contains($title, 'ux ') || str_contains($title, 'human-centered') || str_contains($title, 'human centered') || str_contains($title, 'design thinking') || str_contains($title, 'usability')) {
            return ['label' => 'UI/UX Design', 'color' => 'rose', 'bg' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border-rose-200 dark:border-rose-800'];
        }
        if (str_contains($title, 'iot') || str_contains($title, 'internet of things') || str_contains($title, 'arduino') || str_contains($title, 'raspberry') || str_contains($title, 'sensor') || str_contains($title, 'mikrokontroler') || str_contains($title, 'jaringan') || str_contains($title, 'keamanan')) {
            return ['label' => 'IoT & Hardware', 'color' => 'sky', 'bg' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border-sky-200 dark:border-sky-800'];
        }
        if (str_contains($title, 'e-commerce') || str_contains($title, 'penjualan') || str_contains($title, 'marketplace') || str_contains($title, 'toko online') || str_contains($title, 'pos ') || str_contains($title, 'kasir')) {
            return ['label' => 'E-Commerce / POS', 'color' => 'blue', 'bg' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border-blue-200 dark:border-blue-800'];
        }
        if (str_contains($title, 'web') || str_contains($title, 'website') || str_contains($title, 'portal') || str_contains($title, 'sistem informasi')) {
            return ['label' => 'Web App / SI', 'color' => 'indigo', 'bg' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800'];
        }

        return ['label' => 'Sistem Informasi', 'color' => 'slate', 'bg' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700'];
    }
}

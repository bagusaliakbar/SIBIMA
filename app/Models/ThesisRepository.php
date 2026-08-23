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
     * Topic definitions with regex word boundaries and matching keywords.
     */
    public static function getTopicDefinitions(): array
    {
        return [
            'ai' => [
                'label' => 'AI & Data Science',
                'color' => 'purple',
                'bg' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/70 dark:text-purple-300 border-purple-200 dark:border-purple-800/60',
                'weight' => 20,
                'pattern' => '/\b(machine learning|deep learning|data mining|klasifikasi|clustering|k-means|kmeans|naive bayes|svm|support vector machine|random forest|decision tree|c4\.5|neural network|jaringan syaraf|jst|cnn|rnn|lstm|nlp|natural language processing|text mining|sentiment|sentimen|yolo|computer vision|pengolahan citra|image processing|fuzzy|algoritma genetika|genetic algorithm|knn|k-nearest|regresi|prediksi|forecasting|backpropagation)\b/i',
                'keywords' => ['machine learning', 'deep learning', 'data mining', 'klasifikasi', 'clustering', 'k-means', 'naive bayes', 'svm', 'decision tree', 'neural network', 'cnn', 'nlp', 'yolo', 'fuzzy', 'algoritma genetika']
            ],
            'spk' => [
                'label' => 'SPK / Keputusan',
                'color' => 'amber',
                'bg' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/70 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
                'weight' => 20,
                'pattern' => '/\b(spk|dss|decision support system|pendukung keputusan|pengambil keputusan|\bahp\b|analytic hierarchy|\bsaw\b|simple additive weighting|topsis|profile matching|moora|vikor|mabac|promethee|electre|copras|weighted product|\bwp\b|aras|edas|roc|rank order centroid|metode smart)\b/i',
                'keywords' => ['spk', 'pendukung keputusan', 'ahp', 'saw', 'topsis', 'profile matching', 'moora', 'vikor', 'mabac', 'promethee', 'electre', 'copras', 'weighted product']
            ],
            'iot' => [
                'label' => 'IoT & Hardware',
                'color' => 'sky',
                'bg' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/70 dark:text-sky-300 border-sky-200 dark:border-sky-800/60',
                'weight' => 18,
                'pattern' => '/\b(iot|internet of things|arduino|raspberry|esp8266|esp32|mikrokontroler|microcontroller|sensor|rfid|lora|smart home|smart farming|jaringan|mikrotik|cisco|routing|firewall|intrusion detection|ids|vpn)\b/i',
                'keywords' => ['iot', 'internet of things', 'arduino', 'raspberry', 'sensor', 'mikrokontroler', 'jaringan', 'mikrotik', 'cisco', 'keamanan']
            ],
            'ui_ux' => [
                'label' => 'UI/UX & HCD',
                'color' => 'rose',
                'bg' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/70 dark:text-rose-300 border-rose-200 dark:border-rose-800/60',
                'weight' => 16,
                'pattern' => '/\b(ui\/ux|ui\\\\ux|user interface|user experience|human-centered|human centered|hcd|design thinking|usability|heuristic evaluation|sus|system usability scale|user persona|wireframe|prototyping|user-centered)\b/i',
                'keywords' => ['ui/ux', 'user interface', 'user experience', 'human-centered', 'design thinking', 'usability', 'user persona', 'prototyping']
            ],
            'mobile' => [
                'label' => 'Mobile App',
                'color' => 'emerald',
                'bg' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',
                'weight' => 15,
                'pattern' => '/\b(android|mobile|flutter|react native|kotlin|swift|ios|smartphone|telepon pintar|perangkat bergerak)\b/i',
                'keywords' => ['android', 'mobile', 'flutter', 'react native', 'kotlin', 'swift', 'ios', 'smartphone']
            ],
            'ecommerce' => [
                'label' => 'E-Commerce / POS',
                'color' => 'blue',
                'bg' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/70 dark:text-blue-300 border-blue-200 dark:border-blue-800/60',
                'weight' => 14,
                'pattern' => '/\b(e-commerce|ecommerce|e-bisnis|ebusiness|marketplace|toko online|point of sale|\bpos\b|kasir|penjualan online|pemesanan makanan|reservasi tiket)\b/i',
                'keywords' => ['e-commerce', 'ecommerce', 'marketplace', 'toko online', 'point of sale', 'pos', 'kasir', 'penjualan online']
            ],
            'web' => [
                'label' => 'Web App & SI',
                'color' => 'indigo',
                'bg' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/70 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800/60',
                'weight' => 10,
                'pattern' => '/\b(web|website|portal|sistem informasi|aplikasi berbasis web|web-based|dashboard|inventaris|kepegawaian|akademik|arsip|pendaftaran|peminjaman|e-office|surat menyurat|pelayanan|monitoring)\b/i',
                'keywords' => ['web', 'website', 'portal', 'sistem informasi', 'aplikasi web', 'dashboard', 'monitoring', 'inventaris', 'kepegawaian']
            ],
        ];
    }

    /**
     * Auto-detect topic categories based on title keywords with smart regex word boundaries and scoring.
     */
    public static function detectTopic(?string $title): array
    {
        $title = strtolower(trim($title ?? ''));
        $definitions = self::getTopicDefinitions();

        $bestMatch = null;
        $maxScore = 0;

        foreach ($definitions as $key => $cat) {
            $count = preg_match_all($cat['pattern'], $title, $matches);
            if ($count > 0) {
                $score = $cat['weight'] + ($count * 5);
                if ($score > $maxScore) {
                    $maxScore = $score;
                    $bestMatch = [
                        'key' => $key,
                        'label' => $cat['label'],
                        'color' => $cat['color'],
                        'bg' => $cat['bg']
                    ];
                }
            }
        }

        if ($bestMatch) {
            return $bestMatch;
        }

        return [
            'key' => 'general',
            'label' => 'Sistem Informasi',
            'color' => 'slate',
            'bg' => 'bg-slate-100 text-slate-700 dark:bg-slate-800/90 dark:text-slate-300 border-slate-200 dark:border-slate-700'
        ];
    }

    /**
     * Accessor for topic badge.
     */
    public function getTopicBadgeAttribute(): array
    {
        return self::detectTopic($this->title);
    }
}

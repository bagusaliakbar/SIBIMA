<?php

namespace App\Services;

class JournalAiService
{
    /**
     * Common Indonesian & English academic filler words/stopwords to filter out
     * when extracting core research keywords from thesis titles.
     */
    protected array $stopwords = [
        // Indonesian academic filler words
        'rancang', 'bangun', 'rancang bangun', 'perancangan', 'pembuatan', 'pengembangan',
        'implementasi', 'penerapan', 'analisis', 'analisa', 'komparasi', 'perbandingan',
        'sistem', 'informasi', 'sistem informasi', 'aplikasi', 'program', 'prototipe',
        'berbasis', 'web', 'website', 'mobile', 'android', 'ios', 'desktop',
        'menggunakan', 'dengan', 'metode', 'algoritma', 'teknik', 'model', 'arsitektur',
        'studi', 'kasus', 'studi kasus', 'pada', 'di', 'dalam', 'untuk', 'terhadap',
        'dan', 'atau', 'yang', 'dari', 'ke', 'oleh', 'secara', 'sebagai', 'pada studi',
        'pt', 'cv', 'ud', 'instansi', 'dinas', 'kantor', 'universitas', 'sekolah', 'desa',
        'kelurahan', 'kecamatan', 'kabupaten', 'kota', 'subang', 'fasilkom', 'unsub',
        // English academic filler words
        'design', 'development', 'implementation', 'analysis', 'comparative', 'study',
        'system', 'information', 'application', 'based', 'using', 'algorithm', 'method',
        'framework', 'case', 'study of', 'at', 'in', 'on', 'for', 'with', 'and', 'the',
        'a', 'an', 'of', 'to', 'by', 'from', 'as', 'an analysis of', 'a case study of'
    ];

    /**
     * Extract clean research keywords from a thesis title.
     *
     * @param string $title
     * @return array{query: string, chips: array, raw_title: string}
     */
    public function extractKeywords(string $title): array
    {
        $rawTitle = trim($title);
        if ($rawTitle === '') {
            return [
                'query' => '',
                'chips' => [],
                'raw_title' => '',
            ];
        }

        // Preserve important acronyms (e.g. AHP, TOPSIS, CNN, LSTM, IoT, UI/UX, REST API, KNN, SVM)
        $clean = preg_replace('/[^\p{L}\p{N}\s\-\/]/u', ' ', $rawTitle);
        $clean = preg_replace('/\s+/', ' ', $clean);

        // Standardize words
        $words = explode(' ', $clean);
        $filteredWords = [];

        $lowerStopwords = array_map('mb_strtolower', $this->stopwords);

        // Multi-word phrase cleanup
        $normalizedText = ' ' . mb_strtolower($clean) . ' ';
        foreach ($lowerStopwords as $phrase) {
            if (str_contains($phrase, ' ')) {
                $normalizedText = str_replace(' ' . $phrase . ' ', ' ', $normalizedText);
            }
        }

        $tokens = array_filter(explode(' ', trim($normalizedText)));
        foreach ($tokens as $token) {
            $tokenLower = mb_strtolower($token);
            if (mb_strlen($tokenLower) < 3 && !in_array($tokenLower, ['ai', 'ui', 'ux', 'it', 'vr', 'ar'])) {
                continue;
            }
            if (!in_array($tokenLower, $lowerStopwords)) {
                $filteredWords[] = $token;
            }
        }

        // Deduplicate while preserving order
        $uniqueWords = array_values(array_unique($filteredWords));

        // Detect high-priority academic method acronyms & keywords
        $priorityTerms = [
            'ahp', 'topsis', 'saw', 'wp', 'knn', 'svm', 'cnn', 'lstm', 'rnn', 'nlp', 'iot',
            'api', 'gis', 'spk', 'sdlc', 'k-means', 'naive', 'bayes', 'yolo', 'bert',
            'fuzzy', 'sentimen', 'klasifikasi', 'clustering', 'prediksi'
        ];

        $priorityMatches = [];
        $regularMatches = [];

        foreach ($uniqueWords as $w) {
            if (in_array(mb_strtolower($w), $priorityTerms)) {
                $priorityMatches[] = $w;
            } else {
                $regularMatches[] = $w;
            }
        }

        // Merge keeping priority terms first, max 8 words
        $mergedWords = array_values(array_unique(array_merge($regularMatches, $priorityMatches)));
        if (count($mergedWords) > 8) {
            // Ensure priority terms are not dropped
            $regularKeepCount = max(3, 8 - count($priorityMatches));
            $mergedWords = array_merge(array_slice($regularMatches, 0, $regularKeepCount), $priorityMatches);
            $mergedWords = array_values(array_unique($mergedWords));
        }

        $topKeywords = array_slice($mergedWords, 0, 8);
        $query = implode(' ', $topKeywords);

        // Group into sensible concept chips
        $chips = [];
        foreach ($topKeywords as $w) {
            // Capitalize acronyms or format nicely
            if (in_array(mb_strtolower($w), ['ahp', 'topsis', 'saw', 'wp', 'knn', 'svm', 'cnn', 'lstm', 'rnn', 'nlp', 'iot', 'api', 'gis', 'spk', 'sdlc', 'k-means', 'naive', 'bayes', 'yolo', 'bert', 'ui', 'ux'])) {
                $chips[] = mb_strtoupper($w);
            } else {
                $chips[] = ucfirst(mb_strtolower($w));
            }
        }

        return [
            'query' => $query ?: $rawTitle,
            'chips' => array_values(array_unique($chips)),
            'raw_title' => $rawTitle,
        ];
    }

    /**
     * Generate an AI Quick Summary / TL;DR (3 key points) from a research paper abstract.
     *
     * @param string|null $abstract
     * @param string $title
     * @return array{has_tldr: bool, problem: string, method: string, result: string}
     */
    public function generateTldr(?string $abstract, string $title = ''): array
    {
        $text = trim($abstract ?? '');
        if (mb_strlen($text) < 40) {
            return [
                'has_tldr' => false,
                'problem' => 'Fokus penelitian berkaitan dengan topik ' . ($title ?: 'ini') . '.',
                'method' => 'Menggunakan pendekatan analitis dan pengujian empiris.',
                'result' => 'Memberikan solusi serta luaran terukur sesuai tujuan penelitian.',
            ];
        }

        // Normalize whitespaces & remove HTML tags if any
        $text = strip_tags($text);
        $text = preg_replace('/\s+/', ' ', $text);

        // Split sentences safely using fixed-length lookbehind
        $sentences = preg_split('/(?<=[.!?])\s+(?=[A-Z0-9])/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $sentences = array_values(array_filter(array_map('trim', $sentences)));

        if (empty($sentences)) {
            $sentences = [$text];
        }

        $problem = null;
        $method = null;
        $result = null;

        // Linguistic indicator patterns
        $problemPatterns = [
            'bertujuan', 'tujuan dari', 'penelitian ini bertujuan', 'permasalahan', 'masalah utama',
            'isu yang', 'latar belakang', 'fokus riset', 'pada penelitian ini', 'untuk mengatasi',
            'aims to', 'the aim of', 'objective of', 'this study investigates', 'this paper addresses',
            'problem of', 'to solve', 'focuses on', 'the purpose of', 'we address', 'challenges in'
        ];

        $methodPatterns = [
            'metode yang digunakan', 'menggunakan metode', 'menggunakan algoritma', 'pendekatan',
            'dengan metode', 'dirancang dengan', 'arsitektur', 'framework', 'sistem ini dibangun',
            'tahapan penelitian', 'metodologi', 'we propose', 'proposed method', 'using algorithm',
            'utilized', 'implemented with', 'based on', 'we developed', 'methodology includes',
            'deep learning', 'machine learning', 'neural network', 'classification', 'clustering'
        ];

        $resultPatterns = [
            'hasil penelitian', 'hasil pengujian', 'akurasi', 'menunjukkan bahwa', 'diperoleh',
            'berdasarkan hasil', 'kesimpulan', 'ditemukan bahwa', 'dapat meningkatkan', 'efektivitas',
            'the results show', 'results indicate', 'achieved an accuracy', 'demonstrated that',
            'findings suggest', 'outperformed', 'our results', 'concluded that', 'performance evaluation',
            'success rate', 'precision'
        ];

        // Match sentences by semantic scoring
        foreach ($sentences as $idx => $s) {
            $sLower = mb_strtolower($s);

            if (!$problem && $this->matchesAny($sLower, $problemPatterns)) {
                $problem = $this->cleanSentence($s);
                continue;
            }

            if (!$method && $this->matchesAny($sLower, $methodPatterns)) {
                $method = $this->cleanSentence($s);
                continue;
            }

            if (!$result && $this->matchesAny($sLower, $resultPatterns)) {
                $result = $this->cleanSentence($s);
                continue;
            }
        }

        $totalSentences = count($sentences);

        // Fallbacks for missing sections based on standard abstract narrative flow:
        // Beginning = Problem/Objective, Middle = Methodology, End = Results/Outcome
        if (!$problem) {
            $problem = $this->cleanSentence($sentences[0]);
        }

        if (!$method) {
            if ($totalSentences >= 3) {
                $midIdx = (int) floor($totalSentences / 2);
                $method = $this->cleanSentence($sentences[$midIdx]);
            } elseif ($totalSentences === 2) {
                $method = $this->cleanSentence($sentences[1]);
            } else {
                $method = 'Menerapkan metodologi perancangan sistem dan pengujian komputasi yang terstruktur.';
            }
        }

        if (!$result) {
            if ($totalSentences >= 2) {
                $result = $this->cleanSentence($sentences[$totalSentences - 1]);
            } else {
                $result = 'Memberikan kontribusi dan temuan terverifikasi yang relevan dengan bidang penelitian.';
            }
        }

        return [
            'has_tldr' => true,
            'problem' => $problem,
            'method' => $method,
            'result' => $result,
        ];
    }

    /**
     * Extract a search query to find similar papers based on item metadata.
     *
     * @param array $item
     * @return string
     */
    public function extractSimilarQuery(array $item): string
    {
        // 1. If concepts exist, use the top 2-3 concept names
        if (!empty($item['concepts']) && is_array($item['concepts'])) {
            $conceptNames = array_slice(array_column($item['concepts'], 'name'), 0, 3);
            if (!empty($conceptNames)) {
                return implode(' ', $conceptNames);
            }
        }

        // 2. Otherwise extract core keywords from the title
        $extracted = $this->extractKeywords($item['title'] ?? '');
        return $extracted['query'] ?: ($item['title'] ?? '');
    }

    /**
     * Check if text contains any of the pattern strings.
     */
    protected function matchesAny(string $text, array $patterns): bool
    {
        foreach ($patterns as $p) {
            if (str_contains($text, $p)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Clean and format sentence for presentation.
     */
    protected function cleanSentence(string $sentence): string
    {
        $s = trim($sentence);
        $s = rtrim($s, ".,;:\t\n\r");
        return $s . '.';
    }
}

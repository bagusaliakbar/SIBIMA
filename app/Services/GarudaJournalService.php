<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GarudaJournalService
{
    protected string $baseUrl = 'https://garuda.kemdiktisaintek.go.id/documents';
    protected int $timeout = 10;
    protected int $cacheTtl = 86400; // 24 hours

    /**
     * Search publications in GARUDA (Garba Rujukan Digital - Kemdiktisaintek).
     */
    public function search(string $query, array $options = []): array
    {
        $query = trim($query);
        if ($query === '') {
            return [
                'success' => true,
                'count' => 0,
                'total_pages' => 0,
                'current_page' => 1,
                'per_page' => 10,
                'data' => [],
                'error' => null,
            ];
        }

        $page = max(1, (int) ($options['page'] ?? 1));
        $yearFilter = $options['year_filter'] ?? 'all';
        $openAccessOnly = (bool) ($options['open_access_only'] ?? true);
        $perPage = 10; // GARUDA displays 10 documents per page

        $cacheKey = 'garuda_search_' . md5(json_encode([
            'q' => strtolower($query),
            'page' => $page,
            'year_filter' => $yearFilter,
            'oa_only' => $openAccessOnly,
        ]));

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($query, $page, $yearFilter, $openAccessOnly, $perPage) {
            return $this->performSearch($query, $page, $yearFilter, $openAccessOnly, $perPage);
        });
    }

    /**
     * Perform the actual HTTP request to GARUDA search and parse response.
     */
    protected function performSearch(string $query, int $page, string $yearFilter, bool $openAccessOnly, int $perPage): array
    {
        try {
            $params = [
                'q' => $query,
                'page' => $page,
            ];

            if ($openAccessOnly) {
                $params['pdf'] = 'on';
            }

            // Year range filters
            $currentYear = (int) date('Y');
            if ($yearFilter === '3_years') {
                $params['from'] = $currentYear - 2;
                $params['to'] = $currentYear;
            } elseif ($yearFilter === '5_years') {
                $params['from'] = $currentYear - 4;
                $params['to'] = $currentYear;
            } elseif ($yearFilter === '10_years') {
                $params['from'] = $currentYear - 9;
                $params['to'] = $currentYear;
            } elseif (is_numeric($yearFilter)) {
                $params['from'] = (int) $yearFilter;
                $params['to'] = (int) $yearFilter;
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 SIBIMA-Academic/1.0',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'id,en-US;q=0.9,en;q=0.8',
                ])
                ->withoutVerifying()
                ->get($this->baseUrl, $params);

            if (!$response->successful()) {
                Log::warning("GARUDA search returned status {$response->status()}", [
                    'query' => $query,
                    'page' => $page,
                ]);

                return [
                    'success' => false,
                    'count' => 0,
                    'total_pages' => 0,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'data' => [],
                    'error' => 'Peladen GARUDA Kemdiktisaintek sedang sibuk atau tidak dapat dijangkau. Silakan coba sesaat lagi.',
                ];
            }

            return $this->parseHtml($response->body(), $page, $perPage);
        } catch (\Throwable $e) {
            Log::error('GARUDA search exception: ' . $e->getMessage(), [
                'query' => $query,
                'page' => $page,
            ]);

            return [
                'success' => false,
                'count' => 0,
                'total_pages' => 0,
                'current_page' => $page,
                'per_page' => $perPage,
                'data' => [],
                'error' => 'Gagal terhubung ke portal GARUDA Kemdiktisaintek. Periksa jaringan Anda atau coba sesaat lagi.',
            ];
        }
    }

    /**
     * Parse HTML response from GARUDA search results page.
     */
    public function parseHtml(string $html, int $page = 1, int $perPage = 10): array
    {
        // 1. Extract total count
        $totalCount = 0;
        if (preg_match('/Found\s+([\d,]+)/i', $html, $m)) {
            $totalCount = (int) str_replace(',', '', $m[1]);
        } elseif (preg_match('/(showing|ditemukan|menampilkan)\s*[:\s]*([\d,]+)/i', $html, $m2)) {
            $totalCount = (int) str_replace(',', '', $m2[2]);
        }

        $totalPages = $totalCount > 0 ? (int) ceil($totalCount / $perPage) : 0;

        // 2. Split into article blocks
        $blocks = preg_split('/<div class="article-item">/i', $html);
        array_shift($blocks); // Remove header / HTML before first article

        $data = [];
        foreach ($blocks as $block) {
            $parsed = $this->parseArticleBlock($block);
            if ($parsed) {
                $data[] = $parsed;
            }
        }

        if ($totalCount === 0 && count($data) > 0) {
            $totalCount = count($data);
            $totalPages = 1;
        }

        return [
            'success' => true,
            'count' => $totalCount,
            'total_pages' => min($totalPages, 100), // Cap at 100 pages for search stability
            'current_page' => $page,
            'per_page' => $perPage,
            'data' => $data,
            'error' => null,
        ];
    }

    /**
     * Parse a single article HTML block from GARUDA.
     */
    public function parseArticleBlock(string $block): ?array
    {
        // 1. Title and Document Detail ID
        $title = null;
        $detailId = null;
        if (preg_match('/<a[^>]*class="title-article"[^>]*href="\/documents\/detail\/(\d+)"[^>]*>(.*?)<\/a>/is', $block, $tm)) {
            $detailId = $tm[1];
            $title = trim(strip_tags($tm[2]));
        } elseif (preg_match('/<a[^>]*class="title-article"[^>]*href="([^"]+)"[^>]*>(.*?)<\/a>/is', $block, $tm2)) {
            $title = trim(strip_tags($tm2[2]));
            if (preg_match('/\/(\d+)$/', $tm2[1], $idm)) {
                $detailId = $idm[1];
            }
        }

        if (empty($title)) {
            return null;
        }

        // Clean repeated titles in some GARUDA records (e.g. "Title: Title")
        if (str_contains($title, ': ')) {
            $halves = explode(': ', $title, 2);
            if (trim($halves[0]) === trim($halves[1])) {
                $title = trim($halves[0]);
            }
        }

        // 2. Authors
        $authors = [];
        if (preg_match_all('/<a[^>]*class="author-article"[^>]*>(.*?)<\/a>/is', $block, $am)) {
            foreach ($am[1] as $a) {
                $cleaned = trim(strip_tags($a));
                if ($cleaned !== '') {
                    $authors[] = $cleaned;
                }
            }
        }

        // 3. Publisher
        $publisher = null;
        if (preg_match('/Publisher\s*:\s*(?:<\/i>\s*)?<xmp class="subtitle-article">\s*(.*?)\s*<\/xmp>/is', $block, $pm)) {
            $publisher = trim(strip_tags($pm[1]));
        }

        // 4. Subtitle / Journal / Volume / Year
        $venue = 'Jurnal Nasional (GARUDA / SINTA)';
        $year = null;
        if (preg_match_all('/<xmp class="subtitle-article">\s*(.*?)\s*<\/xmp>/is', $block, $allXmp)) {
            foreach ($allXmp[1] as $xmpText) {
                $cleaned = trim(strip_tags($xmpText));
                if ($cleaned === '') {
                    continue;
                }
                if ($publisher && $cleaned === $publisher) {
                    continue; // Skip the publisher block
                }

                $venue = $cleaned;
                if (preg_match('/\b(19\d\d|20\d\d)\b/', $cleaned, $ym)) {
                    $year = (int) $ym[1];
                    break;
                }
            }
        }

        // Fallback year search in the article block if not found in venue
        if (!$year && preg_match('/\b(19\d\d|20\d\d)\b/', $block, $ym2)) {
            $year = (int) $ym2[1];
        }

        // 5. Abstract
        $abstract = null;
        if (preg_match('/<xmp class="abstract-article">\s*(.*?)\s*<\/xmp>/is', $block, $abm)) {
            $abstract = trim(strip_tags($abm[1]));
        }

        // 6. Direct PDF link
        $pdfUrl = null;
        if (preg_match('/href="([^"]*(?:download\.garuda\.kemdiktisaintek\.go\.id|article\/download)[^"]*)"/i', $block, $pdfm)) {
            $pdfUrl = html_entity_decode($pdfm[1]);
        }

        // 7. Original Source (OJS page)
        $sourceUrl = null;
        if (preg_match('/<a[^>]*href="([^"]+)"[^>]*>\s*Original Source/i', $block, $srcm)) {
            $sourceUrl = html_entity_decode($srcm[1]);
        }

        // 8. DOI
        $doi = null;
        if (preg_match('/href="(https?:\/\/doi\.org\/[^"]+)"/i', $block, $doim)) {
            $doi = $doim[1];
        }

        // Landing page URL on GARUDA
        $landingPageUrl = $detailId 
            ? "https://garuda.kemdiktisaintek.go.id/documents/detail/{$detailId}"
            : ($sourceUrl ?: ($doi ?: null));

        // Generated Citations
        $citations = $this->generateCitations($title, $authors, $year, $venue, $doi, $landingPageUrl);

        $authorsString = !empty($authors) ? implode(', ', $authors) : 'Penulis Jurnal Nasional';

        // Concepts / Keywords
        $concepts = [
            ['name' => 'Jurnal Nasional'],
            ['name' => 'SINTA'],
        ];
        if ($publisher) {
            $concepts[] = ['name' => $publisher];
        }

        return [
            'id' => 'garuda_' . ($detailId ?: md5($title)),
            'title' => $title,
            'authors' => $authors,
            'authors_string' => $authorsString,
            'year' => $year,
            'venue' => $venue,
            'publisher' => $publisher,
            'doi' => $doi,
            'cited_by_count' => 0,
            'is_oa' => true,
            'pdf_url' => $pdfUrl,
            'landing_page_url' => $landingPageUrl,
            'source_url' => $sourceUrl,
            'abstract' => $abstract,
            'concepts' => $concepts,
            'citations' => $citations,
            'source' => 'garuda',
            'source_label' => 'Jurnal Nasional GARUDA (SINTA)',
        ];
    }

    /**
     * Generate scientific citation formats (APA 7th, IEEE, BibTeX).
     */
    public function generateCitations(string $title, array $authors, ?int $year, ?string $venue, ?string $doi, ?string $landingUrl): array
    {
        $cleanTitle = rtrim($title, '. ');
        $cleanVenue = $venue ?: 'Jurnal Nasional Terakreditasi';
        $yearStr = $year ?: (int) date('Y');
        $targetUrl = $doi ?: ($landingUrl ?: 'https://garuda.kemdiktisaintek.go.id');

        // 1. APA 7th format
        $apaAuthors = [];
        foreach ($authors as $author) {
            $parts = preg_split('/\s+/', trim($author));
            if (count($parts) > 1) {
                $last = array_pop($parts);
                $initials = implode('. ', array_map(fn($p) => mb_substr($p, 0, 1), $parts)) . '.';
                $apaAuthors[] = "{$last}, {$initials}";
            } else {
                $apaAuthors[] = $author;
            }
        }

        $apaAuthorStr = '';
        if (count($apaAuthors) === 1) {
            $apaAuthorStr = $apaAuthors[0];
        } elseif (count($apaAuthors) === 2) {
            $apaAuthorStr = $apaAuthors[0] . ' & ' . $apaAuthors[1];
        } elseif (count($apaAuthors) > 2) {
            $lastAuthor = array_pop($apaAuthors);
            $apaAuthorStr = implode(', ', $apaAuthors) . ', & ' . $lastAuthor;
        } else {
            $apaAuthorStr = 'Penulis Jurnal';
        }

        $apa = "{$apaAuthorStr} ({$yearStr}). {$cleanTitle}. {$cleanVenue}. {$targetUrl}";

        // 2. IEEE format
        $ieeeAuthors = [];
        foreach ($authors as $author) {
            $parts = preg_split('/\s+/', trim($author));
            if (count($parts) > 1) {
                $last = array_pop($parts);
                $initials = implode('. ', array_map(fn($p) => mb_substr($p, 0, 1), $parts)) . '.';
                $ieeeAuthors[] = "{$initials} {$last}";
            } else {
                $ieeeAuthors[] = $author;
            }
        }
        $ieeeAuthorStr = implode(', ', $ieeeAuthors) ?: 'Penulis Jurnal';
        $ieee = "{$ieeeAuthorStr}, \"{$cleanTitle},\" {$cleanVenue}, {$yearStr}. [Online]. Available: {$targetUrl}";

        // 3. BibTeX format
        $firstAuthorLastName = 'Garuda';
        if (!empty($authors[0])) {
            $parts = preg_split('/\s+/', trim($authors[0]));
            $firstAuthorLastName = preg_replace('/[^a-zA-Z]/', '', end($parts)) ?: 'Author';
        }
        $bibtexKey = strtolower($firstAuthorLastName) . $yearStr . 'garuda';
        $bibtexAuthors = implode(' and ', $authors) ?: 'Penulis Jurnal Nasional';

        $bibtex = "@article{{$bibtexKey},\n"
            . "  title = {{$cleanTitle}},\n"
            . "  author = {{$bibtexAuthors}},\n"
            . "  journal = {{$cleanVenue}},\n"
            . "  year = {{$yearStr}},\n"
            . "  url = {{$targetUrl}}\n"
            . "}";

        return [
            'apa' => $apa,
            'ieee' => $ieee,
            'bibtex' => $bibtex,
        ];
    }
}

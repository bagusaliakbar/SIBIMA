<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DoajJournalService
{
    protected string $baseUrl = 'https://doaj.org/api/search/articles/';
    protected string $userAgent = 'SIBIMA-Fasilkom-UNSUB/1.0 (mailto:sibima@unsub.ac.id)';
    protected int $cacheTtl = 86400; // 24 hours

    /**
     * Search academic works from DOAJ (Directory of Open Access Journals).
     *
     * @param string $query
     * @param array $options [page, per_page, year_filter, sort]
     * @return array
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
                'per_page' => 12,
                'data' => [],
                'error' => null,
            ];
        }

        $page = max(1, (int) ($options['page'] ?? 1));
        $perPage = min(30, max(5, (int) ($options['per_page'] ?? 12)));
        $yearFilter = $options['year_filter'] ?? 'all';
        $sort = $options['sort'] ?? 'relevance';

        $cacheKey = 'doaj_search_' . md5(json_encode([
            'q' => strtolower($query),
            'page' => $page,
            'per_page' => $perPage,
            'year' => $yearFilter,
            'sort' => $sort,
        ]));

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($query, $page, $perPage, $yearFilter, $sort) {
            return $this->performSearch($query, $page, $perPage, $yearFilter, $sort);
        });
    }

    /**
     * Perform the actual HTTP request to DOAJ API.
     */
    protected function performSearch(string $query, int $page, int $perPage, string $yearFilter, string $sort): array
    {
        try {
            // Build query with Lucene syntax for year filter if applicable
            $fullQuery = $query;
            $currentYear = (int) date('Y');

            if ($yearFilter === '3_years') {
                $fullQuery .= ' AND bibjson.year:[' . ($currentYear - 2) . ' TO ' . $currentYear . ']';
            } elseif ($yearFilter === '5_years') {
                $fullQuery .= ' AND bibjson.year:[' . ($currentYear - 4) . ' TO ' . $currentYear . ']';
            } elseif ($yearFilter === '10_years') {
                $fullQuery .= ' AND bibjson.year:[' . ($currentYear - 9) . ' TO ' . $currentYear . ']';
            } elseif (is_numeric($yearFilter) && strlen($yearFilter) === 4) {
                $fullQuery .= ' AND bibjson.year:' . $yearFilter;
            }

            $endpoint = $this->baseUrl . rawurlencode($fullQuery);

            $queryParams = [
                'page' => $page,
                'pageSize' => $perPage,
            ];

            if ($sort === 'newest') {
                $queryParams['sort'] = 'created_date:desc';
            }

            $response = Http::timeout(10)
                ->retry(2, 500)
                ->withHeaders([
                    'User-Agent' => $this->userAgent,
                    'Accept' => 'application/json',
                ])
                ->get($endpoint, $queryParams);

            if (!$response->successful()) {
                Log::warning('DOAJ API request failed', [
                    'status' => $response->status(),
                    'body' => Str::limit($response->body(), 200),
                ]);

                return [
                    'success' => false,
                    'count' => 0,
                    'total_pages' => 0,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'data' => [],
                    'error' => 'Gagal mengambil data jurnal dari DOAJ. Silakan coba sesaat lagi.',
                ];
            }

            $payload = $response->json();
            $totalCount = (int) ($payload['total'] ?? 0);
            $totalPages = (int) ceil($totalCount / $perPage);
            $results = $payload['results'] ?? [];

            $formattedData = array_map(function ($item) {
                return $this->formatWorkItem($item);
            }, $results);

            return [
                'success' => true,
                'count' => $totalCount,
                'total_pages' => min($totalPages, 100), // DOAJ API practical pagination ceiling
                'current_page' => $page,
                'per_page' => $perPage,
                'data' => $formattedData,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::error('DOAJ API exception: ' . $e->getMessage(), [
                'query' => $query,
            ]);

            return [
                'success' => false,
                'count' => 0,
                'total_pages' => 0,
                'current_page' => $page,
                'per_page' => $perPage,
                'data' => [],
                'error' => 'Koneksi ke repositori jurnal DOAJ mengalami gangguan. Silakan periksa jaringan atau coba sesaat lagi.',
            ];
        }
    }

    /**
     * Format a single DOAJ item into UI-ready structure.
     */
    public function formatWorkItem(array $work): array
    {
        $id = $work['id'] ?? null;
        $bib = $work['bibjson'] ?? [];

        $title = trim($bib['title'] ?? 'Tanpa Judul');
        $year = isset($bib['year']) && is_numeric($bib['year']) ? (int) $bib['year'] : null;
        $abstract = !empty($bib['abstract']) ? trim($bib['abstract']) : null;

        // Authors
        $authors = [];
        if (!empty($bib['author']) && is_array($bib['author'])) {
            foreach ($bib['author'] as $author) {
                $name = $author['name'] ?? null;
                if ($name) {
                    $authors[] = trim($name);
                }
            }
        }

        // Journal & Publisher
        $journalInfo = $bib['journal'] ?? [];
        $venue = $journalInfo['title'] ?? ($journalInfo['publisher'] ?? 'DOAJ Open Access Journal');

        // DOI extraction
        $doi = null;
        if (!empty($bib['identifier']) && is_array($bib['identifier'])) {
            foreach ($bib['identifier'] as $identifier) {
                if (strtolower($identifier['type'] ?? '') === 'doi') {
                    $doi = $identifier['id'] ?? null;
                    break;
                }
            }
        }

        // Fulltext & PDF link
        $pdfUrl = null;
        $landingPageUrl = null;
        if (!empty($bib['link']) && is_array($bib['link'])) {
            foreach ($bib['link'] as $link) {
                $url = $link['url'] ?? null;
                $contentType = strtolower($link['content_type'] ?? '');
                $type = strtolower($link['type'] ?? '');

                if ($type === 'fulltext' || str_contains($contentType, 'pdf') || (is_string($url) && str_ends_with(strtolower($url), '.pdf'))) {
                    if (str_contains($contentType, 'pdf') || (is_string($url) && str_ends_with(strtolower($url), '.pdf'))) {
                        $pdfUrl = $url;
                    } elseif (!$landingPageUrl) {
                        $landingPageUrl = $url;
                    }
                }
            }
        }

        if (!$pdfUrl && $landingPageUrl) {
            $pdfUrl = $landingPageUrl;
        }

        if (!$landingPageUrl) {
            $landingPageUrl = $doi ? 'https://doi.org/' . $doi : ($id ? 'https://doaj.org/article/' . $id : null);
        }

        // Keywords / Concepts
        $concepts = [];
        if (!empty($bib['keywords']) && is_array($bib['keywords'])) {
            foreach (array_slice($bib['keywords'], 0, 4) as $kw) {
                if (is_string($kw) && trim($kw) !== '') {
                    $concepts[] = [
                        'name' => trim($kw),
                        'score' => 90,
                    ];
                }
            }
        }

        // Citations
        $citations = $this->generateCitations($title, $authors, $year, $venue, $doi);

        return [
            'id' => $id,
            'title' => $title,
            'authors' => $authors,
            'authors_string' => !empty($authors) ? implode(', ', $authors) : 'Penulis Tidak Tercatat',
            'year' => $year,
            'publication_date' => !empty($bib['month']) ? "{$year}-" . str_pad($bib['month'], 2, '0', STR_PAD_LEFT) : ($year ? (string) $year : null),
            'venue' => $venue,
            'venue_type' => 'journal',
            'doi' => $doi,
            'cited_by_count' => 0,
            'is_oa' => true,
            'pdf_url' => $pdfUrl,
            'landing_page_url' => $landingPageUrl,
            'abstract' => $abstract,
            'concepts' => $concepts,
            'citations' => $citations,
            'source' => 'doaj',
            'source_label' => 'DOAJ Open Access',
        ];
    }

    /**
     * Generate standard citations (APA, IEEE, BibTeX).
     */
    public function generateCitations(string $title, array $authors, ?int $year, ?string $journal, ?string $doi): array
    {
        $yearStr = $year ?: 'n.d.';
        $journalStr = $journal ?: 'Jurnal Ilmiah';
        $doiStr = $doi ? "https://doi.org/{$doi}" : '';

        // APA Format
        $apaAuthors = 'Anonim';
        if (!empty($authors)) {
            $formattedAuthors = [];
            foreach (array_slice($authors, 0, 6) as $name) {
                $parts = explode(' ', trim($name));
                if (count($parts) > 1) {
                    $last = array_pop($parts);
                    $initials = implode('. ', array_map(fn($p) => mb_substr($p, 0, 1), $parts)) . '.';
                    $formattedAuthors[] = "{$last}, {$initials}";
                } else {
                    $formattedAuthors[] = $name;
                }
            }
            if (count($authors) > 6) {
                $formattedAuthors[] = 'et al.';
            }
            $apaAuthors = implode(', ', $formattedAuthors);
        }
        $apa = "{$apaAuthors} ({$yearStr}). {$title}. {$journalStr}." . ($doiStr ? " {$doiStr}" : '');

        // IEEE Format
        $ieeeAuthors = 'Anon.';
        if (!empty($authors)) {
            $formattedAuthors = [];
            foreach (array_slice($authors, 0, 3) as $name) {
                $parts = explode(' ', trim($name));
                if (count($parts) > 1) {
                    $last = array_pop($parts);
                    $initials = implode('. ', array_map(fn($p) => mb_substr($p, 0, 1), $parts)) . '.';
                    $formattedAuthors[] = "{$initials} {$last}";
                } else {
                    $formattedAuthors[] = $name;
                }
            }
            if (count($authors) > 3) {
                $formattedAuthors[] = 'et al.';
            }
            $ieeeAuthors = implode(', ', $formattedAuthors);
        }
        $ieee = "{$ieeeAuthors}, \"{$title},\" {$journalStr}, {$yearStr}." . ($doiStr ? " doi: {$doi}." : '');

        // BibTeX Format
        $firstAuthor = !empty($authors) ? preg_replace('/[^a-zA-Z0-9]/', '', explode(' ', $authors[0])[0]) : 'author';
        $citeKey = strtolower($firstAuthor) . $yearStr . Str::slug(Str::limit($title, 15, ''));
        $bibAuthors = implode(' and ', $authors);
        $bibtex = "@article{{$citeKey},\n"
            . "  title = {{$title}},\n"
            . "  author = {{$bibAuthors}},\n"
            . "  journal = {{$journalStr}},\n"
            . "  year = {{$yearStr}},\n"
            . ($doi ? "  doi = {{$doi}},\n" : "")
            . ($doiStr ? "  url = {{$doiStr}},\n" : "")
            . "}";

        return [
            'apa' => $apa,
            'ieee' => $ieee,
            'bibtex' => $bibtex,
        ];
    }
}

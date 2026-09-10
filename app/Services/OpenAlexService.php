<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OpenAlexService
{
    protected string $baseUrl = 'https://api.openalex.org/works';
    protected string $userAgent = 'SIBIMA-Fasilkom-UNSUB/1.0 (mailto:sibima@unsub.ac.id)';

    /**
     * Search academic works from OpenAlex with caching and error resilience.
     *
     * @param string $query
     * @param array $options [page, per_page, year_filter, open_access_only, sort]
     * @return array
     */
    public function search(string $query, array $options = []): array
    {
        $query = trim($query);
        $page = max(1, (int) ($options['page'] ?? 1));
        $perPage = min(25, max(5, (int) ($options['per_page'] ?? 12)));
        $yearFilter = $options['year_filter'] ?? 'all';
        $openAccessOnly = $options['open_access_only'] ?? true;
        $sort = $options['sort'] ?? 'relevance';

        // Cache key for 24 hours
        $cacheKey = 'openalex_search_' . md5(json_encode([
            'q' => strtolower($query),
            'page' => $page,
            'per_page' => $perPage,
            'year' => $yearFilter,
            'oa' => $openAccessOnly,
            'sort' => $sort,
        ]));

        return Cache::remember($cacheKey, 86400, function () use ($query, $page, $perPage, $yearFilter, $openAccessOnly, $sort) {
            return $this->fetchFromApi($query, $page, $perPage, $yearFilter, $openAccessOnly, $sort);
        });
    }

    /**
     * Perform the actual HTTP request to OpenAlex API.
     */
    protected function fetchFromApi(string $query, int $page, int $perPage, string $yearFilter, bool $openAccessOnly, string $sort): array
    {
        try {
            $filters = [];

            if ($openAccessOnly) {
                $filters[] = 'is_oa:true';
            }

            // Year filter handling
            $currentYear = (int) date('Y');
            if ($yearFilter === '3_years') {
                $filters[] = 'publication_year:' . ($currentYear - 2) . '-' . $currentYear;
            } elseif ($yearFilter === '5_years') {
                $filters[] = 'publication_year:' . ($currentYear - 4) . '-' . $currentYear;
            } elseif ($yearFilter === '10_years') {
                $filters[] = 'publication_year:' . ($currentYear - 9) . '-' . $currentYear;
            } elseif (is_numeric($yearFilter) && strlen($yearFilter) === 4) {
                $filters[] = 'publication_year:' . $yearFilter;
            }

            // Sort handling
            $sortParam = 'relevance_score:desc';
            if ($sort === 'newest') {
                $sortParam = 'publication_date:desc';
            } elseif ($sort === 'cited') {
                $sortParam = 'cited_by_count:desc';
            }

            $queryParams = [
                'per_page' => $perPage,
                'page' => $page,
                'sort' => $sortParam,
            ];

            if (!empty($query)) {
                $queryParams['search'] = $query;
            }

            if (!empty($filters)) {
                $queryParams['filter'] = implode(',', $filters);
            }

            $response = Http::timeout(10)
                ->retry(2, 500)
                ->withHeaders([
                    'User-Agent' => $this->userAgent,
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl, $queryParams);

            if (!$response->successful()) {
                Log::warning('OpenAlex API request failed', [
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
                    'error' => 'Layanan OpenAlex API sedang mengalami gangguan sementara. Silakan coba kembali sesaat lagi.',
                ];
            }

            $payload = $response->json();
            $meta = $payload['meta'] ?? [];
            $results = $payload['results'] ?? [];

            $totalCount = (int) ($meta['count'] ?? 0);
            $totalPages = (int) ceil($totalCount / $perPage);

            $formattedData = array_map(function ($item) {
                return $this->formatWorkItem($item);
            }, $results);

            return [
                'success' => true,
                'count' => $totalCount,
                'total_pages' => min($totalPages, 100), // OpenAlex basic limits pagination to 100 pages
                'current_page' => $page,
                'per_page' => $perPage,
                'data' => $formattedData,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::error('OpenAlex API exception: ' . $e->getMessage(), [
                'query' => $query,
            ]);

            return [
                'success' => false,
                'count' => 0,
                'total_pages' => 0,
                'current_page' => $page,
                'per_page' => $perPage,
                'data' => [],
                'error' => 'Koneksi ke repositori jurnal OpenAlex mengalami gangguan. Periksa jaringan atau coba sesaat lagi.',
            ];
        }
    }

    /**
     * Format a single work item into clean, UI-ready structure.
     */
    public function formatWorkItem(array $work): array
    {
        $id = $work['id'] ?? null;
        $title = $work['title'] ?? 'Tanpa Judul';
        $year = $work['publication_year'] ?? null;
        $publicationDate = $work['publication_date'] ?? null;
        $doi = $work['doi'] ?? null;
        $citedByCount = (int) ($work['cited_by_count'] ?? 0);

        // Authors
        $authors = [];
        if (!empty($work['authorships'])) {
            foreach ($work['authorships'] as $authorship) {
                $name = $authorship['author']['display_name'] ?? null;
                if ($name) {
                    $authors[] = $name;
                }
            }
        }

        // Venue / Journal Source
        $primaryLocation = $work['primary_location'] ?? [];
        $source = $primaryLocation['source'] ?? [];
        $venue = $source['display_name'] ?? ($work['host_venue']['display_name'] ?? null);
        $venueType = $source['type'] ?? null; // journal, repository, conference

        // Open Access & Fulltext PDF link
        $openAccess = $work['open_access'] ?? [];
        $isOpenAccess = (bool) ($openAccess['is_oa'] ?? false);
        $oaUrl = $openAccess['oa_url'] ?? null;
        $pdfUrl = $primaryLocation['pdf_url'] ?? $oaUrl;

        // Fallback landing page
        $landingPageUrl = $primaryLocation['landing_page_url'] ?? ($doi ?: $oaUrl);

        // Abstract reconstruction
        $abstract = null;
        if (!empty($work['abstract_inverted_index'])) {
            $abstract = $this->reconstructAbstract($work['abstract_inverted_index']);
        }

        // Keywords / Concepts
        $concepts = [];
        if (!empty($work['concepts'])) {
            foreach (array_slice($work['concepts'], 0, 4) as $concept) {
                if (!empty($concept['display_name'])) {
                    $concepts[] = [
                        'name' => $concept['display_name'],
                        'score' => round(($concept['score'] ?? 0) * 100),
                    ];
                }
            }
        }

        // Generated Citations
        $citations = $this->generateCitations($title, $authors, $year, $venue, $doi);

        return [
            'id' => $id,
            'title' => $title,
            'authors' => $authors,
            'authors_string' => !empty($authors) ? implode(', ', $authors) : 'Penulis Tidak Tercatat',
            'year' => $year,
            'publication_date' => $publicationDate,
            'venue' => $venue ?: 'Jurnal / Konferensi Akademik',
            'venue_type' => $venueType,
            'doi' => $doi,
            'cited_by_count' => $citedByCount,
            'is_oa' => $isOpenAccess,
            'pdf_url' => $pdfUrl,
            'landing_page_url' => $landingPageUrl,
            'abstract' => $abstract,
            'concepts' => $concepts,
            'citations' => $citations,
            'source' => 'openalex',
            'source_label' => 'OpenAlex Global Index',
        ];
    }

    /**
     * Reconstruct full abstract string from OpenAlex inverted index.
     */
    public function reconstructAbstract(?array $invertedIndex): ?string
    {
        if (empty($invertedIndex)) {
            return null;
        }

        $words = [];
        foreach ($invertedIndex as $word => $positions) {
            if (is_array($positions)) {
                foreach ($positions as $pos) {
                    $words[(int) $pos] = $word;
                }
            }
        }

        if (empty($words)) {
            return null;
        }

        ksort($words);
        return implode(' ', $words);
    }

    /**
     * Generate standard citations (APA 7th, IEEE, BibTeX).
     */
    public function generateCitations(string $title, array $authors, ?int $year, ?string $venue, ?string $doi): array
    {
        $yearStr = $year ? (string) $year : 'n.d.';
        $venueStr = $venue ?: 'Academic Journal';
        $doiStr = $doi ? ' ' . $doi : '';

        // Format APA 7th
        $apaAuthors = [];
        foreach (array_slice($authors, 0, 7) as $author) {
            $parts = explode(' ', trim($author));
            if (count($parts) > 1) {
                $last = array_pop($parts);
                $initials = implode('. ', array_map(fn($p) => mb_substr($p, 0, 1), $parts)) . '.';
                $apaAuthors[] = $last . ', ' . $initials;
            } else {
                $apaAuthors[] = $parts[0];
            }
        }
        $apaAuthorStr = !empty($apaAuthors) ? implode(', ', $apaAuthors) : 'Anonim';
        if (count($authors) > 7) {
            $apaAuthorStr .= ', et al.';
        }
        $apa = "{$apaAuthorStr} ({$yearStr}). {$title}. {$venueStr}.{$doiStr}";

        // Format IEEE
        $ieeeAuthors = [];
        foreach (array_slice($authors, 0, 6) as $author) {
            $parts = explode(' ', trim($author));
            if (count($parts) > 1) {
                $last = array_pop($parts);
                $initials = implode('. ', array_map(fn($p) => mb_substr($p, 0, 1), $parts)) . '.';
                $ieeeAuthors[] = $initials . ' ' . $last;
            } else {
                $ieeeAuthors[] = $parts[0];
            }
        }
        $ieeeAuthorStr = !empty($ieeeAuthors) ? implode(', ', $ieeeAuthors) : 'Anon';
        if (count($authors) > 6) {
            $ieeeAuthorStr .= ', et al.';
        }
        $ieee = "{$ieeeAuthorStr}, \"{$title},\" {$venueStr}, {$yearStr}." . ($doi ? " doi: " . str_replace('https://doi.org/', '', $doi) . "." : "");

        // Format BibTeX
        $firstAuthor = !empty($authors[0]) ? preg_replace('/[^a-zA-Z]/', '', explode(' ', $authors[0])[0]) : 'author';
        $firstWordTitle = preg_replace('/[^a-zA-Z]/', '', explode(' ', $title)[0] ?? 'paper');
        $citeKey = strtolower($firstAuthor . ($year ?: 'year') . $firstWordTitle);

        $bibtexAuthors = !empty($authors) ? implode(' and ', $authors) : 'Anonymous';
        $bibtex = "@article{{$citeKey},\n" .
            "  title = {{" . addslashes($title) . "}},\n" .
            "  author = {" . addslashes($bibtexAuthors) . "},\n" .
            "  journal = {{" . addslashes($venueStr) . "}},\n" .
            "  year = {" . ($year ?: '2024') . "},\n" .
            ($doi ? "  doi = {" . addslashes($doi) . "},\n" : "") .
            "}";

        return [
            'apa' => $apa,
            'ieee' => $ieee,
            'bibtex' => $bibtex,
        ];
    }
}

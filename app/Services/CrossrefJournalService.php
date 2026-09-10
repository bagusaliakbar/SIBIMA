<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CrossrefJournalService
{
    protected string $baseUrl = 'https://api.crossref.org/works';
    protected string $userAgent = 'SIBIMA-Fasilkom-UNSUB/1.0 (mailto:sibima@unsub.ac.id)';
    protected string $mailto = 'sibima@unsub.ac.id';
    protected int $cacheTtl = 86400; // 24 hours

    /**
     * Search academic works from Crossref DOI Registry.
     *
     * @param string $query
     * @param array $options [page, per_page, year_filter, sort, open_access_only]
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
        $openAccessOnly = (bool) ($options['open_access_only'] ?? false);

        $cacheKey = 'crossref_search_' . md5(json_encode([
            'q' => strtolower($query),
            'page' => $page,
            'per_page' => $perPage,
            'year' => $yearFilter,
            'sort' => $sort,
            'oa' => $openAccessOnly,
        ]));

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($query, $page, $perPage, $yearFilter, $sort, $openAccessOnly) {
            return $this->performSearch($query, $page, $perPage, $yearFilter, $sort, $openAccessOnly);
        });
    }

    /**
     * Perform the actual HTTP request to Crossref API.
     */
    protected function performSearch(string $query, int $page, int $perPage, string $yearFilter, string $sort, bool $openAccessOnly): array
    {
        try {
            $offset = ($page - 1) * $perPage;
            $filters = ['type:journal-article'];

            // Year filter
            $currentYear = (int) date('Y');
            if ($yearFilter === '3_years') {
                $filters[] = 'from-pub-date:' . ($currentYear - 2) . '-01-01';
                $filters[] = 'until-pub-date:' . $currentYear . '-12-31';
            } elseif ($yearFilter === '5_years') {
                $filters[] = 'from-pub-date:' . ($currentYear - 4) . '-01-01';
                $filters[] = 'until-pub-date:' . $currentYear . '-12-31';
            } elseif ($yearFilter === '10_years') {
                $filters[] = 'from-pub-date:' . ($currentYear - 9) . '-01-01';
                $filters[] = 'until-pub-date:' . $currentYear . '-12-31';
            } elseif (is_numeric($yearFilter) && strlen($yearFilter) === 4) {
                $filters[] = 'from-pub-date:' . $yearFilter . '-01-01';
                $filters[] = 'until-pub-date:' . $yearFilter . '-12-31';
            }

            if ($openAccessOnly) {
                $filters[] = 'has-license:true';
            }

            $queryParams = [
                'query' => $query,
                'rows' => $perPage,
                'offset' => $offset,
                'mailto' => $this->mailto,
                'filter' => implode(',', $filters),
            ];

            // Sorting
            if ($sort === 'newest') {
                $queryParams['sort'] = 'published';
                $queryParams['order'] = 'desc';
            } elseif ($sort === 'cited') {
                $queryParams['sort'] = 'is-referenced-by-count';
                $queryParams['order'] = 'desc';
            } else {
                $queryParams['sort'] = 'score';
                $queryParams['order'] = 'desc';
            }

            $response = Http::timeout(10)
                ->retry(2, 500)
                ->withHeaders([
                    'User-Agent' => $this->userAgent,
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl, $queryParams);

            if (!$response->successful()) {
                Log::warning('Crossref API request failed', [
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
                    'error' => 'Gagal mengambil data jurnal dari Crossref. Silakan coba sesaat lagi.',
                ];
            }

            $payload = $response->json();
            $message = $payload['message'] ?? [];
            $totalCount = (int) ($message['total-results'] ?? 0);
            $totalPages = (int) ceil($totalCount / $perPage);
            $items = $message['items'] ?? [];

            $formattedData = array_map(function ($item) {
                return $this->formatWorkItem($item);
            }, $items);

            return [
                'success' => true,
                'count' => $totalCount,
                'total_pages' => min($totalPages, 100), // Crossref ceiling
                'current_page' => $page,
                'per_page' => $perPage,
                'data' => $formattedData,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::error('Crossref API exception: ' . $e->getMessage(), [
                'query' => $query,
            ]);

            return [
                'success' => false,
                'count' => 0,
                'total_pages' => 0,
                'current_page' => $page,
                'per_page' => $perPage,
                'data' => [],
                'error' => 'Koneksi ke repositori Crossref mengalami gangguan. Silakan periksa jaringan atau coba sesaat lagi.',
            ];
        }
    }

    /**
     * Format a single Crossref item into clean UI-ready structure.
     */
    public function formatWorkItem(array $item): array
    {
        $doi = $item['DOI'] ?? null;
        $id = $doi ?: md5(json_encode($item));

        // Title
        $titleArray = $item['title'] ?? [];
        $title = !empty($titleArray) && is_array($titleArray) ? trim($titleArray[0]) : 'Tanpa Judul';

        // Authors
        $authors = [];
        if (!empty($item['author']) && is_array($item['author'])) {
            foreach ($item['author'] as $author) {
                if (!empty($author['name'])) {
                    $authors[] = trim($author['name']);
                } elseif (!empty($author['family'])) {
                    $given = $author['given'] ?? '';
                    $authors[] = trim("{$given} {$author['family']}");
                }
            }
        }

        // Year and publication date
        $year = null;
        $publicationDate = null;
        $dateSources = ['published-print', 'published-online', 'issued', 'created'];
        foreach ($dateSources as $sourceKey) {
            if (!empty($item[$sourceKey]['date-parts'][0][0])) {
                $parts = $item[$sourceKey]['date-parts'][0];
                $year = (int) $parts[0];
                $month = isset($parts[1]) ? str_pad($parts[1], 2, '0', STR_PAD_LEFT) : '01';
                $day = isset($parts[2]) ? str_pad($parts[2], 2, '0', STR_PAD_LEFT) : '01';
                $publicationDate = "{$year}-{$month}-{$day}";
                break;
            }
        }

        // Venue / Journal
        $containerTitle = $item['container-title'] ?? [];
        $venue = !empty($containerTitle) && is_array($containerTitle) ? trim($containerTitle[0]) : ($item['publisher'] ?? 'Crossref Registered Journal');

        // Citation count
        $citedByCount = (int) ($item['is-referenced-by-count'] ?? 0);

        // PDF & landing page link
        $pdfUrl = null;
        $landingPageUrl = $item['URL'] ?? ($doi ? 'https://doi.org/' . $doi : null);

        if (!empty($item['link']) && is_array($item['link'])) {
            foreach ($item['link'] as $link) {
                $url = $link['URL'] ?? null;
                $contentType = strtolower($link['content-type'] ?? '');
                if (str_contains($contentType, 'pdf') || (is_string($url) && str_ends_with(strtolower($url), '.pdf'))) {
                    $pdfUrl = $url;
                    break;
                }
            }
        }

        // Abstract (Clean JATS / XML tags if present)
        $abstract = null;
        if (!empty($item['abstract'])) {
            $rawAbstract = is_string($item['abstract']) ? $item['abstract'] : '';
            $cleanAbstract = strip_tags($rawAbstract);
            $cleanAbstract = preg_replace('/^\s*abstract\s*[:\-]?\s*/i', '', $cleanAbstract);
            $abstract = trim($cleanAbstract) ?: null;
        }

        // Open Access check
        $isOpenAccess = false;
        if (!empty($item['license']) && is_array($item['license'])) {
            foreach ($item['license'] as $lic) {
                $licUrl = strtolower($lic['URL'] ?? '');
                if (str_contains($licUrl, 'creativecommon') || str_contains($licUrl, 'open-access') || str_contains($licUrl, 'publicdomain')) {
                    $isOpenAccess = true;
                    break;
                }
            }
        }
        if ($pdfUrl) {
            $isOpenAccess = true;
        }

        // Subject / Concepts
        $concepts = [];
        if (!empty($item['subject']) && is_array($item['subject'])) {
            foreach (array_slice($item['subject'], 0, 4) as $subj) {
                if (is_string($subj) && trim($subj) !== '') {
                    $concepts[] = [
                        'name' => trim($subj),
                        'score' => 85,
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
            'authors_string' => !empty($authors) ? implode(', ', $authors) : 'Penulis Tidak Tertera',
            'year' => $year,
            'publication_date' => $publicationDate ?: ($year ? (string) $year : null),
            'venue' => $venue,
            'venue_type' => 'journal',
            'doi' => $doi,
            'cited_by_count' => $citedByCount,
            'is_oa' => $isOpenAccess,
            'pdf_url' => $pdfUrl,
            'landing_page_url' => $landingPageUrl,
            'abstract' => $abstract,
            'concepts' => $concepts,
            'citations' => $citations,
            'source' => 'crossref',
            'source_label' => 'Crossref DOI Registry',
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

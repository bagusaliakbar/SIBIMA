<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookRepositoryService
{
    protected string $openLibraryUrl = 'https://openlibrary.org/search.json';
    protected string $doabUrl = 'https://directory.doabooks.org/rest/search';
    protected string $googleBooksUrl = 'https://www.googleapis.com/books/v1/volumes';
    protected string $userAgent = 'SIBIMA-Fasilkom-UNSUB/1.0 (mailto:sibima@unsub.ac.id)';

    /**
     * Definisi 8 Pilar Topik Inti Sistem Informasi untuk kurasi katalog buku teks.
     */
    public static function getInformationSystemsTopics(): array
    {
        return [
            'all_si' => [
                'id' => 'all_si',
                'name' => 'Semua Topik Sistem Informasi',
                'short_name' => 'Semua Topik SI',
                'icon' => '🌟',
                'color' => 'orange',
                'description' => 'Buku teks fundamental Sistem Informasi, Management Information Systems (MIS), dan transformasi digital organisasi.',
                'default_query' => 'management information systems',
                'openlibrary_subject' => 'information_systems',
                'keywords' => ['information systems', 'management information systems', 'business information systems', 'digital transformation'],
            ],
            'basis_data' => [
                'id' => 'basis_data',
                'name' => 'Basis Data & Manajemen Data',
                'short_name' => 'Basis Data & Big Data',
                'icon' => '🗄️',
                'color' => 'blue',
                'description' => 'Perancangan basis data relasional, SQL, NoSQL, data warehousing, data lake, dan arsitektur big data.',
                'default_query' => 'database management systems sql',
                'openlibrary_subject' => 'database_management',
                'keywords' => ['database management', 'relational database', 'sql', 'data warehousing', 'nosql', 'big data'],
            ],
            'analisis_desain' => [
                'id' => 'analisis_desain',
                'name' => 'Analisis & Perancangan Sistem',
                'short_name' => 'Analisis & Desain (UML)',
                'icon' => '💼',
                'color' => 'emerald',
                'description' => 'Metodologi SDLC, permodelan proses bisnis (BPMN), Use Case, UML, diagram aktivitas, dan arsitektur sistem.',
                'default_query' => 'systems analysis and design uml',
                'openlibrary_subject' => 'system_analysis',
                'keywords' => ['systems analysis and design', 'uml', 'bpmn', 'software requirements', 'object oriented analysis'],
            ],
            'rekayasa_web' => [
                'id' => 'rekayasa_web',
                'name' => 'Rekayasa Perangkat Lunak & Web',
                'short_name' => 'RPL & Web Development',
                'icon' => '🌐',
                'color' => 'indigo',
                'description' => 'Pengembangan aplikasi web modern, fullstack framework, arsitektur REST API, microservices, dan software engineering.',
                'default_query' => 'software engineering web development',
                'openlibrary_subject' => 'software_engineering',
                'keywords' => ['software engineering', 'web development', 'web application', 'rest api', 'clean code'],
            ],
            'manajemen_ti' => [
                'id' => 'manajemen_ti',
                'name' => 'Tata Kelola & Manajemen TI',
                'short_name' => 'Tata Kelola & Audit TI',
                'icon' => '📊',
                'color' => 'amber',
                'description' => 'Framework COBIT, ITIL, audit sistem informasi, manajemen proyek perangkat lunak (Agile/Scrum), dan manajemen risiko TI.',
                'default_query' => 'information technology governance cobit',
                'openlibrary_subject' => 'information_technology_management',
                'keywords' => ['it governance', 'cobit', 'itil', 'project management', 'agile', 'scrum', 'it audit'],
            ],
            'ai_data_science' => [
                'id' => 'ai_data_science',
                'name' => 'AI, Data Science & DSS / SPK',
                'short_name' => 'AI & Sistem Penunjang Keputusan',
                'icon' => '🤖',
                'color' => 'purple',
                'description' => 'Sistem Pendukung Keputusan (DSS/SPK), data mining, business intelligence, machine learning, dan analisis prediktif.',
                'default_query' => 'decision support systems business intelligence',
                'openlibrary_subject' => 'decision_support_systems',
                'keywords' => ['decision support systems', 'business intelligence', 'data mining', 'machine learning', 'predictive analytics'],
            ],
            'keamanan_informasi' => [
                'id' => 'keamanan_informasi',
                'name' => 'Keamanan Informasi & Jaringan',
                'short_name' => 'Keamanan Sistem & Siber',
                'icon' => '🔒',
                'color' => 'rose',
                'description' => 'Prinsip keamanan informasi (CIA Triad), ISO 27001, kriptografi terapan, keamanan jaringan, dan manajemen insiden siber.',
                'default_query' => 'information security computer networks',
                'openlibrary_subject' => 'computer_security',
                'keywords' => ['information security', 'cybersecurity', 'network security', 'cryptography', 'iso 27001'],
            ],
            'e_business' => [
                'id' => 'e_business',
                'name' => 'E-Business & Enterprise Systems',
                'short_name' => 'E-Business & ERP',
                'icon' => '🛒',
                'color' => 'cyan',
                'description' => 'Sistem Enterprise Resource Planning (ERP), Customer Relationship Management (CRM), Supply Chain, dan strategi bisnis digital.',
                'default_query' => 'enterprise resource planning e-commerce',
                'openlibrary_subject' => 'enterprise_resource_planning',
                'keywords' => ['enterprise resource planning', 'erp', 'e-commerce', 'e-business', 'supply chain management', 'crm'],
            ],
        ];
    }

    /**
     * Search books with caching, topic prioritization, and multi-source aggregation.
     */
    public function search(string $query = '', array $options = []): array
    {
        $rawQuery = trim($query);
        $topicKey = $options['topic'] ?? 'all_si';
        $source = $options['source'] ?? 'all';
        $access = $options['access'] ?? 'all'; // 'all' or 'free_read'
        $page = max(1, (int) ($options['page'] ?? 1));
        $perPage = min(24, max(6, (int) ($options['per_page'] ?? 12)));

        $topics = self::getInformationSystemsTopics();
        $selectedTopic = $topics[$topicKey] ?? $topics['all_si'];

        // Build effective search keyword
        $effectiveQuery = $rawQuery;
        if (empty($effectiveQuery)) {
            $effectiveQuery = $selectedTopic['default_query'];
        }

        $cacheKey = 'books_search_' . md5(json_encode([
            'q' => strtolower($effectiveQuery),
            'topic' => $topicKey,
            'source' => $source,
            'access' => $access,
            'page' => $page,
            'per_page' => $perPage,
        ]));

        return Cache::remember($cacheKey, 86400, function () use ($effectiveQuery, $selectedTopic, $source, $access, $page, $perPage) {
            return $this->executeSearch($effectiveQuery, $selectedTopic, $source, $access, $page, $perPage);
        });
    }

    /**
     * Execute search across selected sources.
     */
    protected function executeSearch(string $query, array $topic, string $source, string $access, int $page, int $perPage): array
    {
        $results = [
            'success' => true,
            'count' => 0,
            'total_pages' => 1,
            'current_page' => $page,
            'per_page' => $perPage,
            'data' => [],
            'error' => null,
            'source' => $source,
        ];

        try {
            $items = [];

            if ($source === 'openlibrary') {
                $items = $this->searchOpenLibrary($query, $topic, $page, $perPage * 2);
            } elseif ($source === 'doab') {
                $items = $this->searchDoab($query, $page, $perPage * 2);
            } elseif ($source === 'googlebooks') {
                $items = $this->searchGoogleBooks($query, $page, $perPage * 2);
            } else {
                // Federated Search (Open Library + DOAB + Google Books)
                $olItems = $this->searchOpenLibrary($query, $topic, $page, $perPage);
                $doabItems = $this->searchDoab($query, $page, (int) ceil($perPage / 2));
                $gbItems = $this->searchGoogleBooks($query, $page, (int) ceil($perPage / 2));

                $merged = array_merge($olItems, $doabItems, $gbItems);

                // Deduplicate by normalized title or ISBN
                $seen = [];
                foreach ($merged as $item) {
                    $key = !empty($item['isbn']) ? 'isbn:' . $item['isbn'] : 'title:' . Str::slug($item['title']);
                    if (!isset($seen[$key])) {
                        $seen[$key] = true;
                        $items[] = $item;
                    }
                }
            }

            // Filter access if requested (only books with direct read online or pdf available)
            if ($access === 'free_read') {
                $items = array_values(array_filter($items, function ($item) {
                    return !empty($item['is_free_readable']) || !empty($item['pdf_url']);
                }));
            }

            // Slice for pagination
            $totalCount = count($items);
            $totalPages = max(1, (int) ceil($totalCount / $perPage));
            $pagedItems = array_slice($items, ($page - 1) * $perPage, $perPage);

            $results['count'] = $totalCount;
            $results['total_pages'] = $totalPages;
            $results['current_page'] = $page;
            $results['data'] = $pagedItems;
        } catch (\Throwable $e) {
            Log::error('BookRepositoryService search error: ' . $e->getMessage(), ['exception' => $e]);
            $results['success'] = false;
            $results['error'] = 'Gagal memuat katalog buku: ' . $e->getMessage();
        }

        return $results;
    }

    /**
     * Search Open Library (Internet Archive).
     */
    public function searchOpenLibrary(string $query, array $topic, int $page = 1, int $limit = 20): array
    {
        try {
            $params = [
                'q' => $query,
                'limit' => $limit,
                'page' => $page,
                'fields' => 'key,title,author_name,first_publish_year,cover_i,isbn,publisher,ebook_access,ia,subject,first_sentence,number_of_pages_median',
            ];

            if (!empty($topic['openlibrary_subject'])) {
                $params['subject'] = $topic['openlibrary_subject'];
            }

            $response = Http::timeout(8)
                ->withHeaders(['User-Agent' => $this->userAgent])
                ->get($this->openLibraryUrl, $params);

            if (!$response->successful()) {
                Log::warning('Open Library API responded with status ' . $response->status());
                return [];
            }

            $docs = $response->json('docs') ?? [];
            $items = [];

            foreach ($docs as $doc) {
                $title = trim($doc['title'] ?? '');
                if (empty($title)) continue;

                $authors = $doc['author_name'] ?? [];
                $year = isset($doc['first_publish_year']) ? (int) $doc['first_publish_year'] : null;
                $coverId = $doc['cover_i'] ?? null;
                $coverUrl = $coverId ? "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg" : null;
                $coverUrlLarge = $coverId ? "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg" : null;
                $isbnList = $doc['isbn'] ?? [];
                $isbn = !empty($isbnList) ? $isbnList[0] : null;
                $publishers = $doc['publisher'] ?? [];
                $publisher = !empty($publishers) ? $publishers[0] : 'Open Library / Academic Press';

                $olKey = $doc['key'] ?? '';
                $iaList = $doc['ia'] ?? [];
                $iaId = !empty($iaList) ? $iaList[0] : null;
                $ebookAccess = $doc['ebook_access'] ?? 'no_ebook';

                // Reading URLs
                $readUrl = null;
                $isFreeReadable = false;

                if ($iaId) {
                    $readUrl = "https://archive.org/details/{$iaId}";
                    $isFreeReadable = in_array($ebookAccess, ['public', 'borrowable', 'printdisabled']);
                } elseif ($olKey) {
                    $readUrl = "https://openlibrary.org{$olKey}";
                    $isFreeReadable = ($ebookAccess === 'public');
                }

                $abstract = null;
                if (!empty($doc['first_sentence'])) {
                    $firstSent = is_array($doc['first_sentence']) ? ($doc['first_sentence']['value'] ?? '') : $doc['first_sentence'];
                    $abstract = is_string($firstSent) ? $firstSent : null;
                }

                $subjects = array_slice($doc['subject'] ?? [], 0, 4);

                $items[] = $this->formatBookItem([
                    'identifier' => 'ol:' . ltrim($olKey, '/works/'),
                    'title' => $title,
                    'authors' => $authors,
                    'year' => $year,
                    'publisher' => $publisher,
                    'isbn' => $isbn,
                    'cover_url' => $coverUrl,
                    'cover_url_large' => $coverUrlLarge,
                    'read_url' => $readUrl,
                    'pdf_url' => null,
                    'is_open_access' => ($ebookAccess === 'public'),
                    'is_free_readable' => $isFreeReadable,
                    'source' => 'openlibrary',
                    'source_label' => 'Open Library (Internet Archive)',
                    'abstract' => $abstract,
                    'subjects' => $subjects,
                    'pages' => $doc['number_of_pages_median'] ?? null,
                ]);
            }

            return $items;
        } catch (\Throwable $e) {
            Log::warning('Error querying Open Library: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Search DOAB (Directory of Open Access Books) for peer-reviewed OA academic books.
     */
    public function searchDoab(string $query, int $page = 1, int $limit = 15): array
    {
        try {
            $response = Http::timeout(8)
                ->withHeaders(['User-Agent' => $this->userAgent])
                ->get($this->doabUrl, [
                    'query' => $query,
                    'expand' => 'metadata',
                    'limit' => $limit,
                ]);

            if (!$response->successful()) {
                return [];
            }

            $results = $response->json();
            if (!is_array($results)) {
                return [];
            }

            $items = [];
            foreach ($results as $res) {
                $name = $res['name'] ?? '';
                if (empty($name)) continue;

                $metadata = $res['metadata'] ?? [];
                $authors = [];
                $year = null;
                $publisher = 'DOAB Academic Publisher';
                $abstract = null;
                $doi = null;
                $handle = $res['handle'] ?? null;
                $readUrl = $handle ? "https://directory.doabooks.org/handle/{$handle}" : null;
                $pdfUrl = null;
                $isbn = null;
                $subjects = [];

                foreach ($metadata as $meta) {
                    $key = $meta['key'] ?? '';
                    $val = trim($meta['value'] ?? '');
                    if (empty($val)) continue;

                    if (in_array($key, ['dc.contributor.author', 'dc.contributor.editor'])) {
                        $authors[] = $val;
                    } elseif ($key === 'dc.date.issued' && !$year) {
                        $year = (int) substr($val, 0, 4);
                    } elseif ($key === 'dc.publisher') {
                        $publisher = $val;
                    } elseif ($key === 'dc.description.abstract' && !$abstract) {
                        $abstract = $val;
                    } elseif ($key === 'dc.identifier.uri' && Str::startsWith($val, 'http')) {
                        if (Str::endsWith(strtolower($val), '.pdf')) {
                            $pdfUrl = $val;
                        } elseif (!$readUrl) {
                            $readUrl = $val;
                        }
                    } elseif ($key === 'dc.identifier.doi') {
                        $doi = $val;
                    } elseif ($key === 'dc.identifier.isbn' && !$isbn) {
                        $isbn = preg_replace('/[^0-9X]/i', '', $val);
                    } elseif ($key === 'dc.subject.other' && count($subjects) < 4) {
                        $subjects[] = $val;
                    }
                }

                $uuid = $res['uuid'] ?? Str::random(10);

                $items[] = $this->formatBookItem([
                    'identifier' => 'doab:' . $uuid,
                    'title' => $name,
                    'authors' => $authors,
                    'year' => $year ?: (int) date('Y'),
                    'publisher' => $publisher,
                    'isbn' => $isbn,
                    'cover_url' => null, // DOAB doesn't host standard cover thumbnails
                    'cover_url_large' => null,
                    'read_url' => $readUrl,
                    'pdf_url' => $pdfUrl,
                    'is_open_access' => true,
                    'is_free_readable' => true,
                    'source' => 'doab',
                    'source_label' => 'DOAB (Open Access Academic Books)',
                    'abstract' => $abstract,
                    'subjects' => !empty($subjects) ? $subjects : ['Sistem Informasi', 'Open Access Book'],
                    'doi' => $doi,
                    'pages' => null,
                ]);
            }

            return $items;
        } catch (\Throwable $e) {
            Log::warning('Error querying DOAB: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Search Google Books API (Free E-Books / Preview with graceful fallback).
     */
    public function searchGoogleBooks(string $query, int $page = 1, int $limit = 12): array
    {
        try {
            $startIndex = max(0, ($page - 1) * $limit);

            $response = Http::timeout(6)
                ->withHeaders(['User-Agent' => $this->userAgent])
                ->get($this->googleBooksUrl, [
                    'q' => $query,
                    'filter' => 'free-ebooks',
                    'maxResults' => $limit,
                    'startIndex' => $startIndex,
                ]);

            if (!$response->successful()) {
                // If 429 or blocked, return empty array gracefully
                return [];
            }

            $itemsData = $response->json('items') ?? [];
            $items = [];

            foreach ($itemsData as $item) {
                $vol = $item['volumeInfo'] ?? [];
                $title = trim($vol['title'] ?? '');
                if (empty($title)) continue;

                $authors = $vol['authors'] ?? [];
                $pubDate = $vol['publishedDate'] ?? null;
                $year = $pubDate ? (int) substr($pubDate, 0, 4) : null;
                $publisher = $vol['publisher'] ?? 'Google Books Publisher';
                $coverUrl = $vol['imageLinks']['thumbnail'] ?? ($vol['imageLinks']['smallThumbnail'] ?? null);
                if ($coverUrl) {
                    $coverUrl = str_replace('http://', 'https://', $coverUrl);
                }

                $accessInfo = $item['accessInfo'] ?? [];
                $webReader = $accessInfo['webReaderLink'] ?? null;
                $previewLink = $vol['previewLink'] ?? null;
                $pdfDownload = ($accessInfo['pdf']['isAvailable'] ?? false) ? ($accessInfo['pdf']['downloadLink'] ?? null) : null;

                $readUrl = $webReader ?: $previewLink;
                $isFreeReadable = ($accessInfo['viewability'] ?? '') === 'FULL_PUBLIC_DOMAIN' || !empty($pdfDownload) || !empty($webReader);

                $subjects = array_slice($vol['categories'] ?? [], 0, 3);

                $items[] = $this->formatBookItem([
                    'identifier' => 'gb:' . ($item['id'] ?? Str::random(8)),
                    'title' => $title,
                    'authors' => $authors,
                    'year' => $year,
                    'publisher' => $publisher,
                    'isbn' => null,
                    'cover_url' => $coverUrl,
                    'cover_url_large' => $coverUrl,
                    'read_url' => $readUrl,
                    'pdf_url' => $pdfDownload,
                    'is_open_access' => $isFreeReadable,
                    'is_free_readable' => $isFreeReadable,
                    'source' => 'googlebooks',
                    'source_label' => 'Google Books (Free E-Book)',
                    'abstract' => $vol['description'] ?? null,
                    'subjects' => $subjects,
                    'pages' => $vol['pageCount'] ?? null,
                ]);
            }

            return $items;
        } catch (\Throwable $e) {
            Log::warning('Error querying Google Books: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Format a book item with citations and clean structure.
     */
    protected function formatBookItem(array $data): array
    {
        $authors = $data['authors'] ?? [];
        if (empty($authors)) {
            $authors = ['Anonim'];
        }

        $authorsString = implode(', ', $authors);
        $title = $data['title'];
        $year = $data['year'];
        $publisher = $data['publisher'] ?: 'Academic Publisher';
        $doi = $data['doi'] ?? null;

        $citations = $this->generateBookCitations($title, $authors, $year, $publisher, $doi);

        return [
            'identifier' => $data['identifier'],
            'title' => $title,
            'authors' => $authors,
            'authors_string' => $authorsString,
            'year' => $year,
            'publisher' => $publisher,
            'isbn' => $data['isbn'] ?? null,
            'cover_url' => $data['cover_url'] ?? null,
            'cover_url_large' => $data['cover_url_large'] ?? null,
            'read_url' => $data['read_url'] ?? null,
            'pdf_url' => $data['pdf_url'] ?? null,
            'is_open_access' => (bool) ($data['is_open_access'] ?? false),
            'is_free_readable' => (bool) ($data['is_free_readable'] ?? false),
            'source' => $data['source'],
            'source_label' => $data['source_label'],
            'abstract' => $data['abstract'] ?? null,
            'subjects' => $data['subjects'] ?? ['Sistem Informasi'],
            'doi' => $doi,
            'pages' => $data['pages'] ?? null,
            'citations' => $citations,
        ];
    }

    /**
     * Generate standard citations for books (APA 7th, IEEE, Chicago, BibTeX, RIS).
     */
    public function generateBookCitations(string $title, array $authors, ?int $year, ?string $publisher, ?string $doi = null): array
    {
        $yearStr = $year ? (string) $year : 'n.d.';
        $pubStr = $publisher ?: 'Penerbit Ilmiah';
        $doiStr = $doi ? ' https://doi.org/' . ltrim($doi, 'https://doi.org/') : '';

        // 1. APA 7th Format for Books: Author, A. A. (Year). Title of book. Publisher. DOI
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
        $apa = "{$apaAuthorStr} ({$yearStr}). {$title}. {$pubStr}.{$doiStr}";

        // 2. IEEE Format for Books: [1] A. A. Author, Title of book. City: Publisher, Year.
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
        $ieee = "{$ieeeAuthorStr}, {$title}. {$pubStr}, {$yearStr}." . ($doi ? " doi: {$doi}." : "");

        // 3. Chicago 17th: Author, First. Year. Title of Book. City: Publisher.
        $chicago = "{$apaAuthorStr}. {$yearStr}. {$title}. {$pubStr}.";

        // 4. BibTeX format for books: @book{...}
        $firstAuthor = !empty($authors[0]) ? preg_replace('/[^a-zA-Z]/', '', explode(' ', $authors[0])[0]) : 'author';
        $firstWord = preg_replace('/[^a-zA-Z]/', '', explode(' ', $title)[0] ?? 'book');
        $citeKey = strtolower($firstAuthor . ($year ?: 'year') . $firstWord);
        $bibAuthors = implode(' and ', $authors);

        $bibtex = "@book{{$citeKey},\n" .
            "  title     = {{" . addslashes($title) . "}},\n" .
            "  author    = {" . addslashes($bibAuthors) . "},\n" .
            "  year      = {" . ($year ?: '2024') . "},\n" .
            "  publisher = {{" . addslashes($pubStr) . "}},\n" .
            ($doi ? "  doi       = {" . addslashes($doi) . "},\n" : "") .
            "}";

        // 5. RIS Format (Mendeley, Zotero, EndNote)
        $ris = "TY  - BOOK\r\n" .
            "TI  - {$title}\r\n";
        foreach ($authors as $a) {
            $ris .= "AU  - {$a}\r\n";
        }
        if ($year) {
            $ris .= "PY  - {$year}\r\n";
        }
        $ris .= "PB  - {$pubStr}\r\n";
        if ($doi) {
            $ris .= "DO  - {$doi}\r\n";
        }
        $ris .= "ER  - \r\n";

        return [
            'apa' => $apa,
            'ieee' => $ieee,
            'chicago' => $chicago,
            'bibtex' => $bibtex,
            'ris' => $ris,
        ];
    }
}

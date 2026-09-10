<?php

namespace App\Services;

use App\Models\FasilkomJournal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class FasilkomJournalService
{
    protected string $oaiBaseUrl = 'https://ejournal.unsub.ac.id/index.php/FASILKOM/oai';

    /**
     * Search articles in local FASILKOM journal database.
     *
     * @param string $query
     * @param array $options [page, per_page, year_filter, sort, author]
     * @return array
     */
    public function search(string $query, array $options = []): array
    {
        $page = max(1, (int) ($options['page'] ?? 1));
        $perPage = min(25, max(5, (int) ($options['per_page'] ?? 12)));
        $yearFilter = $options['year_filter'] ?? 'all';
        $sort = $options['sort'] ?? 'relevance';
        $author = trim($options['author'] ?? 'all');

        $builder = FasilkomJournal::query();

        if (trim($query) !== '') {
            $builder->search($query);
        }

        if (!empty($yearFilter) && $yearFilter !== 'all') {
            $builder->filterYear($yearFilter);
        }

        // Author / Dosen Filter
        if (!empty($author) && $author !== 'all') {
            $builder->where(function ($q) use ($author) {
                $q->where('authors_string', 'like', "%{$author}%")
                  ->orWhereJsonContains('authors', $author);
            });
        }

        // Sorting
        if ($sort === 'newest') {
            $builder->orderBy('year', 'desc')->orderBy('id', 'desc');
        } elseif ($sort === 'oldest') {
            $builder->orderBy('year', 'asc')->orderBy('id', 'asc');
        } else {
            // Default: newest by year then id
            $builder->orderBy('year', 'desc')->orderBy('id', 'desc');
        }

        $totalCount = $builder->count();
        $totalPages = (int) ceil($totalCount / $perPage);
        $items = $builder->forPage($page, $perPage)->get();

        $data = $items->map(fn(FasilkomJournal $journal) => $journal->toJournalItem())->toArray();

        return [
            'success' => true,
            'count' => $totalCount,
            'total_pages' => $totalPages,
            'current_page' => $page,
            'per_page' => $perPage,
            'data' => $data,
            'error' => null,
            'source' => 'fasilkom',
            'author' => $author !== 'all' ? $author : null,
        ];
    }

    /**
     * Get a normalized, deduplicated list of all authors in Jurnal GLOBAL FASILKOM with article counts.
     * Sorted alphabetically A-Z.
     *
     * @return array array of ['name' => string, 'count' => int]
     */
    public function getAuthorsList(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('fasilkom_journal_authors_list', 3600, function () {
            $journals = FasilkomJournal::all(['id', 'authors', 'authors_string']);
            $counts = [];

            foreach ($journals as $j) {
                $authorList = [];
                if (is_array($j->authors) && !empty($j->authors)) {
                    $authorList = $j->authors;
                } elseif (!empty($j->authors_string)) {
                    $authorList = explode(',', $j->authors_string);
                }

                $seenInArticle = [];
                foreach ($authorList as $rawName) {
                    $name = trim($rawName);
                    if (empty($name) || strlen($name) < 3) {
                        continue;
                    }

                    // Clean repeated words like "Jaja Jaja" or "jaja jaja"
                    $words = explode(' ', $name);
                    if (count($words) === 2 && strtolower($words[0]) === strtolower($words[1])) {
                        $name = $words[0];
                    }

                    // Proper title-casing
                    $name = ucwords(strtolower($name));

                    $normKey = strtolower(preg_replace('/[^a-z0-9]/', '', $name));
                    if (empty($normKey)) {
                        continue;
                    }

                    if (isset($seenInArticle[$normKey])) {
                        continue;
                    }
                    $seenInArticle[$normKey] = true;

                    if (!isset($counts[$normKey])) {
                        $counts[$normKey] = [
                            'name' => $name,
                            'count' => 0,
                        ];
                    } else {
                        if (strlen($name) > strlen($counts[$normKey]['name'])) {
                            $counts[$normKey]['name'] = $name;
                        }
                    }
                    $counts[$normKey]['count']++;
                }
            }

            // Sort alphabetically by name
            uasort($counts, fn($a, $b) => strcasecmp($a['name'], $b['name']));

            return array_values($counts);
        });
    }

    /**
     * Get top authors with the highest publication counts for quick chips.
     *
     * @param int $limit
     * @return array
     */
    public function getTopAuthors(int $limit = 8): array
    {
        $all = $this->getAuthorsList();

        // Sort by article count descending
        usort($all, function ($a, $b) {
            if ($b['count'] === $a['count']) {
                return strcasecmp($a['name'], $b['name']);
            }
            return $b['count'] <=> $a['count'];
        });

        return array_slice($all, 0, $limit);
    }

    /**
     * Harvest all records from OJS OAI-PMH endpoint and store them in database.
     *
     * @param callable|null $progressCallback function(int $savedCount, ?int $totalEstimated)
     * @return array [saved => int, errors => array]
     */
    public function harvestAll(?callable $progressCallback = null): array
    {
        $savedCount = 0;
        $errors = [];
        $resumptionToken = null;

        do {
            try {
                $params = $resumptionToken 
                    ? ['verb' => 'ListRecords', 'resumptionToken' => $resumptionToken]
                    : ['verb' => 'ListRecords', 'metadataPrefix' => 'oai_dc'];

                $response = Http::timeout(30)
                    ->retry(2, 1000)
                    ->withHeaders([
                        'User-Agent' => 'SIBIMA-Fasilkom-Harvester/1.0',
                    ])
                    ->get($this->oaiBaseUrl, $params);

                if (!$response->successful()) {
                    $errors[] = "HTTP Error {$response->status()} while fetching OAI records";
                    break;
                }

                $xmlContent = $response->body();
                $xml = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOCDATA);

                if (!$xml || !isset($xml->ListRecords)) {
                    $errors[] = "Failed to parse OAI XML response";
                    break;
                }

                $records = $xml->ListRecords->record ?? [];
                foreach ($records as $record) {
                    $header = $record->header ?? null;
                    if (!$header || (isset($header['status']) && (string) $header['status'] === 'deleted')) {
                        continue;
                    }

                    $metadata = $record->metadata ? $record->metadata->children('http://www.openarchives.org/OAI/2.0/oai_dc/')->dc : null;
                    if (!$metadata) {
                        continue;
                    }

                    $parsed = $this->parseRecord($header, $metadata);
                    if (!empty($parsed['title']) && !empty($parsed['identifier'])) {
                        FasilkomJournal::updateOrCreate(
                            ['identifier' => $parsed['identifier']],
                            $parsed
                        );
                        $savedCount++;
                    }
                }

                // Check resumption token
                $tokenElement = $xml->ListRecords->resumptionToken ?? null;
                $completeListSize = $tokenElement && isset($tokenElement['completeListSize']) ? (int) $tokenElement['completeListSize'] : null;

                if ($progressCallback) {
                    $progressCallback($savedCount, $completeListSize);
                }

                $resumptionToken = $tokenElement ? trim((string) $tokenElement) : null;

            } catch (\Throwable $e) {
                Log::error("FasilkomJournalService harvest error: {$e->getMessage()}");
                $errors[] = $e->getMessage();
                break;
            }

        } while (!empty($resumptionToken));

        return [
            'saved' => $savedCount,
            'errors' => $errors,
        ];
    }

    /**
     * Parse single Dublin Core OAI record into array for model.
     */
    protected function parseRecord($header, $dc): array
    {
        $dcElements = $dc->children('http://purl.org/dc/elements/1.1/');

        $identifier = (string) ($header->identifier ?? '');
        $articleId = null;
        if (preg_match('/article\/(\d+)/', $identifier, $m)) {
            $articleId = (int) $m[1];
        }

        // Title
        $title = trim(html_entity_decode((string) ($dcElements->title ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        // Creators / Authors
        $authors = [];
        if (isset($dcElements->creator)) {
            foreach ($dcElements->creator as $c) {
                $rawAuthor = trim(html_entity_decode((string) $c, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                if (!empty($rawAuthor)) {
                    // Normalize "Last, First" into "First Last" if comma present
                    if (str_contains($rawAuthor, ',')) {
                        $parts = array_map('trim', explode(',', $rawAuthor, 2));
                        $authors[] = $parts[1] . ' ' . $parts[0];
                    } else {
                        $authors[] = $rawAuthor;
                    }
                }
            }
        }
        $authorsString = implode(', ', $authors);

        // Description / Abstract
        $abstract = '';
        if (isset($dcElements->description)) {
            $abstract = trim(html_entity_decode((string) $dcElements->description, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        // Subjects / Keywords
        $subjects = [];
        if (isset($dcElements->subject)) {
            foreach ($dcElements->subject as $s) {
                $sub = trim(html_entity_decode((string) $s, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                if (!empty($sub)) {
                    $subjects[] = $sub;
                }
            }
        }

        // Date & Year
        $dateStr = isset($dcElements->date) ? trim((string) $dcElements->date) : null;
        $year = null;
        if (!empty($dateStr) && preg_match('/(\d{4})/', $dateStr, $ym)) {
            $year = (int) $ym[1];
        }

        // Source: e.g. "Global; Vol. 5 No. 1 (2018): GLOBAL; 1-12"
        $volume = null;
        $issue = null;
        $pages = null;
        $issn = null;

        if (isset($dcElements->source)) {
            foreach ($dcElements->source as $src) {
                $srcText = (string) $src;
                if (preg_match('/Vol\.?\s*(\d+)/i', $srcText, $vm)) {
                    $volume = $vm[1];
                }
                if (preg_match('/No\.?\s*(\d+)/i', $srcText, $im)) {
                    $issue = $im[1];
                }
                if (preg_match('/;\s*(\d+\s*-\s*\d+)/', $srcText, $pm)) {
                    $pages = trim($pm[1]);
                }
                if (preg_match('/\b\d{4}-\d{4}\b/', $srcText, $issnM)) {
                    $issn = $issnM[0];
                }
            }
        }

        // Identifier & Relation (URLs)
        $landingPageUrl = "https://ejournal.unsub.ac.id/index.php/FASILKOM/article/view/{$articleId}";
        $pdfUrl = null;
        $doi = null;

        if (isset($dcElements->identifier)) {
            foreach ($dcElements->identifier as $idElem) {
                $val = trim((string) $idElem);
                if (str_starts_with($val, 'http') && str_contains($val, '/article/view/')) {
                    $landingPageUrl = $val;
                } elseif (str_contains($val, 'doi.org/') || str_starts_with($val, '10.')) {
                    $doi = str_starts_with($val, 'http') ? $val : "https://doi.org/{$val}";
                }
            }
        }

        if (isset($dcElements->relation)) {
            foreach ($dcElements->relation as $relElem) {
                $val = trim((string) $relElem);
                if (str_starts_with($val, 'http') && str_contains($val, '/article/view/')) {
                    $pdfUrl = $val;
                }
            }
        }

        // Publisher
        $publisher = isset($dcElements->publisher) ? trim((string) $dcElements->publisher) : 'Fakultas Ilmu Komputer Universitas Subang';

        return [
            'identifier' => $identifier,
            'article_id' => $articleId,
            'title' => $title,
            'authors' => $authors,
            'authors_string' => $authorsString,
            'abstract' => $abstract,
            'subjects' => $subjects,
            'publication_date' => $dateStr,
            'year' => $year,
            'volume' => $volume,
            'issue' => $issue,
            'pages' => $pages,
            'landing_page_url' => $landingPageUrl,
            'pdf_url' => $pdfUrl,
            'doi' => $doi,
            'publisher' => $publisher,
            'issn' => $issn,
        ];
    }
}

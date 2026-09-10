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
     * @param array $options [page, per_page, year_filter, sort]
     * @return array
     */
    public function search(string $query, array $options = []): array
    {
        $page = max(1, (int) ($options['page'] ?? 1));
        $perPage = min(25, max(5, (int) ($options['per_page'] ?? 12)));
        $yearFilter = $options['year_filter'] ?? 'all';
        $sort = $options['sort'] ?? 'relevance';

        $builder = FasilkomJournal::query();

        if (trim($query) !== '') {
            $builder->search($query);
        }

        if (!empty($yearFilter) && $yearFilter !== 'all') {
            $builder->filterYear($yearFilter);
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
        ];
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

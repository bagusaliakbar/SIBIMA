<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FasilkomJournal extends Model
{
    use HasFactory;

    protected $table = 'fasilkom_journals';

    protected $fillable = [
        'identifier',
        'article_id',
        'title',
        'authors',
        'authors_string',
        'abstract',
        'subjects',
        'publication_date',
        'year',
        'volume',
        'issue',
        'pages',
        'landing_page_url',
        'pdf_url',
        'doi',
        'publisher',
        'issn',
    ];

    protected $casts = [
        'authors' => 'array',
        'subjects' => 'array',
        'year' => 'integer',
    ];

    /**
     * Scope query to search keywords in title, author, abstract, or subjects.
     */
    public function scopeSearch($query, ?string $term)
    {
        $term = trim($term ?? '');
        if ($term === '') {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('authors_string', 'like', "%{$term}%")
              ->orWhere('abstract', 'like', "%{$term}%")
              ->orWhere('subjects', 'like', "%{$term}%");
        });
    }

    /**
     * Scope query to filter by publication year.
     */
    public function scopeFilterYear($query, ?string $filter)
    {
        if (empty($filter) || $filter === 'all') {
            return $query;
        }

        $currentYear = (int) date('Y');

        if ($filter === '3_years') {
            return $query->where('year', '>=', $currentYear - 2);
        }

        if ($filter === '5_years') {
            return $query->where('year', '>=', $currentYear - 4);
        }

        if ($filter === '10_years') {
            return $query->where('year', '>=', $currentYear - 9);
        }

        if (is_numeric($filter)) {
            return $query->where('year', (int) $filter);
        }

        return $query;
    }

    /**
     * Transform the Eloquent model into normalized journal item array.
     */
    public function toJournalItem(): array
    {
        $venue = 'GLOBAL: Jurnal Fakultas Ilmu Komputer UNSUB';
        if ($this->volume && $this->issue) {
            $venue .= " (Vol. {$this->volume} No. {$this->issue})";
        } elseif ($this->volume) {
            $venue .= " (Vol. {$this->volume})";
        }

        $authors = $this->authors ?: [];
        $citations = $this->generateCitations($authors);

        $concepts = [];
        if (!empty($this->subjects)) {
            foreach ($this->subjects as $subject) {
                $concepts[] = ['name' => $subject];
            }
        }

        return [
            'id' => 'fasilkom_' . $this->id,
            'title' => $this->title,
            'authors' => $authors,
            'authors_string' => $this->authors_string ?: 'FASILKOM UNSUB',
            'publication_date' => $this->publication_date ?: ($this->year ? (string) $this->year : '-'),
            'year' => $this->year,
            'venue' => $venue,
            'doi' => $this->doi,
            'landing_page_url' => $this->landing_page_url,
            'pdf_url' => $this->pdf_url,
            'is_oa' => true,
            'oa_url' => $this->pdf_url ?: $this->landing_page_url,
            'cited_by_count' => 0,
            'abstract' => $this->abstract,
            'concepts' => $concepts,
            'citations' => $citations,
            'source' => 'fasilkom',
            'source_label' => 'Jurnal GLOBAL FASILKOM UNSUB',
            'volume' => $this->volume,
            'issue' => $this->issue,
            'pages' => $this->pages,
        ];
    }

    /**
     * Generate standard citations (APA 7th, IEEE, BibTeX).
     */
    public function generateCitations(array $authors): array
    {
        $title = rtrim($this->title, '. ');
        $year = $this->year ?: (int) date('Y');
        $venue = 'GLOBAL: Jurnal Fakultas Ilmu Komputer';
        $volIssue = '';
        if ($this->volume && $this->issue) {
            $volIssue = "{$this->volume}({$this->issue})";
        } elseif ($this->volume) {
            $volIssue = $this->volume;
        }

        $pages = $this->pages ? ", {$this->pages}" : '';
        $url = $this->doi ?: $this->landing_page_url;

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
            $apaAuthorStr = 'Penulis FASILKOM';
        }

        $apa = "{$apaAuthorStr} ({$year}). {$title}. {$venue}";
        if ($volIssue) {
            $apa .= ", {$volIssue}{$pages}";
        }
        $apa .= ". {$url}";

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
        $ieeeAuthorStr = implode(', ', $ieeeAuthors) ?: 'Penulis FASILKOM';
        $ieee = "{$ieeeAuthorStr}, \"{$title},\" {$venue}";
        if ($volIssue) {
            $ieee .= ", vol. {$this->volume}, no. {$this->issue}{$pages}";
        }
        $ieee .= ", {$year}. [Online]. Available: {$url}";

        // 3. BibTeX format
        $firstAuthorLastName = 'Fasilkom';
        if (!empty($authors[0])) {
            $parts = preg_split('/\s+/', trim($authors[0]));
            $firstAuthorLastName = preg_replace('/[^a-zA-Z]/', '', end($parts)) ?: 'Author';
        }
        $bibtexKey = strtolower($firstAuthorLastName) . $year . ($this->article_id ?: 'article');
        $bibtexAuthors = implode(' and ', $authors) ?: 'FASILKOM UNSUB';

        $bibtex = "@article{{$bibtexKey},\n"
            . "  title = {{$title}},\n"
            . "  author = {{$bibtexAuthors}},\n"
            . "  journal = {{$venue}},\n"
            . ($this->volume ? "  volume = {{$this->volume}},\n" : "")
            . ($this->issue ? "  number = {{$this->issue}},\n" : "")
            . ($this->pages ? "  pages = {{$this->pages}},\n" : "")
            . "  year = {{$year}},\n"
            . "  url = {{$url}}\n"
            . "}";

        return [
            'apa' => $apa,
            'ieee' => $ieee,
            'bibtex' => $bibtex,
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalBookmark extends Model
{
    use HasFactory;

    protected $table = 'journal_bookmarks';

    protected $fillable = [
        'user_id',
        'journal_identifier',
        'title',
        'authors',
        'authors_string',
        'year',
        'venue',
        'publisher',
        'doi',
        'url',
        'pdf_url',
        'abstract',
        'source',
        'source_label',
        'citations',
        'notes',
    ];

    protected $casts = [
        'authors' => 'array',
        'citations' => 'array',
        'year' => 'integer',
    ];

    /**
     * The student / user who saved this journal bookmark.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get clean source label.
     */
    public function getCleanSourceLabelAttribute(): string
    {
        return match ($this->source) {
            'fasilkom' => 'Jurnal GLOBAL FASILKOM UNSUB',
            'garuda' => 'Jurnal Nasional GARUDA (SINTA)',
            'doaj' => 'DOAJ Open Access',
            'crossref' => 'Crossref DOI Registry',
            'openalex' => 'Academic Global (OpenAlex)',
            default => $this->source_label ?: ucfirst($this->source),
        };
    }

    /**
     * Convert this bookmark to a standard journal card item array for blade templates.
     */
    public function toJournalItem(): array
    {
        return [
            'id' => $this->journal_identifier,
            'title' => $this->title,
            'authors' => $this->authors ?: [],
            'authors_string' => $this->authors_string ?: 'Penulis Tidak Diketahui',
            'year' => $this->year,
            'venue' => $this->venue,
            'publisher' => $this->publisher,
            'doi' => $this->doi,
            'landing_page_url' => $this->url,
            'pdf_url' => $this->pdf_url,
            'abstract' => $this->abstract,
            'source' => $this->source,
            'source_label' => $this->clean_source_label,
            'citations' => $this->citations ?: [],
            'is_oa' => !empty($this->pdf_url),
            'cited_by_count' => 0,
            'bookmark_id' => $this->id,
            'notes' => $this->notes,
            'saved_at' => $this->created_at?->locale('id')->translatedFormat('d F Y H:i'),
        ];
    }
}

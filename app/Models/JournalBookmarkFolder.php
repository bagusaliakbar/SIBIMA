<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalBookmarkFolder extends Model
{
    use HasFactory;

    protected $table = 'journal_bookmark_folders';

    protected $fillable = [
        'user_id',
        'name',
        'color',
        'description',
    ];

    /**
     * The student / user who owns this folder.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Bookmarks stored inside this folder.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(JournalBookmark::class, 'folder_id');
    }
}

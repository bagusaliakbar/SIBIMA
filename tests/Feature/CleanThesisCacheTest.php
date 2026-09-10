<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

test('repositories clean cache handles empty folder gracefully', function () {
    Storage::fake('public');

    $this->artisan('repositories:clean-cache')
        ->expectsOutputToContain("Folder cache 'theses_cache' belum ada atau masih kosong.")
        ->assertSuccessful();
});

test('repositories clean cache deletes files older than threshold days', function () {
    Storage::fake('public');

    // Create simulated cache files
    $oldFile = 'theses_cache/bab1/old_cached_doc.pdf';
    $newFile = 'theses_cache/bab2/new_cached_doc.pdf';

    Storage::disk('public')->put($oldFile, '%PDF-1.4 old content');
    Storage::disk('public')->put($newFile, '%PDF-1.4 new content');

    // Manually adjust old file timestamp to 10 days ago
    $oldFullPath = Storage::disk('public')->path($oldFile);
    touch($oldFullPath, Carbon::now()->subDays(10)->timestamp);

    $this->artisan('repositories:clean-cache', ['--days' => 7])
        ->expectsOutputToContain('File dihapus        : 1')
        ->assertSuccessful();

    expect(Storage::disk('public')->exists($oldFile))->toBeFalse();
    expect(Storage::disk('public')->exists($newFile))->toBeTrue();
});

test('repositories clean cache with --all flag clears all cached pdfs', function () {
    Storage::fake('public');

    Storage::disk('public')->put('theses_cache/bab1/doc1.pdf', '%PDF-1.4 content 1');
    Storage::disk('public')->put('theses_cache/bab3/doc2.pdf', '%PDF-1.4 content 2');

    $this->artisan('repositories:clean-cache', ['--all' => true])
        ->expectsOutputToContain('Berhasil membersihkan SEMUA cache repositori skripsi.')
        ->expectsOutputToContain('File dihapus        : 2')
        ->assertSuccessful();

    expect(Storage::disk('public')->exists('theses_cache/bab1/doc1.pdf'))->toBeFalse();
    expect(Storage::disk('public')->exists('theses_cache/bab3/doc2.pdf'))->toBeFalse();
});

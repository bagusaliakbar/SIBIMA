<?php

namespace App\Services;

use App\Models\ThesisRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UnsubRepositorySyncService
{
    public const API_URL = 'https://repository.unsub.ac.id/api/documents';
    public const GDRIVE_PROXY_BASE = 'https://repository.unsub.ac.id/api/gdrive-proxy/';

    /**
     * Fetch all documents from the official UNSUB repository API with caching.
     */
    public function fetchDocuments(bool $forceRefresh = false): array
    {
        $cacheKey = 'unsub_repository_all_documents';

        if (!$forceRefresh && Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && !empty($cached)) {
                return $cached;
            }
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'application/json',
            ])->timeout(30)->withoutVerifying()->get(self::API_URL);

            if (!$response->successful()) {
                Log::error('UnsubRepositorySyncService: Failed to fetch API documents. HTTP Status: ' . $response->status());
                return [];
            }

            $json = $response->json();
            $data = is_array($json) ? ($json['data'] ?? $json) : [];

            if (!empty($data)) {
                Cache::put($cacheKey, $data, now()->addMinutes(15));
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('UnsubRepositorySyncService Exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Strictly check if document belongs to Fakultas Ilmu Komputer / Sistem Informasi.
     */
    public function isFasilkomDocument(array $doc): bool
    {
        $faculty = $doc['fakultas_nama'] ?? ($doc['faculty'] ?? ($doc['fakultas'] ?? ''));
        $prodi = $doc['prodi_nama'] ?? ($doc['department'] ?? ($doc['prodi'] ?? ''));

        $isFakultasMatch = (
            stripos($faculty, 'komputer') !== false ||
            stripos($faculty, 'fasilkom') !== false
        );

        $isProdiMatch = (
            stripos($prodi, 'sistem informasi') !== false ||
            stripos($prodi, 'informatika') !== false
        );

        return $isFakultasMatch || $isProdiMatch;
    }

    /**
     * Filter only documents belonging to Fakultas Ilmu Komputer.
     */
    public function filterFasilkom(array $documents): array
    {
        return array_values(array_filter($documents, fn($doc) => $this->isFasilkomDocument($doc)));
    }

    /**
     * Strictly extract ONLY file BAB I.
     */
    public function extractBab1File(array $files): ?array
    {
        foreach ($files as $file) {
            $name = $file['file_name'] ?? '';
            // Match BAB I, BAB 1, BAB_1, BAB-I with word boundary
            if (preg_match('/bab\s*[\-_]?(?:i|1)\b/i', $name)) {
                return $file;
            }
        }
        return null;
    }

    /**
     * Strictly extract ONLY file BAB II.
     */
    public function extractBab2File(array $files): ?array
    {
        foreach ($files as $file) {
            $name = $file['file_name'] ?? '';
            // Match BAB II, BAB 2, BAB_2, BAB-II with word boundary
            if (preg_match('/bab\s*[\-_]?(?:ii|2)\b/i', $name)) {
                return $file;
            }
        }
        return null;
    }

    /**
     * Generic download for chapter PDF to local public storage disk.
     */
    public function downloadChapterPdf(string $folder, string $gdriveId, string $localFilename): ?string
    {
        $relativePath = $folder . '/' . $localFilename;

        // If already downloaded locally, return path immediately
        if (Storage::disk('public')->exists($relativePath)) {
            return $relativePath;
        }

        try {
            $streamUrl = self::GDRIVE_PROXY_BASE . $gdriveId;
            $res = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            ])->timeout(15)->withoutVerifying()->get($streamUrl);

            if ($res->successful()) {
                $body = $res->body();
                // Validate PDF magic bytes (%PDF)
                if (str_starts_with($body, '%PDF')) {
                    Storage::disk('public')->put($relativePath, $body);
                    return $relativePath;
                }
            }

            // Fallback to proxy URL if download didn't return valid PDF
            Log::warning("UnsubRepositorySyncService: Failed to download PDF for {$gdriveId}, saving remote proxy URL as fallback.");
            return $streamUrl;
        } catch (\Exception $e) {
            Log::warning("UnsubRepositorySyncService: Download timeout/error for {$gdriveId}: " . $e->getMessage());
            return self::GDRIVE_PROXY_BASE . $gdriveId;
        }
    }

    /**
     * Download BAB 1 PDF to local storage, or return proxy URL on failure.
     */
    public function downloadBab1Pdf(string $gdriveId, string $localFilename): ?string
    {
        return $this->downloadChapterPdf('theses_bab1', $gdriveId, $localFilename);
    }

    /**
     * Download BAB 2 PDF to local storage, or return proxy URL on failure.
     */
    public function downloadBab2Pdf(string $gdriveId, string $localFilename): ?string
    {
        return $this->downloadChapterPdf('theses_bab2', $gdriveId, $localFilename);
    }

    /**
     * Process and sync a single document record.
     */
    public function syncDocument(array $doc, bool $downloadPdf = true): array
    {
        if (!$this->isFasilkomDocument($doc)) {
            return [
                'status' => 'skipped',
                'reason' => 'Bukan dokumen Fakultas Ilmu Komputer (' . ($doc['fakultas_nama'] ?? 'Unknown') . ')'
            ];
        }

        $title = trim($doc['judul'] ?? '');
        $name = trim($doc['penulis'] ?? '');
        $npm = trim($doc['npm'] ?? '');
        $extractedYear = ThesisRepository::extractYearFromIdentifier($npm);
        $year = $extractedYear ?: (int) ($doc['tahun'] ?? date('Y'));
        $abstract = trim($doc['abstrak'] ?? '');
        
        $p1 = !empty($doc['dosen_pembimbing']) 
            ? trim(preg_replace('/^\d+[\.\)]\s*/', '', $doc['dosen_pembimbing'])) 
            : null;
        $p2 = !empty($doc['dosen_pembimbing_2']) 
            ? trim(preg_replace('/^\d+[\.\)]\s*/', '', $doc['dosen_pembimbing_2'])) 
            : null;

        if (empty($title) || empty($name)) {
            return [
                'status' => 'skipped',
                'reason' => 'Judul atau nama penulis kosong'
            ];
        }

        // Clean filename prefix
        $cleanNpm = preg_replace('/[^a-zA-Z0-9]/', '', $npm);
        $cleanName = substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 20);
        $fileIdentifier = $cleanNpm ?: ($cleanName ?: substr(md5($title), 0, 10));

        // Extract BAB 1 & BAB 2
        $bab1 = $this->extractBab1File($doc['files'] ?? []);
        $bab2 = $this->extractBab2File($doc['files'] ?? []);
        $filePathBab1 = null;
        $filePathBab2 = null;

        if ($bab1 && !empty($bab1['file_path'])) {
            $gdriveId1 = $bab1['file_path'];
            if ($downloadPdf) {
                $filePathBab1 = $this->downloadBab1Pdf($gdriveId1, "{$fileIdentifier}_BAB1.pdf");
            } else {
                $filePathBab1 = self::GDRIVE_PROXY_BASE . $gdriveId1;
            }
        }

        if ($bab2 && !empty($bab2['file_path'])) {
            $gdriveId2 = $bab2['file_path'];
            if ($downloadPdf) {
                $filePathBab2 = $this->downloadBab2Pdf($gdriveId2, "{$fileIdentifier}_BAB2.pdf");
            } else {
                $filePathBab2 = self::GDRIVE_PROXY_BASE . $gdriveId2;
            }
        }

        // Lookup existing record by NPM or exact Title
        $existing = null;
        if (!empty($npm) && strlen($npm) >= 5) {
            $existing = ThesisRepository::where('identifier', $npm)->first();
        }
        if (!$existing) {
            $existing = ThesisRepository::where('title', $title)->first();
        }

        if ($existing) {
            // Smart Enrichment: update missing abstract, file_path, file_path_bab2, and advisors
            $updates = [];
            if (empty($existing->abstract) && !empty($abstract)) {
                $updates['abstract'] = $abstract;
            }
            if (!empty($filePathBab1) && (empty($existing->file_path) || $existing->file_path !== $filePathBab1)) {
                $updates['file_path'] = $filePathBab1;
            }
            if (!empty($filePathBab2) && (empty($existing->file_path_bab2) || $existing->file_path_bab2 !== $filePathBab2)) {
                $updates['file_path_bab2'] = $filePathBab2;
            }
            if (empty($existing->pembimbing1) && !empty($p1)) {
                $updates['pembimbing1'] = $p1;
            }
            if (empty($existing->pembimbing2) && !empty($p2)) {
                $updates['pembimbing2'] = $p2;
            }
            if (empty($existing->identifier) && !empty($npm)) {
                $updates['identifier'] = $npm;
            }
            if ($extractedYear && $existing->year !== $extractedYear) {
                $updates['year'] = $extractedYear;
            } elseif (empty($existing->year) && !empty($year)) {
                $updates['year'] = $year;
            }

            if (!empty($updates)) {
                $existing->update($updates);
            }

            return [
                'status' => 'enriched',
                'title' => $title,
                'name' => $name,
                'npm' => $npm,
                'has_bab1' => !empty($filePathBab1),
                'has_bab2' => !empty($filePathBab2),
                'repo' => $existing
            ];
        }

        // Create new record
        $repo = ThesisRepository::create([
            'identifier' => $npm ?: null,
            'name' => $name,
            'year' => $year ?: (int) date('Y'),
            'title' => $title,
            'abstract' => $abstract ?: null,
            'pembimbing1' => $p1,
            'pembimbing2' => $p2,
            'file_path' => $filePathBab1,
            'file_path_bab2' => $filePathBab2,
        ]);

        return [
            'status' => 'created',
            'title' => $title,
            'name' => $name,
            'npm' => $npm,
            'has_bab1' => !empty($filePathBab1),
            'has_bab2' => !empty($filePathBab2),
            'repo' => $repo
        ];
    }
}

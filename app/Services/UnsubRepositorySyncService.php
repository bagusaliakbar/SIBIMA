<?php

namespace App\Services;

use App\Models\ThesisRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UnsubRepositorySyncService
{
    public const API_URL = 'https://repository.unsub.ac.id/api/documents';
    public const GDRIVE_PROXY_BASE = 'https://repository.unsub.ac.id/api/gdrive-proxy/';

    /**
     * Fetch all documents from the official UNSUB repository API.
     */
    public function fetchDocuments(): array
    {
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
            return is_array($json) ? ($json['data'] ?? $json) : [];
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
     * Ignores BAB II, III, IV, V, VI and any other file.
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
     * Download BAB 1 PDF to local storage, or return proxy URL on failure.
     */
    public function downloadBab1Pdf(string $gdriveId, string $localFilename): ?string
    {
        $relativePath = 'theses_bab1/' . $localFilename;

        // If already downloaded locally, return path immediately
        if (Storage::disk('public')->exists($relativePath)) {
            return $relativePath;
        }

        try {
            $streamUrl = self::GDRIVE_PROXY_BASE . $gdriveId;
            $res = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            ])->timeout(45)->withoutVerifying()->get($streamUrl);

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
        $year = (int) ($doc['tahun'] ?? date('Y'));
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

        // Strictly extract BAB 1
        $bab1 = $this->extractBab1File($doc['files'] ?? []);
        $filePath = null;

        if ($bab1 && !empty($bab1['file_path'])) {
            $gdriveId = $bab1['file_path'];
            if ($downloadPdf) {
                $cleanNpm = preg_replace('/[^a-zA-Z0-9]/', '', $npm);
                $cleanName = substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 20);
                $fileIdentifier = $cleanNpm ?: ($cleanName ?: substr(md5($title), 0, 10));
                $localFilename = "{$fileIdentifier}_BAB1.pdf";
                $filePath = $this->downloadBab1Pdf($gdriveId, $localFilename);
            } else {
                $filePath = self::GDRIVE_PROXY_BASE . $gdriveId;
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
            // Smart Enrichment: update missing abstract, file_path, and advisors
            $updates = [];
            if (empty($existing->abstract) && !empty($abstract)) {
                $updates['abstract'] = $abstract;
            }
            if (empty($existing->file_path) && !empty($filePath)) {
                $updates['file_path'] = $filePath;
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
            if (empty($existing->year) && !empty($year)) {
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
                'has_bab1' => !empty($filePath),
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
            'file_path' => $filePath,
        ]);

        return [
            'status' => 'created',
            'title' => $title,
            'name' => $name,
            'npm' => $npm,
            'has_bab1' => !empty($filePath),
            'repo' => $repo
        ];
    }
}

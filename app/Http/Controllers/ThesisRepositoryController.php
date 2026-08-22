<?php

namespace App\Http\Controllers;

use App\Models\ThesisRepository;
use App\Models\User;
use App\Exports\RepositoryTemplateExport;
use App\Exports\RepositoryCatalogExport;
use App\Imports\ThesisRepositoriesImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ThesisRepositoryController extends Controller
{
    public function exportExcel(Request $request)
    {
        if (Auth::user()->role === 'mahasiswa') {
            abort(403, 'Akses ekspor tidak diizinkan untuk mahasiswa.');
        }

        $search = $request->input('search');
        $year = $request->input('year');
        $advisor = $request->input('advisor');
        $topic = $request->input('topic', 'all');

        $fileName = 'katalog-pustaka-' . ($year ? "angkatan-{$year}-" : '') . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new RepositoryCatalogExport($search, $year, $advisor, $topic), $fileName);
    }

    public function exportPdf(Request $request)
    {
        if (Auth::user()->role === 'mahasiswa') {
            abort(403, 'Akses ekspor tidak diizinkan untuk mahasiswa.');
        }

        $search = $request->input('search');
        $year = $request->input('year');
        $advisor = $request->input('advisor');
        $topic = $request->input('topic', 'all');

        $query = ThesisRepository::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('identifier', 'like', "%{$search}%")
                  ->orWhere('abstract', 'like', "%{$search}%")
                  ->orWhere('pembimbing1', 'like', "%{$search}%")
                  ->orWhere('pembimbing2', 'like', "%{$search}%");
            });
        }

        if ($year) {
            $query->where('year', $year);
        }

        if ($advisor) {
            $cleanAdv = preg_replace('/^(drs\.|dr\.|ir\.|prof\.|h\.|hj\.)\s+/i', '', preg_replace('/^\d+[\.\)]\s*/', '', trim($advisor)));
            $baseAdvName = trim(explode(',', $cleanAdv)[0]);

            $query->where(function($q) use ($advisor, $baseAdvName) {
                $q->where('pembimbing1', 'like', "%{$baseAdvName}%")
                  ->orWhere('pembimbing2', 'like', "%{$baseAdvName}%")
                  ->orWhere('pembimbing1', 'like', "%{$advisor}%")
                  ->orWhere('pembimbing2', 'like', "%{$advisor}%");
            });
        }

        if ($topic && $topic !== 'all') {
            $topicKeywords = match($topic) {
                'web' => ['web', 'website', 'portal', 'sistem informasi'],
                'mobile' => ['android', 'mobile', 'flutter', 'ios', 'smartphone'],
                'spk' => ['spk', 'pendukung keputusan', 'ahp', 'saw', 'topsis', 'smart', 'profile matching', 'moora', 'vikor', 'mabac', 'promethee'],
                'ai' => ['machine learning', 'deep learning', 'klasifikasi', 'clustering', 'k-means', 'naive bayes', 'svm', 'c4.5', 'decision tree', 'neural network', 'cnn', 'nlp', 'yolo', 'fuzzy', 'algoritma genetika'],
                'ui_ux' => ['ui/ux', 'ui ', 'ux ', 'human-centered', 'human centered', 'design thinking', 'usability', 'user experience', 'user interface'],
                'iot' => ['iot', 'internet of things', 'arduino', 'raspberry', 'sensor', 'mikrokontroler', 'jaringan', 'mikrotik', 'keamanan'],
                'ecommerce' => ['e-commerce', 'penjualan', 'marketplace', 'toko online', 'pos ', 'point of sale', 'pemesanan', 'kasir'],
                default => [$topic]
            };

            $query->where(function($q) use ($topicKeywords) {
                foreach ($topicKeywords as $kw) {
                    $q->orWhere('title', 'like', "%{$kw}%")
                      ->orWhere('abstract', 'like', "%{$kw}%");
                }
            });
        }

        $repositories = $query->orderBy('year', 'desc')->orderBy('name', 'asc')->get();
        $kaprodi = User::where('role', 'kaprodi')->first() ?? User::where('role', 'admin')->first();

        $pdf = Pdf::loadView('repositories.pdf', compact('repositories', 'kaprodi', 'search', 'year', 'advisor', 'topic'));
        $pdf->setPaper('a4', 'landscape');

        $fileName = 'katalog-pustaka-' . ($year ? "angkatan-{$year}-" : '') . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($fileName);
    }
    public function index(Request $request)
    {
        $search = $request->input('search');
        $year = $request->input('year');
        $advisor = $request->input('advisor');
        $topic = $request->input('topic', 'all');
        
        $query = ThesisRepository::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('identifier', 'like', "%{$search}%")
                  ->orWhere('abstract', 'like', "%{$search}%")
                  ->orWhere('pembimbing1', 'like', "%{$search}%")
                  ->orWhere('pembimbing2', 'like', "%{$search}%");
            });
        }

        if ($year) {
            $query->where('year', $year);
        }

        if ($advisor) {
            // Find base keyword from advisor parameter to match all uppercase/lowercase/degree variants
            $cleanAdv = preg_replace('/^(drs\.|dr\.|ir\.|prof\.|h\.|hj\.)\s+/i', '', preg_replace('/^\d+[\.\)]\s*/', '', trim($advisor)));
            $baseAdvName = trim(explode(',', $cleanAdv)[0]);
            
            $query->where(function($q) use ($advisor, $baseAdvName) {
                $q->where('pembimbing1', 'like', "%{$baseAdvName}%")
                  ->orWhere('pembimbing2', 'like', "%{$baseAdvName}%")
                  ->orWhere('pembimbing1', 'like', "%{$advisor}%")
                  ->orWhere('pembimbing2', 'like', "%{$advisor}%");
            });
        }

        if ($topic && $topic !== 'all') {
            $topicKeywords = match($topic) {
                'web' => ['web', 'website', 'portal', 'sistem informasi'],
                'mobile' => ['android', 'mobile', 'flutter', 'ios', 'smartphone'],
                'spk' => ['spk', 'pendukung keputusan', 'ahp', 'saw', 'topsis', 'smart', 'profile matching', 'moora', 'vikor', 'mabac', 'promethee'],
                'ai' => ['machine learning', 'deep learning', 'klasifikasi', 'clustering', 'k-means', 'naive bayes', 'svm', 'c4.5', 'decision tree', 'neural network', 'cnn', 'nlp', 'yolo', 'fuzzy', 'algoritma genetika'],
                'ui_ux' => ['ui/ux', 'ui ', 'ux ', 'human-centered', 'human centered', 'design thinking', 'usability', 'user experience', 'user interface'],
                'iot' => ['iot', 'internet of things', 'arduino', 'raspberry', 'sensor', 'mikrokontroler', 'jaringan', 'mikrotik', 'keamanan'],
                'ecommerce' => ['e-commerce', 'penjualan', 'marketplace', 'toko online', 'pos ', 'point of sale', 'pemesanan', 'kasir'],
                default => [$topic]
            };

            $query->where(function($q) use ($topicKeywords) {
                foreach ($topicKeywords as $kw) {
                    $q->orWhere('title', 'like', "%{$kw}%")
                      ->orWhere('abstract', 'like', "%{$kw}%");
                }
            });
        }

        $totalCount = ThesisRepository::count();
        $filteredCount = (clone $query)->count();
        $repositories = $query->orderBy('year', 'desc')->orderBy('name', 'asc')->paginate(12)->withQueryString();

        // Get unique years for filter
        $years = ThesisRepository::select('year')->whereNotNull('year')->where('year', '!=', '')->distinct()->orderBy('year', 'desc')->pluck('year');

        // Compile distinct deduplicated advisors from ThesisRepository and active lecturers
        $advisors = $this->getNormalizedAdvisorsList();

        $minYear = $years->min();
        $maxYear = $years->max();
        $yearRange = ($minYear && $maxYear) ? ($minYear == $maxYear ? $minYear : "{$minYear} – {$maxYear}") : '-';

        // Count topics distribution dynamically
        $topicStats = [];
        $sampleRepos = ThesisRepository::select('title')->get();
        foreach ($sampleRepos as $r) {
            $badge = $r->topic_badge;
            $lbl = $badge['label'] ?? 'Lainnya';
            $topicStats[$lbl] = ($topicStats[$lbl] ?? 0) + 1;
        }
        arsort($topicStats);
        $topTopicLabel = key($topicStats) ?? 'Web App & SI';
        $topTopicCount = current($topicStats) ?: 0;

        $stats = [
            'total' => $totalCount,
            'year_range' => $yearRange,
            'total_years' => $years->count(),
            'total_advisors' => count($advisors),
            'top_topic' => $topTopicLabel,
            'top_topic_count' => $topTopicCount,
        ];

        $topics = [
            'all' => ['label' => 'Semua Topik', 'icon' => 'sparkles'],
            'web' => ['label' => 'Web App & SI', 'icon' => 'globe'],
            'mobile' => ['label' => 'Mobile / Android', 'icon' => 'device-mobile'],
            'ai' => ['label' => 'AI & Data Science', 'icon' => 'cpu-chip'],
            'spk' => ['label' => 'SPK / Keputusan', 'icon' => 'chart-bar'],
            'ui_ux' => ['label' => 'UI/UX & HCD', 'icon' => 'paint-brush'],
            'iot' => ['label' => 'IoT & Hardware', 'icon' => 'wifi'],
            'ecommerce' => ['label' => 'E-Commerce / POS', 'icon' => 'shopping-cart'],
        ];

        return view('repositories.index', compact(
            'repositories', 'search', 'year', 'advisor', 'topic', 'years', 'advisors', 'topics', 'totalCount', 'filteredCount', 'stats'
        ));
    }

    /**
     * Normalize and deduplicate advisors across repository records and active lecturers.
     */
    private function getNormalizedAdvisorsList(): array
    {
        $p1 = ThesisRepository::whereNotNull('pembimbing1')->where('pembimbing1', '!=', '')->distinct()->pluck('pembimbing1');
        $p2 = ThesisRepository::whereNotNull('pembimbing2')->where('pembimbing2', '!=', '')->distinct()->pluck('pembimbing2');
        $dosenUsers = \App\Models\User::where('role', 'dosen')->pluck('name');

        $rawList = $p1->concat($p2)->concat($dosenUsers);
        $groups = [];

        foreach ($rawList as $raw) {
            $trimmed = trim(preg_replace('/^\d+[\.\)]\s*/', '', $raw));
            if (empty($trimmed) || strlen($trimmed) < 3) continue;

            // Extract base name without title prefixes or comma degrees
            $base = preg_replace('/^(drs\.|dr\.|ir\.|prof\.|h\.|hj\.)\s+/i', '', $trimmed);
            $baseNameOnly = trim(explode(',', $base)[0]);
            $key = preg_replace('/[^a-z0-9]/', '', strtolower($baseNameOnly));

            if (empty($key) || strlen($key) < 3) continue;

            $isAllUpper = ctype_upper(str_replace([' ', '.', ',', '-', "'", '/'], '', $trimmed));
            $formatted = $trimmed;
            if ($isAllUpper) {
                $formatted = ucwords(strtolower($trimmed));
                // Fix common degree casings
                $formatted = preg_replace_callback('/\b(m\.kom|m\.cs|m\.si|m\.t|mt|s\.kom|s\.si|s\.sos|s\.s|sfc)\b/i', fn($m) => strtoupper($m[0]), $formatted);
            }

            if (!isset($groups[$key])) {
                $groups[$key] = $formatted;
            } else {
                $existing = $groups[$key];
                $existingIsUpper = ctype_upper(str_replace([' ', '.', ',', '-', "'", '/'], '', $existing));
                $newIsUpper = ctype_upper(str_replace([' ', '.', ',', '-', "'", '/'], '', $trimmed));

                // Prefer non-all-caps version, or more complete degree format
                if ($existingIsUpper && !$newIsUpper) {
                    $groups[$key] = $trimmed;
                } elseif (!$newIsUpper && strlen($trimmed) > strlen($existing)) {
                    $groups[$key] = $trimmed;
                }
            }
        }

        // Check if any key matches registered active lecturers in SIBIMA, use their official name
        foreach ($dosenUsers as $dosenName) {
            $base = preg_replace('/^(drs\.|dr\.|ir\.|prof\.|h\.|hj\.)\s+/i', '', trim($dosenName));
            $baseNameOnly = trim(explode(',', $base)[0]);
            $key = preg_replace('/[^a-z0-9]/', '', strtolower($baseNameOnly));
            if (isset($groups[$key])) {
                $groups[$key] = $dosenName;
            }
        }

        $advisors = array_values($groups);
        natcasesort($advisors);

        return array_values($advisors);
    }

    public function cleanAdvisorsData()
    {
        if (!in_array(Auth::user()->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $repositories = ThesisRepository::all();
        $cleanedCount = 0;

        foreach ($repositories as $repo) {
            $p1 = $repo->pembimbing1 ? trim(preg_replace('/^\d+[\.\)]\s*/', '', $repo->pembimbing1)) : null;
            $p2 = $repo->pembimbing2 ? trim(preg_replace('/^\d+[\.\)]\s*/', '', $repo->pembimbing2)) : null;

            if ($p1 !== $repo->pembimbing1 || $p2 !== $repo->pembimbing2) {
                $repo->update([
                    'pembimbing1' => $p1,
                    'pembimbing2' => $p2,
                ]);
                $cleanedCount++;
            }
        }

        return redirect()->route('repositories.index')->with('success', "Berhasil merapikan data pembimbing pada {$cleanedCount} arsip skripsi.");
    }

    public function createImport()
    {
        if (!in_array(Auth::user()->role, ['admin', 'kaprodi'])) {
            abort(403);
        }
        return view('repositories.import');
    }

    public function storeImport(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new ThesisRepositoriesImport, $request->file('file'));
            return redirect()->route('repositories.index')->with('success', 'Data repositori berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        if (!in_array(Auth::user()->role, ['admin', 'kaprodi'])) {
            abort(403);
        }
        return Excel::download(new RepositoryTemplateExport, 'Template_Repositori_Skripsi.xlsx');
    }

    public function syncPage($page)
    {
        if (!in_array(Auth::user()->role, ['admin', 'kaprodi'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $opts = [
                "http" => [
                    "method" => "GET",
                    "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n",
                    "timeout" => 15,
                    "ignore_errors" => true,
                ],
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ]
            ];
            $context = stream_context_create($opts);
            $html = @file_get_contents("https://fasilkom.unsub.ac.id/penelitian-mahasiswa?page={$page}", false, $context);
            
            if (!$html) {
                return response()->json(['success' => false, 'message' => 'Gagal mengakses portal (Timeout)'], 500);
            }
            
            $dom = new \DOMDocument();
            @$dom->loadHTML($html);
            
            $xpath = new \DOMXPath($dom);
            
            // Find all table rows inside .sr-table tbody
            $rows = $xpath->query("//table[contains(@class, 'sr-table')]//tbody//tr");
            
            $count = 0;
            $newCount = 0;
            $duplicateCount = 0;
            
            foreach ($rows as $row) {
                // Name: .student-info-main
                $nameNode = $xpath->query(".//*[contains(@class, 'student-info-main')]", $row);
                $name = $nameNode->length > 0 ? trim($nameNode->item(0)->textContent) : null;
                
                // Meta: .student-meta
                $metaNode = $xpath->query(".//*[contains(@class, 'student-meta')]", $row);
                $npm = null;
                $year = date('Y');
                if ($metaNode->length > 0) {
                    $metaText = $metaNode->item(0)->textContent;
                    if (preg_match('/NPM:\s*([A-Za-z0-9]+)/i', $metaText, $matches)) {
                        $npm = $matches[1];
                    }
                    if (preg_match('/Angkatan\s*(\d{4})/i', $metaText, $matches)) {
                        $year = $matches[1];
                    }
                }
                
                // Title: .thesis-title-premium
                $titleNode = $xpath->query(".//*[contains(@class, 'thesis-title-premium')]", $row);
                $title = $titleNode->length > 0 ? trim($titleNode->item(0)->textContent) : null;
                
                // Supervisor: .supervisor-tag
                $supervisorNodes = $xpath->query(".//*[contains(@class, 'supervisor-tag')]", $row);
                $pembimbing1 = $supervisorNodes->length > 0 ? trim(preg_replace('/^\d+[\.\)]\s*/', '', $supervisorNodes->item(0)->textContent)) : null;
                $pembimbing2 = $supervisorNodes->length > 1 ? trim(preg_replace('/^\d+[\.\)]\s*/', '', $supervisorNodes->item(1)->textContent)) : null;
                
                if ($name && $title) {
                    $repo = ThesisRepository::updateOrCreate(
                        ['title' => $title, 'name' => $name],
                        [
                            'identifier' => $npm,
                            'year' => $year,
                            'pembimbing1' => $pembimbing1,
                            'pembimbing2' => $pembimbing2
                        ]
                    );

                    if ($repo->wasRecentlyCreated) {
                        $newCount++;
                    } else {
                        $duplicateCount++;
                    }
                    $count++;
                }
            }
            
            return response()->json([
                'success' => true,
                'count' => $count,
                'new_count' => $newCount,
                'duplicate_count' => $duplicateCount,
                'message' => "Halaman {$page} berhasil disinkronisasi."
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, ThesisRepository $repository)
    {
        if (!in_array(Auth::user()->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:500',
            'identifier' => 'nullable|string|max:50',
            'year' => 'nullable|integer|digits:4',
            'pembimbing1' => 'nullable|string|max:255',
            'pembimbing2' => 'nullable|string|max:255',
            'abstract' => 'nullable|string',
        ]);

        if (!empty($validated['pembimbing1'])) {
            $validated['pembimbing1'] = trim(preg_replace('/^\d+[\.\)]\s*/', '', $validated['pembimbing1']));
        }
        if (!empty($validated['pembimbing2'])) {
            $validated['pembimbing2'] = trim(preg_replace('/^\d+[\.\)]\s*/', '', $validated['pembimbing2']));
        }

        $repository->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data arsip skripsi berhasil diperbarui.',
                'repository' => $repository
            ]);
        }

        return redirect()->back()->with('success', 'Data arsip skripsi berhasil diperbarui.');
    }

    public function destroy(Request $request, ThesisRepository $repository)
    {
        if (!in_array(Auth::user()->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $title = $repository->title;
        $repository->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Arsip skripsi berhasil dihapus.'
            ]);
        }

        return redirect()->back()->with('success', "Arsip '{$title}' berhasil dihapus dari pustaka.");
    }
}

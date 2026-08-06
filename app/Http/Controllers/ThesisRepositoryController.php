<?php

namespace App\Http\Controllers;

use App\Models\ThesisRepository;
use App\Exports\RepositoryTemplateExport;
use App\Imports\ThesisRepositoriesImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ThesisRepositoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $year = $request->input('year');
        
        $query = ThesisRepository::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('abstract', 'like', "%{$search}%")
                  ->orWhere('pembimbing1', 'like', "%{$search}%")
                  ->orWhere('pembimbing2', 'like', "%{$search}%");
            });
        }

        if ($year) {
            $query->where('year', $year);
        }

        $repositories = $query->orderBy('year', 'desc')->orderBy('name', 'asc')->paginate(12)->withQueryString();

        // Get unique years for filter
        $years = ThesisRepository::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        return view('repositories.index', compact('repositories', 'search', 'year', 'years'));
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
            $html = @file_get_contents("https://fasilkom.unsub.ac.id/penelitian-mahasiswa?page={$page}");
            if (!$html) {
                return response()->json(['success' => false, 'message' => 'Gagal mengakses portal'], 500);
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
                $pembimbing1 = $supervisorNodes->length > 0 ? trim($supervisorNodes->item(0)->textContent) : null;
                $pembimbing2 = $supervisorNodes->length > 1 ? trim($supervisorNodes->item(1)->textContent) : null;
                
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
}

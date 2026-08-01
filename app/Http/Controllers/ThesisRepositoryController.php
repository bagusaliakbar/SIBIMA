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
}

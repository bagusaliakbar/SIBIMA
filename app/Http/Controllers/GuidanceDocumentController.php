<?php

namespace App\Http\Controllers;

use App\Models\GuidanceDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GuidanceDocumentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $category = $request->input('category', 'all');
        $search = $request->input('search');

        $query = GuidanceDocument::query()->with('uploader');

        // Non-admin / non-kaprodi can only see active documents
        if (!in_array($user->role, ['admin', 'kaprodi'])) {
            $query->active();
        }

        if (!empty($category) && $category !== 'all') {
            $query->category($category);
        }

        if (!empty($search)) {
            $query->search($search);
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(12)->appends([
            'category' => $category,
            'search' => $search,
        ]);

        // Category counts
        $baseScope = GuidanceDocument::query();
        if (!in_array($user->role, ['admin', 'kaprodi'])) {
            $baseScope->active();
        }

        $categoryCounts = [
            'all' => (clone $baseScope)->count(),
            'panduan_skripsi' => (clone $baseScope)->where('category', 'panduan_skripsi')->count(),
            'format_template' => (clone $baseScope)->where('category', 'format_template')->count(),
            'pedoman_bimbingan' => (clone $baseScope)->where('category', 'pedoman_bimbingan')->count(),
            'lainnya' => (clone $baseScope)->where('category', 'lainnya')->count(),
        ];

        return view('guidance_documents.index', compact('documents', 'category', 'search', 'categoryCounts'));
    }

    public function store(Request $request)
    {
        $this->authorizeManager();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:panduan_skripsi,format_template,pedoman_bimbingan,lainnya',
            'document_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,zip,rar,7z|max:25600',
        ], [
            'title.required' => 'Judul dokumen panduan wajib diisi.',
            'category.required' => 'Kategori dokumen wajib dipilih.',
            'document_file.required' => 'File dokumen wajib diunggah.',
            'document_file.mimes' => 'Format file harus berupa PDF, DOC, DOCX, XLS, XLSX, atau ZIP.',
            'document_file.max' => 'Ukuran file dokumen maksimal 25 MB.',
        ]);

        $file = $request->file('document_file');
        $disk = config('filesystems.default');
        $path = $file->store('guidance_documents', $disk);

        GuidanceDocument::create([
            'title' => trim($validated['title']),
            'description' => trim($validated['description'] ?? ''),
            'category' => $validated['category'],
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'file_extension' => strtolower($file->getClientOriginalExtension()),
            'download_count' => 0,
            'is_active' => $request->boolean('is_active', true),
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->route('guidance-documents.index')->with('success', 'Dokumen panduan berhasil ditambahkan.');
    }

    public function update(Request $request, GuidanceDocument $guidanceDocument)
    {
        $this->authorizeManager();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:panduan_skripsi,format_template,pedoman_bimbingan,lainnya',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip,rar,7z|max:25600',
        ], [
            'title.required' => 'Judul dokumen panduan wajib diisi.',
            'category.required' => 'Kategori dokumen wajib dipilih.',
            'document_file.mimes' => 'Format file harus berupa PDF, DOC, DOCX, XLS, XLSX, atau ZIP.',
            'document_file.max' => 'Ukuran file dokumen maksimal 25 MB.',
        ]);

        $disk = config('filesystems.default');
        $data = [
            'title' => trim($validated['title']),
            'description' => trim($validated['description'] ?? ''),
            'category' => $validated['category'],
        ];

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            
            // Delete old file if exists
            if ($guidanceDocument->file_path && Storage::disk($disk)->exists($guidanceDocument->file_path)) {
                Storage::disk($disk)->delete($guidanceDocument->file_path);
            }

            $data['file_path'] = $file->store('guidance_documents', $disk);
            $data['original_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['file_extension'] = strtolower($file->getClientOriginalExtension());
        }

        $guidanceDocument->update($data);

        return redirect()->route('guidance-documents.index')->with('success', 'Dokumen panduan berhasil diperbarui.');
    }

    public function destroy(GuidanceDocument $guidanceDocument)
    {
        $this->authorizeManager();

        $disk = config('filesystems.default');
        if ($guidanceDocument->file_path && Storage::disk($disk)->exists($guidanceDocument->file_path)) {
            Storage::disk($disk)->delete($guidanceDocument->file_path);
        }

        $guidanceDocument->delete();

        return redirect()->route('guidance-documents.index')->with('success', 'Dokumen panduan berhasil dihapus.');
    }

    public function toggleStatus(GuidanceDocument $guidanceDocument)
    {
        $this->authorizeManager();

        $guidanceDocument->update([
            'is_active' => !$guidanceDocument->is_active,
        ]);

        $statusText = $guidanceDocument->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Status dokumen panduan berhasil {$statusText}.");
    }

    public function download(GuidanceDocument $guidanceDocument)
    {
        $user = Auth::user();
        if (!$guidanceDocument->is_active && !in_array($user->role, ['admin', 'kaprodi'])) {
            abort(404, 'Dokumen panduan tidak ditemukan atau sedang tidak aktif.');
        }

        $disk = config('filesystems.default');
        if (!$guidanceDocument->file_path || !Storage::disk($disk)->exists($guidanceDocument->file_path)) {
            abort(404, 'File fisik dokumen panduan tidak ditemukan di server.');
        }

        $guidanceDocument->increment('download_count');

        return Storage::disk($disk)->download($guidanceDocument->file_path, $guidanceDocument->original_name);
    }

    public function view(GuidanceDocument $guidanceDocument)
    {
        $user = Auth::user();
        if (!$guidanceDocument->is_active && !in_array($user->role, ['admin', 'kaprodi'])) {
            abort(404, 'Dokumen panduan tidak ditemukan atau sedang tidak aktif.');
        }

        $disk = config('filesystems.default');
        if (!$guidanceDocument->file_path || !Storage::disk($disk)->exists($guidanceDocument->file_path)) {
            abort(404, 'File fisik dokumen panduan tidak ditemukan di server.');
        }

        $ext = strtolower($guidanceDocument->file_extension ?: pathinfo($guidanceDocument->original_name, PATHINFO_EXTENSION));

        // If PDF, stream inline preview in browser
        if ($ext === 'pdf') {
            $path = Storage::disk($disk)->path($guidanceDocument->file_path);
            return response()->file($path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $guidanceDocument->original_name . '"',
            ]);
        }

        // For non-PDF files (e.g. DOCX, ZIP), trigger download
        return $this->download($guidanceDocument);
    }

    private function authorizeManager(): void
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola dokumen panduan.');
        }
    }
}

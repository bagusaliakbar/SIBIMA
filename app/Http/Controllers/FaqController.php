<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller
{
    /**
     * Display a listing of FAQs for all users / public / students / lecturers.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user ? $user->role : null;

        $selectedCategory = $request->query('category', 'all');

        $query = Faq::active()->forRole($role)->ordered();

        if ($selectedCategory && $selectedCategory !== 'all') {
            $query->where('category', $selectedCategory);
        }

        $faqs = $query->get();
        $categories = Faq::categories();

        // Calculate counts per category
        $categoryCounts = [];
        $allFaqs = Faq::active()->forRole($role)->get();
        $categoryCounts['all'] = $allFaqs->count();
        foreach (array_keys($categories) as $catKey) {
            $categoryCounts[$catKey] = $allFaqs->where('category', $catKey)->count();
        }

        return view('faqs.index', compact('faqs', 'categories', 'selectedCategory', 'categoryCounts'));
    }

    /**
     * Admin & Kaprodi management view for FAQs.
     */
    public function manage(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403, 'Akses terbatas untuk Admin dan Kaprodi.');
        }

        $query = Faq::query()->ordered();

        // Filter by category
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filter by target role
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('target_role', $request->role);
        }

        // Search query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        $faqs = $query->paginate(15)->withQueryString();
        $categories = Faq::categories();

        $stats = [
            'total' => Faq::count(),
            'active' => Faq::where('is_active', true)->count(),
            'inactive' => Faq::where('is_active', false)->count(),
        ];

        return view('faqs.manage', compact('faqs', 'categories', 'stats'));
    }

    /**
     * Store a newly created FAQ in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category' => 'required|string|in:' . implode(',', array_keys(Faq::categories())),
            'target_role' => 'required|string|in:all,mahasiswa,dosen',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['order'] = $validated['order'] ?? 0;
        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['created_by'] = $user->id;

        Faq::create($validated);

        return redirect()->back()->with('success', 'FAQ baru berhasil ditambahkan.');
    }

    /**
     * Update the specified FAQ in storage.
     */
    public function update(Request $request, Faq $faq)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category' => 'required|string|in:' . implode(',', array_keys(Faq::categories())),
            'target_role' => 'required|string|in:all,mahasiswa,dosen',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['order'] = $validated['order'] ?? 0;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $faq->update($validated);

        return redirect()->back()->with('success', 'FAQ berhasil diperbarui.');
    }

    /**
     * Toggle active state of FAQ.
     */
    public function toggle(Faq $faq)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $faq->update(['is_active' => !$faq->is_active]);

        $statusText = $faq->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "FAQ berhasil {$statusText}.");
    }

    /**
     * Remove the specified FAQ from storage.
     */
    public function destroy(Faq $faq)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'kaprodi'])) {
            abort(403);
        }

        $faq->delete();

        return redirect()->back()->with('success', 'FAQ berhasil dihapus.');
    }
}

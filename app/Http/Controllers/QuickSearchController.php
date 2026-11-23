<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\GuidanceDocument;
use App\Models\Thesis;
use App\Models\ThesisRepository;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuickSearchController extends Controller
{
    /**
     * Role-aware live global quick search endpoint for the Ctrl+K Command Palette.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));
        if (mb_strlen($query) < 2 || mb_strlen($query) > 100) {
            return response()->json(['results' => []]);
        }

        $user = Auth::user();
        $role = $user->role;
        $like = '%' . $query . '%';
        $results = [];

        // 1. Theses & Students (Admin/Kaprodi: all; Dosen: strictly their advised students)
        if (in_array($role, ['admin', 'kaprodi', 'dosen'], true)) {
            $thesisQuery = Thesis::query()
                ->with(['student:id,name,identifier,entry_year'])
                ->where(function ($q) use ($like) {
                    $q->where('title', 'like', $like)
                        ->orWhere('final_title', 'like', $like)
                        ->orWhere('topic', 'like', $like)
                        ->orWhereHas('student', function ($sq) use ($like) {
                            $sq->where('name', 'like', $like)
                                ->orWhere('identifier', 'like', $like)
                                ->orWhere('username', 'like', $like);
                        });
                });

            if ($role === 'dosen') {
                $thesisQuery->where(function ($q) use ($user) {
                    $q->where('pembimbing1_id', $user->id)
                        ->orWhere('pembimbing2_id', $user->id);
                });
            }

            $theses = $thesisQuery->latest('updated_at')->take(6)->get();
            foreach ($theses as $thesis) {
                $studentName = $thesis->student?->name ?? 'Mahasiswa';
                $npm = $thesis->student?->identifier ?: '-';
                $roleHint = $role === 'dosen'
                    ? ($thesis->pembimbing1_id === $user->id ? 'P1' : 'P2')
                    : ucfirst((string) $thesis->status);

                $results[] = [
                    'category' => $role === 'dosen' ? 'Mahasiswa Bimbingan' : 'Skripsi & Mahasiswa',
                    'title' => $studentName . ' (' . $npm . ')',
                    'subtitle' => Str::limit($thesis->display_title, 78),
                    'badge' => $roleHint,
                    'url' => route('theses.logbooks', $thesis->id),
                    'icon' => 'thesis',
                ];
            }
        }

        // 2. Users Directory (Admin & Kaprodi only)
        if (in_array($role, ['admin', 'kaprodi'], true)) {
            $users = User::query()
                ->select('id', 'name', 'role', 'identifier', 'username', 'is_active')
                ->where(function ($q) use ($like) {
                    $q->where('name', 'like', $like)
                        ->orWhere('identifier', 'like', $like)
                        ->orWhere('username', 'like', $like)
                        ->orWhere('email', 'like', $like);
                })
                ->orderByRaw("CASE WHEN role = 'dosen' THEN 1 WHEN role = 'mahasiswa' THEN 2 ELSE 3 END")
                ->take(5)
                ->get();

            foreach ($users as $u) {
                $results[] = [
                    'category' => 'Pengguna',
                    'title' => $u->name,
                    'subtitle' => trim(($u->identifier ?: $u->username ?: '-') . ' · ' . ucfirst($u->role)),
                    'badge' => $u->is_active ? ucfirst($u->role) : 'Nonaktif',
                    'url' => route('users.index', ['search' => $u->identifier ?: $u->name]),
                    'icon' => 'user',
                ];
            }
        }

        // 3. Guidance Documents (all authenticated users)
        $documents = GuidanceDocument::query()
            ->where('is_active', true)
            ->where(function ($q) use ($like) {
                $q->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('category', 'like', $like);
            })
            ->latest()
            ->take(3)
            ->get(['id', 'title', 'category']);

        foreach ($documents as $doc) {
            $results[] = [
                'category' => 'Panduan & Dokumen',
                'title' => $doc->title,
                'subtitle' => $doc->category ? 'Kategori: ' . $doc->category : 'Dokumen panduan akademik',
                'badge' => 'Dokumen',
                'url' => route('guidance-documents.index', ['search' => $doc->title]),
                'icon' => 'document',
            ];
        }

        // 4. Repository Catalog (all authenticated users)
        $repos = ThesisRepository::query()
            ->where(function ($q) use ($like) {
                $q->where('title', 'like', $like)
                    ->orWhere('name', 'like', $like)
                    ->orWhere('identifier', 'like', $like);
            })
            ->latest('year')
            ->take(4)
            ->get(['id', 'title', 'name', 'year']);

        foreach ($repos as $repo) {
            $results[] = [
                'category' => 'Katalog Pustaka',
                'title' => Str::limit($repo->title, 78),
                'subtitle' => trim(($repo->name ?: '-') . ($repo->year ? ' · ' . $repo->year : '')),
                'badge' => $repo->year ? (string) $repo->year : 'Arsip',
                'url' => route('repositories.index', ['search' => $query]),
                'icon' => 'book',
            ];
        }

        // 5. Help Center FAQs (all authenticated users)
        $faqs = Faq::query()
            ->where('is_active', true)
            ->where(function ($q) use ($like) {
                $q->where('question', 'like', $like)
                    ->orWhere('answer', 'like', $like)
                    ->orWhere('category', 'like', $like);
            })
            ->orderBy('order')
            ->take(4)
            ->get(['id', 'question', 'category']);

        foreach ($faqs as $faq) {
            $results[] = [
                'category' => 'Pusat Bantuan (FAQ)',
                'title' => Str::limit($faq->question, 84),
                'subtitle' => $faq->category ?: 'Tanya Jawab SIBIMA',
                'badge' => 'FAQ',
                'url' => route('faqs.index', ['q' => $query]),
                'icon' => 'help',
            ];
        }

        return response()->json([
            'results' => $results,
        ]);
    }
}

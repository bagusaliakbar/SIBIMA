<?php

namespace App\Http\Controllers;

use App\Models\WaTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class WaTemplateController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (!in_array(Auth::user()->role, ['admin', 'kaprodi'])) {
                    abort(403);
                }
                return $next($request);
            }),
        ];
    }

    public function index(Request $request)
    {
        $this->ensureDefaultTemplatesExist();

        $selectedCategory = $request->input('category', 'all');

        $query = WaTemplate::query();

        if ($selectedCategory !== 'all') {
            $query->where('category', $selectedCategory);
        }

        $templates = $query->orderBy('category')->orderBy('id')->get();
        $categories = ['Bimbingan', 'Skripsi', 'Ujian', 'Pengingat'];

        return view('wa_templates.index', compact('templates', 'categories', 'selectedCategory'));
    }

    /**
     * Auto-ensure all required standard templates exist in the database (useful for hosting/production).
     */
    private function ensureDefaultTemplatesExist(): void
    {
        $requiredCodes = [
            'mentoring_requested',
            'mentoring_scheduled_by_dosen',
            'mentoring_status_updated',
            'mentoring_reminder',
            'supervisor_assigned',
            'thesis_accepted',
            'revision_requested',
            'revision_submitted',
            'thesis_completed',
            'schedule_published',
            'schedule_reminder',
            'critical_student_reminder',
            'kaprodi_critical_summary',
        ];

        $existingCount = WaTemplate::whereIn('code', $requiredCodes)->count();
        if ($existingCount < count($requiredCodes)) {
            try {
                $seeder = new \Database\Seeders\WaTemplateSeeder();
                $seeder->run();
            } catch (\Throwable $e) {
                // Ignore if DB connection or permissions issue occurs
            }
        }
    }

    public function edit(WaTemplate $waTemplate)
    {
        return view('wa_templates.edit', compact('waTemplate'));
    }

    public function update(Request $request, WaTemplate $waTemplate)
    {
        $request->validate([
            'content' => 'required|string|min:5',
        ]);

        $waTemplate->update([
            'content' => $request->input('content'),
            'is_customized' => true,
        ]);

        WaTemplate::clearCache($waTemplate->code);

        return redirect()->route('wa-templates.index', ['category' => $waTemplate->category])
            ->with('success', "Template WhatsApp '{$waTemplate->name}' berhasil diperbarui.");
    }

    public function reset(WaTemplate $waTemplate)
    {
        // Re-run seeder data for this specific template code
        $seeder = new \Database\Seeders\WaTemplateSeeder();
        // Run seed to restore default
        $seeder->run();

        // Refetch and mark not customized
        $waTemplate->refresh();
        $waTemplate->update(['is_customized' => false]);
        WaTemplate::clearCache($waTemplate->code);

        return redirect()->back()->with('success', "Template WhatsApp '{$waTemplate->name}' berhasil dikembalikan ke format default pabrikan.");
    }
}

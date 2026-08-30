<?php

namespace App\Http\Controllers;

use App\Models\Setting;
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
        $isWhatsAppGloballyEnabled = Setting::isWhatsAppEnabled();

        return view('wa_templates.index', compact('templates', 'categories', 'selectedCategory', 'isWhatsAppGloballyEnabled'));
    }

    /**
     * Toggle the global WhatsApp master switch and synchronize all templates.
     */
    public function toggleGlobal(Request $request)
    {
        $current = Setting::isWhatsAppEnabled();
        $newState = !$current;
        Setting::setWhatsAppEnabled($newState);

        // Synchronize all templates in DB with the new master switch state
        WaTemplate::query()->update(['is_active' => $newState]);
        WaTemplate::clearCache();

        $message = $newState 
            ? 'Seluruh notifikasi WhatsApp berhasil DIAKTIFKAN. Saklar utama dan seluruh template kartu aktif.' 
            : 'Seluruh notifikasi WhatsApp berhasil DINONAKTIFKAN. Saklar utama dan seluruh template kartu dimatikan.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Toggle the status (active/inactive) for a specific template.
     */
    public function toggleStatus(Request $request, WaTemplate $waTemplate)
    {
        $newStatus = !$waTemplate->is_active;
        $waTemplate->update([
            'is_active' => $newStatus,
        ]);

        WaTemplate::clearCache($waTemplate->code);

        // If at least 1 template is active, keep master switch enabled; if all are inactive, disable master switch.
        $anyActive = WaTemplate::where('is_active', true)->exists();
        Setting::setWhatsAppEnabled($anyActive);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        $message = "Template notifikasi '{$waTemplate->name}' berhasil {$statusText}.";

        return redirect()->back()->with('success', $message);
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
            'mentoring_rescheduled',
            'mentoring_cancelled',
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

        $updateData = [
            'content' => $request->input('content'),
            'is_customized' => true,
        ];

        if ($request->has('is_active')) {
            $updateData['is_active'] = (bool) $request->input('is_active');
        }

        $waTemplate->update($updateData);

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

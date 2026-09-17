<?php

namespace App\Http\Controllers;

use App\Models\BugReport;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BugReportController extends Controller
{
    /**
     * Submit a new bug report (Mahasiswa, Dosen, etc.)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:ui_ux,functionality,performance,security,data_error,other',
            'severity' => 'required|string|in:low,medium,high,critical',
            'page_url' => 'nullable|string|max:500',
            'description' => 'required|string|max:5000',
            'steps_to_reproduce' => 'nullable|string|max:5000',
            'device_info' => 'nullable|string|max:500',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,pdf|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('bug_reports', 'public');
        }

        // Generate unique ticket number: BUG-YYYYMMDD-XXXX
        $ticketNumber = 'BUG-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        while (BugReport::where('ticket_number', $ticketNumber)->exists()) {
            $ticketNumber = 'BUG-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        }

        $bugReport = BugReport::create([
            'ticket_number' => $ticketNumber,
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'category' => $validated['category'],
            'severity' => $validated['severity'],
            'status' => 'open',
            'page_url' => $validated['page_url'] ?? url()->previous(),
            'description' => $validated['description'],
            'steps_to_reproduce' => $validated['steps_to_reproduce'] ?? null,
            'device_info' => $validated['device_info'] ?? null,
            'attachment_path' => $attachmentPath,
        ]);

        // Send notifications to all active Admins & Kaprodi
        $recipients = User::whereIn('role', ['admin', 'kaprodi'])
            ->where('is_active', true)
            ->where('id', '!=', Auth::id())
            ->get();

        $severityUpper = strtoupper($bugReport->severity);
        $roleLabel = ucfirst(Auth::user()->role);

        foreach ($recipients as $recipient) {
            $recipient->notify(new GeneralNotification(
                title: 'Laporan Bug Baru #' . $bugReport->ticket_number,
                message: Auth::user()->name . " ({$roleLabel}) melaporkan kendala [{$severityUpper}]: " . Str::limit($bugReport->title, 45),
                url: route('admin.bug-reports.index', ['highlight' => $bugReport->id]),
                type: $bugReport->severity === 'critical' ? 'danger' : 'warning'
            ));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan bug berhasil dikirim! Tim Admin/Kaprodi akan segera meninjaunya.',
                'ticket_number' => $bugReport->ticket_number,
                'report' => $bugReport,
            ]);
        }

        return back()->with('success', 'Laporan bug berhasil dikirim! Kode tiket: ' . $bugReport->ticket_number);
    }

    /**
     * Get reports submitted by current logged-in user (JSON)
     */
    public function myReports(Request $request)
    {
        $reports = BugReport::where('user_id', Auth::id())
            ->with(['resolver:id,name,role'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'ticket_number' => $report->ticket_number,
                    'title' => $report->title,
                    'category' => $report->category,
                    'category_label' => $report->category_label,
                    'severity' => $report->severity,
                    'severity_label' => $report->severity_label,
                    'status' => $report->status,
                    'status_label' => $report->status_label,
                    'page_url' => $report->page_url,
                    'description' => $report->description,
                    'steps_to_reproduce' => $report->steps_to_reproduce,
                    'attachment_url' => $report->attachment_url,
                    'admin_notes' => $report->admin_notes,
                    'resolver_name' => $report->resolver ? $report->resolver->name : null,
                    'resolved_at' => $report->resolved_at ? $report->resolved_at->translatedFormat('d M Y H:i') : null,
                    'created_at_formatted' => $report->created_at->translatedFormat('d M Y H:i'),
                    'created_at_human' => $report->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'reports' => $reports,
        ]);
    }

    /**
     * Admin & Kaprodi Management Panel
     */
    public function index(Request $request)
    {
        $query = BugReport::with(['user', 'resolver']);

        // Filter status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter severity
        if ($request->filled('severity') && $request->severity !== 'all') {
            $query->where('severity', $request->severity);
        }

        // Filter user role (mahasiswa / dosen)
        if ($request->filled('role') && $request->role !== 'all') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        // Keyword Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('identifier', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $bugReports = $query->orderByRaw("CASE WHEN status = 'open' THEN 1 WHEN status = 'in_progress' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Metrics Summary
        $stats = [
            'total' => BugReport::count(),
            'open' => BugReport::where('status', 'open')->count(),
            'in_progress' => BugReport::where('status', 'in_progress')->count(),
            'resolved' => BugReport::where('status', 'resolved')->count(),
            'critical' => BugReport::where('severity', 'critical')->whereIn('status', ['open', 'in_progress'])->count(),
        ];

        return view('bug_reports.index', compact('bugReports', 'stats'));
    }

    /**
     * Show single bug report details (JSON or modal)
     */
    public function show(BugReport $bugReport)
    {
        // Check authorization: must be admin, kaprodi, or the owner
        if (!in_array(Auth::user()->role, ['admin', 'kaprodi']) && $bugReport->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak melihat laporan ini.');
        }

        $bugReport->load(['user', 'resolver']);

        return response()->json([
            'success' => true,
            'report' => [
                'id' => $bugReport->id,
                'ticket_number' => $bugReport->ticket_number,
                'title' => $bugReport->title,
                'category' => $bugReport->category,
                'category_label' => $bugReport->category_label,
                'severity' => $bugReport->severity,
                'severity_label' => $bugReport->severity_label,
                'status' => $bugReport->status,
                'status_label' => $bugReport->status_label,
                'page_url' => $bugReport->page_url,
                'description' => $bugReport->description,
                'steps_to_reproduce' => $bugReport->steps_to_reproduce,
                'device_info' => $bugReport->device_info,
                'attachment_url' => $bugReport->attachment_url,
                'admin_notes' => $bugReport->admin_notes,
                'user' => [
                    'name' => $bugReport->user->name,
                    'email' => $bugReport->user->email,
                    'role' => ucfirst($bugReport->user->role),
                    'identifier' => $bugReport->user->identifier,
                    'avatar_url' => $bugReport->user->avatar_url,
                ],
                'resolver' => $bugReport->resolver ? [
                    'name' => $bugReport->resolver->name,
                    'role' => ucfirst($bugReport->resolver->role),
                ] : null,
                'resolved_at' => $bugReport->resolved_at ? $bugReport->resolved_at->translatedFormat('d M Y H:i') : null,
                'created_at' => $bugReport->created_at->translatedFormat('d M Y H:i'),
                'created_at_human' => $bugReport->created_at->diffForHumans(),
            ],
        ]);
    }

    /**
     * Update bug report status & admin notes (Kaprodi / Admin)
     */
    public function updateStatus(Request $request, BugReport $bugReport)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:open,in_progress,resolved,rejected',
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        $prevStatus = $bugReport->status;
        $bugReport->status = $validated['status'];
        $bugReport->admin_notes = $validated['admin_notes'] ?? $bugReport->admin_notes;
        $bugReport->resolved_by = Auth::id();

        if (in_array($validated['status'], ['resolved', 'rejected'])) {
            $bugReport->resolved_at = now();
        } elseif ($validated['status'] === 'open') {
            $bugReport->resolved_at = null;
        }

        $bugReport->save();

        // Notify the reporter if status changed and the user exists
        if ($bugReport->user && $bugReport->user_id !== Auth::id()) {
            $statusLabel = $bugReport->status_label;
            $notesSnippet = $bugReport->admin_notes ? ' | Catatan: ' . Str::limit($bugReport->admin_notes, 60) : '';

            $bugReport->user->notify(new GeneralNotification(
                title: 'Update Tiket Bug #' . $bugReport->ticket_number,
                message: "Laporan bug '{$bugReport->title}' kini berstatus: {$statusLabel}{$notesSnippet}",
                url: route('dashboard'),
                type: $bugReport->status === 'resolved' ? 'success' : 'info'
            ));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status laporan bug berhasil diperbarui.',
                'report' => $bugReport->load(['user', 'resolver']),
            ]);
        }

        return back()->with('success', 'Status laporan #' . $bugReport->ticket_number . ' berhasil diubah menjadi ' . $bugReport->status_label);
    }

    /**
     * Delete a bug report (Admin only)
     */
    public function destroy(BugReport $bugReport)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Hanya Administrator yang berhak menghapus laporan bug.');
        }

        if ($bugReport->attachment_path && Storage::disk('public')->exists($bugReport->attachment_path)) {
            Storage::disk('public')->delete($bugReport->attachment_path);
        }

        $ticket = $bugReport->ticket_number;
        $bugReport->delete();

        return back()->with('success', 'Laporan bug #' . $ticket . ' berhasil dihapus.');
    }
}

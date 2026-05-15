<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AnnouncementController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (Auth::user()->role !== 'admin') abort(403);
                return $next($request);
            }),
        ];
    }

    public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->paginate(10);
        return view('announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,warning,important',
        ]);

        Announcement::create(array_merge($data, [
            'is_active' => $request->has('is_active'),
            'user_id' => Auth::id(),
        ]));

        ActivityLog::log('Buat Pengumuman', "Admin membuat pengumuman baru: {$request->title}", 'Pengumuman');

        return redirect()->back()->with('success', 'Pengumuman berhasil diterbitkan.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,warning,important',
        ]);

        $announcement->update(array_merge($data, [
            'is_active' => $request->has('is_active'),
        ]));

        ActivityLog::log('Edit Pengumuman', "Admin memperbarui pengumuman: {$announcement->title}", 'Pengumuman');

        return redirect()->back()->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        ActivityLog::log('Hapus Pengumuman', "Admin menghapus pengumuman: {$announcement->title}", 'Pengumuman');
        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus.');
    }

    public function toggleStatus(Announcement $announcement)
    {
        $announcement->is_active = !$announcement->is_active;
        $announcement->save();

        $status = $announcement->is_active ? 'mengaktifkan' : 'menonaktifkan';
        ActivityLog::log('Toggle Pengumuman', "Admin {$status} pengumuman: {$announcement->title}", 'Pengumuman');

        return redirect()->back()->with('success', 'Status pengumuman berhasil diubah.');
    }
}

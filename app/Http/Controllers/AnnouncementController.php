<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class AnnouncementController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $announcements = Announcement::orderBy('created_at', 'desc')->paginate(10);
        return view('announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,warning,important',
        ]);

        Announcement::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'type' => $request->input('type'),
            'is_active' => $request->has('is_active'),
            'user_id' => Auth::id(),
        ]);

        ActivityLog::log('Buat Pengumuman', "Admin membuat pengumuman baru: {$request->title}", 'Pengumuman');

        return redirect()->back()->with('success', 'Pengumuman berhasil diterbitkan.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,warning,important',
        ]);

        $announcement->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'type' => $request->input('type'),
            'is_active' => $request->has('is_active'),
        ]);

        ActivityLog::log('Edit Pengumuman', "Admin memperbarui pengumuman: {$announcement->title}", 'Pengumuman');

        return redirect()->back()->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $announcement->delete();

        ActivityLog::log('Hapus Pengumuman', "Admin menghapus pengumuman: {$announcement->title}", 'Pengumuman');

        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus.');
    }

    public function toggleStatus(Announcement $announcement)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $announcement->is_active = !$announcement->is_active;
        $announcement->save();

        $status = $announcement->is_active ? 'mengaktifkan' : 'menonaktifkan';
        ActivityLog::log('Toggle Pengumuman', "Admin {$status} pengumuman: {$announcement->title}", 'Pengumuman');

        return redirect()->back()->with('success', 'Status pengumuman berhasil diubah.');
    }
}

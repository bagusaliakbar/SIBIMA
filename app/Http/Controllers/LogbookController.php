<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use App\Models\MentoringSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogbookController extends Controller
{
    /**
     * Display logbook index for Mahasiswa and Dosen.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        if (Auth::user()->role === 'mahasiswa') {
            $sessions = MentoringSession::whereHas('thesis', function ($q) {
                    $q->where('student_id', Auth::id());
                })
                ->where('status', 'completed')
                ->where('is_absent', false)
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('topic', 'like', "%{$search}%")
                          ->orWhere('notes', 'like', "%{$search}%");
                    });
                })
                ->with('thesis.pembimbing1', 'thesis.pembimbing2', 'dosen')
                ->orderBy('scheduled_at', 'desc')
                ->paginate(10)
                ->appends(['search' => $search]);

            return view('logbooks.index', compact('sessions', 'search'));

        } elseif (Auth::user()->role === 'dosen') {
            $theses = Thesis::where(function($q) {
                    $q->where('pembimbing1_id', Auth::id())
                      ->orWhere('pembimbing2_id', Auth::id());
                })
                ->with('student')
                ->when($search, function ($query, $search) {
                    $query->whereHas('student', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('identifier', 'like', "%{$search}%");
                    })
                    ->orWhere('title', 'like', "%{$search}%");
                })
                ->withCount(['mentoringSessions as completed_sessions_count' => function ($q) {
                    $q->where('dosen_id', Auth::id())->where('status', 'completed')->where('is_absent', false);
                }])
                ->paginate(12)
                ->appends(['search' => $search]);

            return view('logbooks.dosen_index', compact('theses', 'search'));
        }

        abort(403);
    }

    /**
     * Display mentoring activities (monitoring) for a specific thesis.
     * Accessible by Admin and Dosen Pembimbing.
     */
    public function show(Thesis $thesis)
    {
        if (Auth::user()->role === 'admin' || 
           (Auth::user()->role === 'dosen' && ($thesis->pembimbing1_id === Auth::id() || $thesis->pembimbing2_id === Auth::id()))) {
            
            $thesis->load(['student', 'pembimbing1', 'pembimbing2']);

            $activeSessions = MentoringSession::where('thesis_id', $thesis->id)
                ->when(Auth::user()->role === 'dosen', function($q) {
                    $q->where('dosen_id', Auth::id());
                })
                ->whereIn('status', ['pending', 'approved'])
                ->orderBy('scheduled_at', 'asc')
                ->get();

            $completedSessions = MentoringSession::where('thesis_id', $thesis->id)
                ->when(Auth::user()->role === 'dosen', function($q) {
                    $q->where('dosen_id', Auth::id());
                })
                ->where('status', 'completed')
                ->where('is_absent', false)
                ->orderBy('scheduled_at', 'desc')
                ->get();

            return view('logbooks.show', compact('thesis', 'activeSessions', 'completedSessions'));
        }
        
        abort(403);
    }

    /**
     * Export logbook to PDF.
     */
    public function exportPdf(Request $request, Thesis $thesis = null)
    {
        // If $thesis is null, it means a student is accessing their own logbook
        if (!$thesis) {
            $thesis = Thesis::where('student_id', Auth::id())->first();
        }

        if (!$thesis) {
            return back()->with('error', 'Data skripsi tidak ditemukan.');
        }

        // Authorization check
        $isAuthorized = Auth::user()->role === 'admin' || 
                        (Auth::user()->role === 'mahasiswa' && $thesis->student_id === Auth::id()) ||
                        (Auth::user()->role === 'dosen' && ($thesis->pembimbing1_id === Auth::id() || $thesis->pembimbing2_id === Auth::id()));

        if (!$isAuthorized) {
            abort(403);
        }

        $thesis->load(['student', 'pembimbing1', 'pembimbing2']);

        $sessions = MentoringSession::where('thesis_id', $thesis->id)
            ->when(Auth::user()->role === 'dosen', function($q) {
                $q->where('dosen_id', Auth::id());
            })
            ->where('status', 'completed')
            ->where('is_absent', false)
            ->orderBy('scheduled_at', 'asc')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('logbooks.pdf', compact('thesis', 'sessions'));
        
        $filename = 'Logbook_' . str_replace(' ', '_', $thesis->student->name) . '_' . date('Ymd') . '.pdf';
        
        \App\Models\ActivityLog::log('Export Logbook', "User mengekspor logbook mahasiswa {$thesis->student->name} ke PDF.", 'Logbook');

        return $pdf->download($filename);
    }
}

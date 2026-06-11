<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Thesis;
use App\Models\ThesisDefenseRevision;
use App\Models\SeminarRevision;
use App\Models\MentoringSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentHistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role !== 'mahasiswa') {
            abort(403);
        }

        $thesis = Thesis::where('student_id', $user->id)->first();
        
        if (!$thesis) {
            return view('student.history', ['logs' => collect()]);
        }

        $thesisId = $thesis->id;
        $sessionIds = MentoringSession::where('thesis_id', $thesisId)->pluck('id');

        // Fetch all logs related to this student's thesis
        $logs = ActivityLog::with('user')
            ->where(function($query) use ($thesisId, $sessionIds, $user) {
                // Logs where the thesis is the subject
                $query->where(function($q) use ($thesisId) {
                    $q->where('subject_type', Thesis::class)
                      ->where('subject_id', $thesisId);
                })
                // Logs where revisions for this thesis are the subject
                ->orWhere(function($q) use ($thesisId) {
                    $q->where('subject_type', ThesisDefenseRevision::class)
                      ->whereIn('subject_id', ThesisDefenseRevision::whereHas('detail', function($d) use ($thesisId) {
                          $d->where('thesis_id', $thesisId);
                      })->pluck('id'));
                })
                ->orWhere(function($q) use ($thesisId) {
                    $q->where('subject_type', SeminarRevision::class)
                      ->whereIn('subject_id', SeminarRevision::whereHas('detail', function($d) use ($thesisId) {
                          $d->where('thesis_id', $thesisId);
                      })->pluck('id'));
                })
                // Logs where mentoring sessions for this thesis are the subject
                ->orWhere(function($q) use ($sessionIds) {
                    $q->where('subject_type', MentoringSession::class)
                      ->whereIn('subject_id', $sessionIds);
                })
                // Logs performed by the student themselves
                ->orWhere('user_id', $user->id);
            })
            ->whereNotIn('activity', [
                'Created', 'Updated', 'Deleted', 
                'Data Dibuat', 'Data Diperbarui', 'Data Dihapus',
                'Login', 'Logout', 'Kirim Pesan'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.history', compact('logs', 'thesis'));
    }
}

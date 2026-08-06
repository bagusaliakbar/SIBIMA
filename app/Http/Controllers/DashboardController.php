<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $user = Auth::user();
        $data = [];

        if ($user->role === 'mahasiswa') {
            $data = $this->dashboardService->getStudentData($user);
        } elseif ($user->role === 'dosen') {
            $data = $this->dashboardService->getDosenData($user);
        } elseif ($user->role === 'admin' || $user->role === 'kaprodi') {
            $data = $this->dashboardService->getAdminData();
        }

        $commonData = $this->dashboardService->getCommonData();
        
        return view('dashboard', array_merge($this->getDefaultData(), $data, $commonData));
    }

    private function getDefaultData()
    {
        return [
            'thesis' => null,
            'upcomingSessions' => collect(),
            'pastSessionsCount' => 0,
            'pastSessionsCountP1' => 0,
            'pastSessionsCountP2' => 0,
            'recentLogbooks' => collect(),
            'activeThesesCount' => 0,
            'sessionsThisWeek' => 0,
            'seminar' => null,
            'defense' => null,
            'thesisStatusCounts' => [],
            'monthlyMentoringCounts' => [],
            'studentProgressDistribution' => [],
            'dosenWorkload' => [],
            'totalActiveStudentsP1' => 0,
            'totalActiveStudentsP2' => 0,
            'pendingSessionsThisWeek' => 0,
            'approvedSessionsThisWeek' => 0,
            'totalCompletedSessions' => 0,
            'averageStudentProgress' => 0,
            'isStale' => false,
            'daysSinceLastSession' => null,
            'mySeminarSchedule' => null,
            'myDefenseSchedule' => null,
            'examinerSeminarSchedules' => collect(),
            'examinerDefenseSchedules' => collect(),
            'onTimeStats' => null,
            'studentHealthStats' => null,
            'cohortCompletionData' => [],
            'topicTrends' => [],
            'onlineUsers' => collect(),
            'onlineUsersCount' => 0,
            'onlineUsersByRole' => ['mahasiswa' => 0, 'dosen' => 0, 'kaprodi' => 0, 'admin' => 0],
            'progress' => [
                'percent' => 0, 
                'isGraduated' => false, 
                'seminarDone' => false, 
                'defenseDone' => false,
                'currentStage' => 0,
                'stages' => [
                    ['name' => 'Judul', 'desc' => 'Pengajuan judul skripsi'],
                    ['name' => 'Bimbingan', 'desc' => 'Proses bimbingan Bab 1-3'],
                    ['name' => 'Seminar', 'desc' => 'Seminar proposal/hasil'],
                    ['name' => 'Penelitian', 'desc' => 'Pengolahan data & bimbingan Bab 4-5'],
                    ['name' => 'Sidang', 'desc' => 'Ujian sidang akhir skripsi'],
                    ['name' => 'Yudisium', 'desc' => 'Pernyataan kelulusan final']
                ]
            ],
        ];
    }

    public function onlineUsers()
    {
        $allUsers = \App\Models\User::all();
        $onlineUsers = $allUsers->filter(fn($u) => $u->is_online)->values()->map(function($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'role' => $u->role,
                'avatar_url' => $u->avatar_url,
                'identifier' => $u->identifier,
                'chat_url' => route('chat.show', $u->id),
            ];
        });

        return response()->json([
            'count' => $onlineUsers->count(),
            'users' => $onlineUsers,
            'by_role' => [
                'mahasiswa' => $onlineUsers->where('role', 'mahasiswa')->count(),
                'dosen' => $onlineUsers->where('role', 'dosen')->count(),
                'kaprodi' => $onlineUsers->where('role', 'kaprodi')->count(),
                'admin' => $onlineUsers->where('role', 'admin')->count(),
            ]
        ]);
    }
}

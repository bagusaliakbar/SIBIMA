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
            'isCriticalStale' => false,
            'daysSinceLastSession' => null,
            'earlyWarning' => [
                'total_inactive' => 0,
                'warning_count' => 0,
                'critical_count' => 0,
                'students' => [],
            ],
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
                'currentStage' => 1,
                'stages' => [
                    ['number' => 1, 'name' => '1. Pengajuan Judul', 'short_name' => 'Pengajuan Judul', 'desc' => 'Pengajuan judul & penetapan pembimbing', 'url' => '#', 'status_type' => 'active', 'status_label' => 'Sedang Berjalan', 'badge' => null, 'is_completed' => false, 'is_current' => true, 'is_locked' => false],
                    ['number' => 2, 'name' => '2. Pendaftaran Seminar', 'short_name' => 'Pendaftaran Seminar', 'desc' => 'Bimbingan berkala & pendaftaran jadwal Sempro', 'url' => '#', 'status_type' => 'locked', 'status_label' => 'Terkunci', 'badge' => null, 'is_completed' => false, 'is_current' => false, 'is_locked' => true],
                    ['number' => 3, 'name' => '3. Revisi Seminar', 'short_name' => 'Revisi Seminar', 'desc' => 'Pelaksanaan ujian Sempro & approval revisi penguji', 'url' => '#', 'status_type' => 'locked', 'status_label' => 'Terkunci', 'badge' => null, 'is_completed' => false, 'is_current' => false, 'is_locked' => true],
                    ['number' => 4, 'name' => '4. Pendaftaran Sidang', 'short_name' => 'Pendaftaran Sidang', 'desc' => 'Bimbingan Bab 4-5 & pendaftaran sidang skripsi', 'url' => '#', 'status_type' => 'locked', 'status_label' => 'Terkunci', 'badge' => null, 'is_completed' => false, 'is_current' => false, 'is_locked' => true],
                    ['number' => 5, 'name' => '5. Revisi Sidang', 'short_name' => 'Revisi Sidang', 'desc' => 'Ujian sidang skripsi & approval revisi dosen penguji', 'url' => '#', 'status_type' => 'locked', 'status_label' => 'Terkunci', 'badge' => null, 'is_completed' => false, 'is_current' => false, 'is_locked' => true],
                    ['number' => 6, 'name' => '6. Yudisium & SKL', 'short_name' => 'Yudisium & SKL', 'desc' => 'Verifikasi bebas tanggungan & Surat Keterangan Lulus', 'url' => '#', 'status_type' => 'locked', 'status_label' => 'Terkunci', 'badge' => null, 'is_completed' => false, 'is_current' => false, 'is_locked' => true],
                ]
            ],
        ];
    }

    public function onlineUsers()
    {
        $data = \Illuminate\Support\Facades\Cache::remember('dashboard_online_users_json', 15, function () {
            $allUsers = \App\Models\User::select('id', 'name', 'role', 'identifier')->get();
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

            return [
                'count' => $onlineUsers->count(),
                'users' => $onlineUsers,
                'by_role' => [
                    'mahasiswa' => $onlineUsers->where('role', 'mahasiswa')->count(),
                    'dosen' => $onlineUsers->where('role', 'dosen')->count(),
                    'kaprodi' => $onlineUsers->where('role', 'kaprodi')->count(),
                    'admin' => $onlineUsers->where('role', 'admin')->count(),
                ]
            ];
        });

        return response()->json($data);
    }
}

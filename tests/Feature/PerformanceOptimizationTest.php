<?php

namespace Tests\Feature;

use App\Models\MentoringSession;
use App\Models\SeminarRevision;
use App\Models\SeminarSchedule;
use App\Models\SeminarScheduleDetail;
use App\Models\Thesis;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_online_user_index_tracks_presence_without_full_table_scan(): void
    {
        Cache::flush();

        $u1 = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $u2 = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
        $u3 = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);

        User::markOnline($u1->id, 180);
        User::markOnline($u2->id, 180);

        $this->assertTrue($u1->is_online);
        $this->assertTrue($u2->is_online);
        $this->assertFalse($u3->is_online);

        $online = User::getOnlineUsers(true);
        $this->assertCount(2, $online);
        $this->assertEqualsCanonicalizing([$u1->id, $u2->id], $online->pluck('id')->all());

        User::markOffline($u2->id);
        $this->assertFalse($u2->is_online);
        $this->assertEquals([$u1->id], User::getOnlineUsers()->pluck('id')->all());
    }

    public function test_dosen_and_admin_dashboards_batch_progress_queries_accurately(): void
    {
        Cache::flush();

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $dosen = User::factory()->create(['role' => 'dosen', 'is_active' => true]);

        for ($i = 0; $i < 6; $i++) {
            $student = User::factory()->create([
                'role' => 'mahasiswa',
                'is_active' => true,
                'entry_year' => 2022,
            ]);

            $thesis = Thesis::create([
                'student_id' => $student->id,
                'pembimbing1_id' => $dosen->id,
                'title' => "Skripsi Uji {$i}",
                'topic' => $i % 2 === 0 ? 'AI' : 'Web',
                'status' => 'active',
            ]);

            if ($i < 4) {
                MentoringSession::create([
                    'thesis_id' => $thesis->id,
                    'dosen_id' => $dosen->id,
                    'topic' => "Bimbingan Bab {$i}",
                    'scheduled_at' => now()->subDays(2),
                    'status' => 'completed',
                    'is_absent' => false,
                ]);
            }
        }

        $service = app(DashboardService::class);

        DB::flushQueryLog();
        DB::enableQueryLog();
        $dosenData = $service->getDosenData($dosen);
        $dosenQueries = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertSame(6, $dosenData['activeThesesCount']);
        $this->assertSame(4, $dosenData['studentProgressDistribution']['Bimbingan']);
        $this->assertSame(2, $dosenData['studentProgressDistribution']['Judul']);
        $this->assertGreaterThan(0, $dosenData['averageStudentProgress']);
        $this->assertCount(6, $dosenData['monthlyMentoringCounts']);
        $this->assertSame(4, array_sum($dosenData['monthlyMentoringCounts']));
        $this->assertLessThan(35, $dosenQueries, "Expected batched dosen dashboard queries (<35), got {$dosenQueries}");

        // Ensure HTTP dashboard endpoints render cleanly for admin & dosen
        $this->actingAs($dosen)->get(route('dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('dashboard'))->assertOk();
    }

    public function test_seminar_schedule_detail_reuses_eager_loaded_revisions_with_zero_extra_queries(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);
        $ex1 = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
        $ex2 = User::factory()->create(['role' => 'dosen', 'is_active' => true]);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'pembimbing1_id' => $ex1->id,
            'title' => 'Uji Eager Loading Revisi',
            'status' => 'active',
        ]);

        $schedule = SeminarSchedule::create([
            'title' => 'Jadwal Seminar Uji',
            'date' => now()->subDay()->toDateString(),
            'location' => 'Ruang 101',
            'created_by' => $ex1->id,
        ]);

        $detail = SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'examiner1_id' => $ex1->id,
            'examiner2_id' => $ex2->id,
            'order' => 1,
        ]);

        SeminarRevision::create([
            'seminar_schedule_detail_id' => $detail->id,
            'examiner_id' => $ex1->id,
            'status' => 'approved',
        ]);
        SeminarRevision::create([
            'seminar_schedule_detail_id' => $detail->id,
            'examiner_id' => $ex2->id,
            'status' => 'approved',
        ]);

        $loaded = SeminarScheduleDetail::with('revisions')->findOrFail($detail->id);

        DB::flushQueryLog();
        DB::enableQueryLog();
        $started = $loaded->isRevisionStarted();
        $allApproved = $loaded->isAllRevisionsApproved();
        $graded = $loaded->isGraded();
        $extraQueries = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertTrue($started);
        $this->assertTrue($allApproved);
        $this->assertFalse($graded);
        $this->assertSame(0, $extraQueries);
    }
}

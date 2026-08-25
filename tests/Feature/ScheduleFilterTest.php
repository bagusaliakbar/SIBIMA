<?php

namespace Tests\Feature;

use App\Models\SeminarSchedule;
use App\Models\SeminarScheduleDetail;
use App\Models\Thesis;
use App\Models\ThesisDefenseSchedule;
use App\Models\ThesisDefenseScheduleDetail;
use App\Models\User;
use App\Models\Wave;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleFilterTest extends TestCase
{
    use RefreshDatabase;

    protected $dosen1;
    protected $dosen2;
    protected $student1;
    protected $student2;
    protected $thesis1;
    protected $thesis2;
    protected $wave;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dosen1 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Utama']);
        $this->dosen2 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Lain']);
        $this->student1 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Santoso', 'identifier' => '12345678']);
        $this->student2 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Siti Nurhaliza', 'identifier' => '87654321']);

        $this->thesis1 = Thesis::create([
            'student_id' => $this->student1->id,
            'title' => 'Implementasi Machine Learning untuk Prediksi Cuaca',
            'pembimbing1_id' => $this->dosen1->id,
            'pembimbing2_id' => $this->dosen2->id,
            'status' => 'active',
        ]);

        $this->thesis2 = Thesis::create([
            'student_id' => $this->student2->id,
            'title' => 'Rancang Bangun Sistem IoT Pertanian',
            'pembimbing1_id' => $this->dosen2->id,
            'status' => 'active',
        ]);

        $this->wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subMonths(1),
            'end_date' => now()->addMonths(2),
        ]);

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_seminar_schedule_date_filtering()
    {
        // Schedule 1: Past
        $pastSchedule = SeminarSchedule::create([
            'title' => 'Seminar Proposal Sesi Lampau',
            'date' => now()->subDays(5)->toDateString(),
            'location' => 'Ruang 101',
            'wave_id' => $this->wave->id,
            'created_by' => $this->admin->id,
        ]);

        // Schedule 2: Today
        $todaySchedule = SeminarSchedule::create([
            'title' => 'Seminar Proposal Sesi Hari Ini',
            'date' => now()->toDateString(),
            'location' => 'Ruang 102',
            'wave_id' => $this->wave->id,
            'created_by' => $this->admin->id,
        ]);

        // Schedule 3: Upcoming
        $upcomingSchedule = SeminarSchedule::create([
            'title' => 'Seminar Proposal Sesi Mendatang',
            'date' => now()->addDays(5)->toDateString(),
            'location' => 'Ruang 103',
            'wave_id' => $this->wave->id,
            'created_by' => $this->admin->id,
        ]);

        // Test filter_date=today
        $response = $this->actingAs($this->dosen1)->get(route('seminar-schedules.index', ['filter_date' => 'today']));
        $response->assertStatus(200);
        $response->assertSee('Seminar Proposal Sesi Hari Ini');
        $response->assertDontSee('Seminar Proposal Sesi Lampau');
        $response->assertDontSee('Seminar Proposal Sesi Mendatang');

        // Test filter_date=upcoming
        $response = $this->actingAs($this->dosen1)->get(route('seminar-schedules.index', ['filter_date' => 'upcoming']));
        $response->assertStatus(200);
        $response->assertSee('Seminar Proposal Sesi Hari Ini');
        $response->assertSee('Seminar Proposal Sesi Mendatang');
        $response->assertDontSee('Seminar Proposal Sesi Lampau');

        // Test filter_date=past
        $response = $this->actingAs($this->dosen1)->get(route('seminar-schedules.index', ['filter_date' => 'past']));
        $response->assertStatus(200);
        $response->assertSee('Seminar Proposal Sesi Lampau');
        $response->assertDontSee('Seminar Proposal Sesi Mendatang');

        // Test specific date filter
        $response = $this->actingAs($this->dosen1)->get(route('seminar-schedules.index', ['date' => now()->addDays(5)->toDateString()]));
        $response->assertStatus(200);
        $response->assertSee('Seminar Proposal Sesi Mendatang');
        $response->assertDontSee('Seminar Proposal Sesi Hari Ini');

        // Test date range filter
        $response = $this->actingAs($this->dosen1)->get(route('seminar-schedules.index', [
            'date_from' => now()->subDays(6)->toDateString(),
            'date_to' => now()->subDays(4)->toDateString(),
        ]));
        $response->assertStatus(200);
        $response->assertSee('Seminar Proposal Sesi Lampau');
        $response->assertDontSee('Seminar Proposal Sesi Hari Ini');
    }

    public function test_seminar_schedule_my_schedules_filter()
    {
        // Schedule where Dosen1 is involved (as Examiner 1)
        $mySchedule = SeminarSchedule::create([
            'title' => 'Seminar Proposal Sesi Saya',
            'date' => now()->addDays(2)->toDateString(),
            'location' => 'Lab Komputer',
            'wave_id' => $this->wave->id,
            'created_by' => $this->admin->id,
        ]);
        SeminarScheduleDetail::create([
            'seminar_schedule_id' => $mySchedule->id,
            'thesis_id' => $this->thesis2->id,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'examiner1_id' => $this->dosen1->id,
            'order' => 1,
        ]);

        // Schedule where Dosen1 is NOT involved
        $otherSchedule = SeminarSchedule::create([
            'title' => 'Seminar Proposal Sesi Dosen Lain',
            'date' => now()->addDays(2)->toDateString(),
            'location' => 'Ruang 202',
            'wave_id' => $this->wave->id,
            'created_by' => $this->admin->id,
        ]);
        SeminarScheduleDetail::create([
            'seminar_schedule_id' => $otherSchedule->id,
            'thesis_id' => $this->thesis2->id,
            'start_time' => '10:00',
            'end_time' => '11:00',
            'examiner1_id' => $this->dosen2->id,
            'order' => 1,
        ]);

        $response = $this->actingAs($this->dosen1)->get(route('seminar-schedules.index', [
            'filter_date' => 'all',
            'my_schedules' => 1,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Seminar Proposal Sesi Saya');
        $response->assertDontSee('Seminar Proposal Sesi Dosen Lain');
    }

    public function test_seminar_schedule_search_filter()
    {
        $schedule1 = SeminarSchedule::create([
            'title' => 'Sesi Algoritma Genetika',
            'date' => now()->addDays(1)->toDateString(),
            'location' => 'Ruang Teater',
            'wave_id' => $this->wave->id,
            'created_by' => $this->admin->id,
        ]);
        SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule1->id,
            'thesis_id' => $this->thesis1->id,
            'start_time' => '08:00',
            'end_time' => '09:00',
            'order' => 1,
        ]);

        $schedule2 = SeminarSchedule::create([
            'title' => 'Sesi Cloud Computing',
            'date' => now()->addDays(1)->toDateString(),
            'location' => 'Ruang 301',
            'wave_id' => $this->wave->id,
            'created_by' => $this->admin->id,
        ]);
        SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule2->id,
            'thesis_id' => $this->thesis2->id,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'order' => 1,
        ]);

        // Search by student name
        $response = $this->actingAs($this->dosen1)->get(route('seminar-schedules.index', [
            'filter_date' => 'all',
            'search' => 'Budi Santoso',
        ]));
        $response->assertStatus(200);
        $response->assertSee('Sesi Algoritma Genetika');
        $response->assertDontSee('Sesi Cloud Computing');

        // Search by location
        $response = $this->actingAs($this->dosen1)->get(route('seminar-schedules.index', [
            'filter_date' => 'all',
            'search' => 'Ruang 301',
        ]));
        $response->assertStatus(200);
        $response->assertSee('Sesi Cloud Computing');
        $response->assertDontSee('Sesi Algoritma Genetika');
    }

    public function test_thesis_defense_schedule_filters()
    {
        // Past defense
        $pastDefense = ThesisDefenseSchedule::create([
            'title' => 'Sidang Skripsi Sesi Lampau',
            'date' => now()->subDays(3)->toDateString(),
            'location' => 'Ruang Sidang 1',
            'wave_id' => $this->wave->id,
            'created_by' => $this->admin->id,
        ]);

        // Upcoming defense with Dosen1 as Moderator
        $upcomingDefense = ThesisDefenseSchedule::create([
            'title' => 'Sidang Skripsi Sesi Mendatang',
            'date' => now()->addDays(4)->toDateString(),
            'location' => 'Ruang Sidang 2',
            'wave_id' => $this->wave->id,
            'moderator_id' => $this->dosen1->id,
            'created_by' => $this->admin->id,
        ]);

        // Test filter_date=upcoming
        $response = $this->actingAs($this->dosen1)->get(route('thesis-defense-schedules.index', ['filter_date' => 'upcoming']));
        $response->assertStatus(200);
        $response->assertSee('Sidang Skripsi Sesi Mendatang');
        $response->assertDontSee('Sidang Skripsi Sesi Lampau');

        // Test my_schedules=1
        $response = $this->actingAs($this->dosen1)->get(route('thesis-defense-schedules.index', [
            'filter_date' => 'all',
            'my_schedules' => 1,
        ]));
        $response->assertStatus(200);
        $response->assertSee('Sidang Skripsi Sesi Mendatang');
        $response->assertDontSee('Sidang Skripsi Sesi Lampau');
    }
}

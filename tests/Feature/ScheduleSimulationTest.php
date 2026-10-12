<?php

namespace Tests\Feature;

use App\Models\SeminarApplication;
use App\Models\SeminarSchedule;
use App\Models\SeminarScheduleDetail;
use App\Models\Thesis;
use App\Models\ThesisDefenseApplication;
use App\Models\User;
use App\Models\Wave;
use App\Services\AutoScheduleSimulationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleSimulationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $kaprodi;
    protected $dosenList;
    protected $wave;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin SIBIMA']);
        $this->kaprodi = User::factory()->create(['role' => 'kaprodi', 'name' => 'Kaprodi SIBIMA']);

        // Create 8 lecturers
        $this->dosenList = User::factory()->count(8)->create(['role' => 'dosen']);

        $this->wave = Wave::create([
            'name' => 'Gelombang 1 Genap 2025/2026',
            'is_active' => true,
            'start_date' => Carbon::yesterday(),
            'end_date' => Carbon::tomorrow()->addDays(30),
        ]);
    }

    public function test_admin_and_kaprodi_can_access_simulation_page()
    {
        $response = $this->actingAs($this->admin)->get(route('seminar-schedules.simulation'));
        $response->assertStatus(200);

        $responseDefense = $this->actingAs($this->kaprodi)->get(route('thesis-defense-schedules.simulation'));
        $responseDefense->assertStatus(200);
    }

    public function test_dosen_and_mahasiswa_cannot_access_simulation_page()
    {
        $dosen = $this->dosenList->first();
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $responseDosen = $this->actingAs($dosen)->get(route('seminar-schedules.simulation'));
        $responseDosen->assertStatus(403);

        $responseStudent = $this->actingAs($student)->get(route('seminar-schedules.simulation'));
        $responseStudent->assertStatus(403);
    }

    public function test_simulation_groups_same_pembimbing_1_in_the_same_room_and_avoids_conflicts()
    {
        // Setup 14 theses:
        // Dosen 0 has 4 students
        // Dosen 1 has 3 students
        // Dosen 2 has 4 students
        // Dosen 3 has 3 students
        $p1Assignments = [
            $this->dosenList[0]->id => 4,
            $this->dosenList[1]->id => 3,
            $this->dosenList[2]->id => 4,
            $this->dosenList[3]->id => 3,
        ];

        foreach ($p1Assignments as $p1Id => $count) {
            for ($i = 0; $i < $count; $i++) {
                $student = User::factory()->create(['role' => 'mahasiswa']);
                $p2Id = ($p1Id === $this->dosenList[0]->id || $p1Id === $this->dosenList[1]->id) 
                    ? $this->dosenList[4]->id 
                    : $this->dosenList[5]->id;

                $thesis = Thesis::create([
                    'student_id' => $student->id,
                    'title' => "Skripsi Mahasiswa {$student->name}",
                    'pembimbing1_id' => $p1Id,
                    'pembimbing2_id' => $p2Id,
                    'status' => 'seminar',
                    'acc_up_p1' => true,
                    'acc_up_p2' => true,
                ]);

                SeminarApplication::create([
                    'thesis_id' => $thesis->id,
                    'wave_id' => $this->wave->id,
                    'status' => 'approved',
                    'file_acc_pembimbing' => 'dummy.pdf',
                    'file_pembayaran' => 'dummy.pdf',
                    'file_kartu_bimbingan' => 'dummy.pdf',
                    'file_skripsi' => 'dummy.pdf',
                    'file_formulir' => 'dummy.pdf',
                ]);
            }
        }

        $service = app(AutoScheduleSimulationService::class);
        $options = [
            'date' => Carbon::tomorrow()->toDateString(),
            'room_count' => 2,
            'room_names' => ['Ruang 1 (R. Sidang)', 'Ruang 2 (Smart Class)'],
            'max_students_per_room' => 8,
            'start_time' => '08:30',
            'duration_minutes' => 50,
            'break_enabled' => true,
            'break_start' => '11:30',
            'break_end' => '13:00',
        ];

        $result = $service->generateSimulation('seminar', $this->wave->id, $options);

        $this->assertEquals('success', $result['status']);
        $this->assertEquals(14, $result['stats']['total_candidates']);
        $this->assertEquals(14, $result['stats']['scheduled_candidates']);
        $this->assertFalse($result['has_conflict'], 'Hasil simulasi harus 0 konflik!');
        $this->assertCount(0, $result['conflicts']);
        $this->assertEquals(100, $result['stats']['grouping_p1_success_rate']);

        // Check Room 1 and Room 2
        $room1Theses = collect($result['rooms'][0]['sessions'])->pluck('thesis');
        $room2Theses = collect($result['rooms'][1]['sessions'])->pluck('thesis');

        $this->assertEquals(7, $room1Theses->count());
        $this->assertEquals(7, $room2Theses->count());

        // Check grouping: All students with Pembimbing 1 = Dosen 0 must be in ONE room
        $p1Dosen0Room1 = $room1Theses->where('pembimbing1_id', $this->dosenList[0]->id)->count();
        $p1Dosen0Room2 = $room2Theses->where('pembimbing1_id', $this->dosenList[0]->id)->count();
        $this->assertTrue($p1Dosen0Room1 === 4 || $p1Dosen0Room2 === 4, 'Semua mahasiswa bimbingan Dosen 0 harus berada di 1 ruangan yang sama!');

        // Check examiners: No lecturer can be assigned to 2 rooms at the same time
        $allSessions = [];
        foreach ($result['rooms'] as $rIdx => $r) {
            foreach ($r['sessions'] as $s) {
                $allSessions[] = [
                    'room' => $rIdx,
                    'start' => $s['start_time'],
                    'end' => $s['end_time'],
                    'p1' => $s['thesis']['pembimbing1_id'],
                    'p2' => $s['thesis']['pembimbing2_id'],
                    'e1' => $s['examiner1_id'],
                    'e2' => $s['examiner2_id'],
                ];
            }
        }

        // Verify pairwise: no same lecturer at same time in different rooms
        for ($i = 0; $i < count($allSessions); $i++) {
            for ($j = $i + 1; $j < count($allSessions); $j++) {
                $s1 = $allSessions[$i];
                $s2 = $allSessions[$j];
                if ($s1['room'] !== $s2['room'] && $s1['start'] === $s2['start']) {
                    $lecturers1 = array_filter([$s1['p1'], $s1['p2'], $s1['e1'], $s1['e2']]);
                    $lecturers2 = array_filter([$s2['p1'], $s2['p2'], $s2['e1'], $s2['e2']]);
                    $overlap = array_intersect($lecturers1, $lecturers2);
                    $this->assertEmpty($overlap, 'Tidak boleh ada dosen yang bentrok di 2 ruangan pada jam yang sama!');
                }
            }
        }
    }

    public function test_apply_simulation_persists_schedule_records_to_database()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Skripsi Uji Coba Simulasi',
            'pembimbing1_id' => $this->dosenList[0]->id,
            'pembimbing2_id' => $this->dosenList[1]->id,
            'status' => 'seminar',
            'acc_up_p1' => true,
            'acc_up_p2' => true,
        ]);

        SeminarApplication::create([
            'thesis_id' => $thesis->id,
            'wave_id' => $this->wave->id,
            'status' => 'approved',
            'file_acc_pembimbing' => 'dummy.pdf',
            'file_pembayaran' => 'dummy.pdf',
            'file_kartu_bimbingan' => 'dummy.pdf',
            'file_skripsi' => 'dummy.pdf',
            'file_formulir' => 'dummy.pdf',
        ]);

        $payload = [
            'wave_id' => $this->wave->id,
            'date' => Carbon::tomorrow()->toDateString(),
            'rooms' => [
                [
                    'title' => 'Seminar Proposal Gelombang 1 - Ruang 1',
                    'room_name' => 'Ruang 1 (R. Sidang)',
                    'chairman_id' => $this->dosenList[0]->id,
                    'moderator_id' => $this->dosenList[2]->id,
                    'meeting_link' => null,
                    'sessions' => [
                        [
                            'thesis_id' => $thesis->id,
                            'start_time' => '08:30',
                            'end_time' => '09:20',
                            'examiner1_id' => $this->dosenList[2]->id,
                            'examiner2_id' => $this->dosenList[3]->id,
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)->postJson(route('seminar-schedules.simulation.apply'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('seminar_schedules', [
            'wave_id' => $this->wave->id,
            'location' => 'Ruang 1 (R. Sidang)',
            'chairman_id' => $this->dosenList[0]->id,
        ]);

        $this->assertDatabaseHas('seminar_schedule_details', [
            'thesis_id' => $thesis->id,
            'examiner1_id' => $this->dosenList[2]->id,
            'examiner2_id' => $this->dosenList[3]->id,
        ]);
    }
}

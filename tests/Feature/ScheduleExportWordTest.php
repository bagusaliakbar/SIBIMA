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

class ScheduleExportWordTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $kaprodi;
    protected $dosen;
    protected $student;
    protected $wave;
    protected $seminarSchedule;
    protected $defenseSchedule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->kaprodi = User::factory()->create(['role' => 'kaprodi', 'name' => 'Kaprodi Fasilkom']);
        $this->dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing']);
        $this->student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Test', 'identifier' => '202201001']);

        $this->wave = Wave::create([
            'name' => 'Gelombang 1 2026',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(10),
        ]);

        $thesis = Thesis::create([
            'student_id' => $this->student->id,
            'title' => 'Sistem Informasi Skripsi dan Ujian',
            'status' => 'active',
            'pembimbing1_id' => $this->dosen->id,
        ]);

        // Seminar Schedule
        $this->seminarSchedule = SeminarSchedule::create([
            'title' => 'Ruang 1 - Seminar Proposal',
            'date' => now()->toDateString(),
            'location' => 'Lab Komputer 1',
            'meeting_link' => 'https://meet.google.com/abc-defg-hij',
            'wave_id' => $this->wave->id,
            'chairman_id' => $this->dosen->id,
            'created_by' => $this->admin->id,
        ]);

        SeminarScheduleDetail::create([
            'seminar_schedule_id' => $this->seminarSchedule->id,
            'thesis_id' => $thesis->id,
            'start_time' => '08:00',
            'end_time' => '08:45',
            'examiner1_id' => $this->dosen->id,
        ]);

        // Non-thesis activity in seminar
        SeminarScheduleDetail::create([
            'seminar_schedule_id' => $this->seminarSchedule->id,
            'activity_name' => 'Istirahat dan Sholat',
            'start_time' => '12:00',
            'end_time' => '13:00',
        ]);

        // Defense Schedule
        $this->defenseSchedule = ThesisDefenseSchedule::create([
            'title' => 'Ruang 1 - Sidang Skripsi',
            'date' => now()->toDateString(),
            'location' => 'Ruang Sidang Utama',
            'meeting_link' => 'https://meet.google.com/xyz-uvw-rst',
            'wave_id' => $this->wave->id,
            'chairman_id' => $this->dosen->id,
            'created_by' => $this->admin->id,
        ]);

        ThesisDefenseScheduleDetail::create([
            'thesis_defense_schedule_id' => $this->defenseSchedule->id,
            'thesis_id' => $thesis->id,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'examiner1_id' => $this->dosen->id,
        ]);
    }

    public function test_admin_can_export_seminar_schedule_to_word()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('seminar-schedules.export-word', $this->seminarSchedule));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.wordprocessingml.document', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.docx', $response->headers->get('Content-Disposition'));
    }

    public function test_admin_can_export_batch_seminar_schedules_to_word()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('seminar-schedules.export-batch-word', ['wave_id' => $this->wave->id]));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.wordprocessingml.document', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.docx', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('Jadwal_Seminar_Gelombang_', $response->headers->get('Content-Disposition'));
    }

    public function test_kaprodi_can_export_defense_schedule_to_word()
    {
        $response = $this->actingAs($this->kaprodi)
            ->get(route('thesis-defense-schedules.export-word', $this->defenseSchedule));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.wordprocessingml.document', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.docx', $response->headers->get('Content-Disposition'));
    }

    public function test_kaprodi_can_export_batch_defense_schedules_to_word()
    {
        $response = $this->actingAs($this->kaprodi)
            ->get(route('thesis-defense-schedules.export-batch-word', ['wave_id' => $this->wave->id]));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.wordprocessingml.document', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.docx', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('Jadwal_Sidang_Skripsi_Gelombang_', $response->headers->get('Content-Disposition'));
    }

    public function test_dosen_forbidden_from_exporting_word()
    {
        $this->actingAs($this->dosen)
            ->get(route('seminar-schedules.export-word', $this->seminarSchedule))
            ->assertStatus(403);

        $this->actingAs($this->dosen)
            ->get(route('seminar-schedules.export-batch-word', ['wave_id' => $this->wave->id]))
            ->assertStatus(403);

        $this->actingAs($this->dosen)
            ->get(route('thesis-defense-schedules.export-word', $this->defenseSchedule))
            ->assertStatus(403);

        $this->actingAs($this->dosen)
            ->get(route('thesis-defense-schedules.export-batch-word', ['wave_id' => $this->wave->id]))
            ->assertStatus(403);
    }

    public function test_student_forbidden_from_exporting_word()
    {
        $this->actingAs($this->student)
            ->get(route('seminar-schedules.export-word', $this->seminarSchedule))
            ->assertStatus(403);

        $this->actingAs($this->student)
            ->get(route('seminar-schedules.export-batch-word', ['wave_id' => $this->wave->id]))
            ->assertStatus(403);

        $this->actingAs($this->student)
            ->get(route('thesis-defense-schedules.export-word', $this->defenseSchedule))
            ->assertStatus(403);

        $this->actingAs($this->student)
            ->get(route('thesis-defense-schedules.export-batch-word', ['wave_id' => $this->wave->id]))
            ->assertStatus(403);
    }

    public function test_admin_and_kaprodi_views_render_export_word_buttons()
    {
        // Admin index seminar
        $this->actingAs($this->admin)
            ->get(route('seminar-schedules.index'))
            ->assertStatus(200)
            ->assertSee(route('seminar-schedules.export-batch-word'))
            ->assertSee(route('seminar-schedules.export-word', $this->seminarSchedule));

        // Admin show seminar
        $this->actingAs($this->admin)
            ->get(route('seminar-schedules.show', $this->seminarSchedule))
            ->assertStatus(200)
            ->assertSee(route('seminar-schedules.export-word', $this->seminarSchedule));

        // Kaprodi index defense
        $this->actingAs($this->kaprodi)
            ->get(route('thesis-defense-schedules.index'))
            ->assertStatus(200)
            ->assertSee(route('thesis-defense-schedules.export-batch-word'))
            ->assertSee(route('thesis-defense-schedules.export-word', $this->defenseSchedule));

        // Kaprodi show defense
        $this->actingAs($this->kaprodi)
            ->get(route('thesis-defense-schedules.show', $this->defenseSchedule))
            ->assertStatus(200)
            ->assertSee(route('thesis-defense-schedules.export-word', $this->defenseSchedule));

        // Dosen show seminar does NOT see Word export button
        $this->actingAs($this->dosen)
            ->get(route('seminar-schedules.show', $this->seminarSchedule))
            ->assertStatus(200)
            ->assertDontSee(route('seminar-schedules.export-word', $this->seminarSchedule));
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\Wave;
use App\Models\SeminarSchedule;
use App\Models\SeminarScheduleDetail;
use App\Models\SeminarRevision;
use App\Models\SeminarRevisionMessage;
use App\Models\ThesisDefenseSchedule;
use App\Models\ThesisDefenseScheduleDetail;
use App\Models\ThesisDefenseRevision;
use App\Models\ThesisDefenseRevisionMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointByPointRevisionTest extends TestCase
{
    use RefreshDatabase;

    protected $examiner;
    protected $student;
    protected $thesis;
    protected $wave;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->examiner = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Penguji Teladan']);
        $this->student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Mahasiswa']);
        $this->admin = User::factory()->create(['role' => 'admin']);

        $this->thesis = Thesis::create([
            'student_id' => $this->student->id,
            'title' => 'Rancang Bangun Sistem Informasi Point Revisi',
            'status' => 'active',
        ]);

        $this->wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);
    }

    public function test_examiner_can_submit_point_by_point_revision_for_seminar()
    {
        $schedule = SeminarSchedule::create([
            'title' => 'Seminar Proposal Gelombang 1',
            'date' => now()->toDateString(),
            'location' => 'Ruang 101',
            'wave_id' => $this->wave->id,
            'created_by' => $this->admin->id,
        ]);

        $detail = SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $this->thesis->id,
            'activity_name' => 'Seminar Proposal',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'order' => 1,
            'examiner1_id' => $this->examiner->id,
            'examiner2_id' => null,
        ]);

        $points = [
            'Perbaiki rumusan masalah pada Bab 1 agar lebih terfokus.',
            'Tambahkan referensi jurnal internasional bereputasi 5 tahun terakhir.',
            'Lengkapi diagram flowchart alur sistem pada Bab 3.',
        ];

        $response = $this->actingAs($this->examiner)->post(route('seminar-examiner.store-revision', $detail->id), [
            'revision_points' => $points,
            'additional_notes' => 'Harap diselesaikan dalam waktu 7 hari kerja.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Verify SeminarRevision is created
        $revision = SeminarRevision::where('seminar_schedule_detail_id', $detail->id)
            ->where('examiner_id', $this->examiner->id)
            ->first();

        $this->assertNotNull($revision);

        // Verify Message compiled into numbered list
        $message = SeminarRevisionMessage::where('seminar_revision_id', $revision->id)->first();
        $this->assertNotNull($message);
        $this->assertStringContainsString('1. Perbaiki rumusan masalah pada Bab 1 agar lebih terfokus.', $message->message);
        $this->assertStringContainsString('2. Tambahkan referensi jurnal internasional bereputasi 5 tahun terakhir.', $message->message);
        $this->assertStringContainsString('3. Lengkapi diagram flowchart alur sistem pada Bab 3.', $message->message);
        $this->assertStringContainsString('Catatan Tambahan: Harap diselesaikan dalam waktu 7 hari kerja.', $message->message);

        // Test Student View
        $studentResponse = $this->actingAs($this->student)->get(route('student-seminar-revisions.show', $revision->id));
        $studentResponse->assertStatus(200);
        $studentResponse->assertSee('Daftar Poin Revisi');
        $studentResponse->assertSee('Perbaiki rumusan masalah pada Bab 1');

        // Test Print PDF View
        $pdfResponse = $this->actingAs($this->student)->get(route('student-seminar-revisions.print-pdf', $revision->id));
        $pdfResponse->assertStatus(200);
    }

    public function test_examiner_can_submit_point_by_point_revision_for_thesis_defense()
    {
        $defenseSchedule = ThesisDefenseSchedule::create([
            'title' => 'Sidang Skripsi Gelombang 1',
            'date' => now()->toDateString(),
            'location' => 'Ruang Sidang Utama',
            'wave_id' => $this->wave->id,
            'created_by' => $this->admin->id,
        ]);

        $detail = ThesisDefenseScheduleDetail::create([
            'thesis_defense_schedule_id' => $defenseSchedule->id,
            'thesis_id' => $this->thesis->id,
            'activity_name' => 'Sidang Skripsi',
            'start_time' => '13:00',
            'end_time' => '14:00',
            'order' => 1,
            'examiner1_id' => $this->examiner->id,
            'examiner2_id' => null,
        ]);

        $points = [
            'Revisi kesimpulan pada Bab 5 agar menjawab seluruh rumusan masalah.',
            'Perbaiki format daftar pustaka sesuai panduan IEEE / APA.',
        ];

        $response = $this->actingAs($this->examiner)->post(route('defense-examiner.store-revision', $detail->id), [
            'revision_points' => $points,
            'additional_notes' => 'Segera hubungi penguji setelah revisi diunggah.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Verify ThesisDefenseRevision is created
        $revision = ThesisDefenseRevision::where('thesis_defense_schedule_detail_id', $detail->id)
            ->where('examiner_id', $this->examiner->id)
            ->first();

        $this->assertNotNull($revision);

        // Verify Message compiled into numbered list
        $message = ThesisDefenseRevisionMessage::where('thesis_defense_revision_id', $revision->id)->first();
        $this->assertNotNull($message);
        $this->assertStringContainsString('1. Revisi kesimpulan pada Bab 5 agar menjawab seluruh rumusan masalah.', $message->message);
        $this->assertStringContainsString('2. Perbaiki format daftar pustaka sesuai panduan IEEE / APA.', $message->message);
        $this->assertStringContainsString('Catatan Tambahan: Segera hubungi penguji setelah revisi diunggah.', $message->message);

        // Test Student View
        $studentResponse = $this->actingAs($this->student)->get(route('student-defense-revisions.show', $revision->id));
        $studentResponse->assertStatus(200);
        $studentResponse->assertSee('Daftar Poin Revisi');
        $studentResponse->assertSee('Revisi kesimpulan pada Bab 5');

        // Test Print PDF View
        $pdfResponse = $this->actingAs($this->student)->get(route('student-defense-revisions.print-pdf', $revision->id));
        $pdfResponse->assertStatus(200);
    }

    public function test_examiner_can_still_submit_free_text_notes_backward_compatibility()
    {
        $schedule = SeminarSchedule::create([
            'title' => 'Seminar Proposal Gelombang 1',
            'date' => now()->toDateString(),
            'location' => 'Ruang 101',
            'wave_id' => $this->wave->id,
            'created_by' => $this->admin->id,
        ]);

        $detail = SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $this->thesis->id,
            'activity_name' => 'Seminar Proposal',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'order' => 1,
            'examiner1_id' => $this->examiner->id,
            'examiner2_id' => null,
        ]);

        $freeText = "Catatan bebas penguji tanpa format poin angka khusus.\nHarap diperhatikan baik-baik.";

        $response = $this->actingAs($this->examiner)->post(route('seminar-examiner.store-revision', $detail->id), [
            'revision_notes' => $freeText,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $revision = SeminarRevision::where('seminar_schedule_detail_id', $detail->id)
            ->where('examiner_id', $this->examiner->id)
            ->first();

        $message = SeminarRevisionMessage::where('seminar_revision_id', $revision->id)->first();
        $this->assertNotNull($message);
        $this->assertEquals($freeText, $message->message);

        // Student View should render free text gracefully
        $studentResponse = $this->actingAs($this->student)->get(route('student-seminar-revisions.show', $revision->id));
        $studentResponse->assertStatus(200);
        $studentResponse->assertSee('Catatan bebas penguji tanpa format poin angka khusus.');
    }
}

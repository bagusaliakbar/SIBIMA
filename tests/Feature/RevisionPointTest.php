<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\Wave;
use App\Models\SeminarSchedule;
use App\Models\SeminarScheduleDetail;
use App\Models\ThesisDefenseSchedule;
use App\Models\ThesisDefenseScheduleDetail;
use App\Models\SeminarRevision;
use App\Models\SeminarRevisionMessage;
use App\Models\ThesisDefenseRevision;
use App\Models\ThesisDefenseRevisionMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RevisionPointTest extends TestCase
{
    use RefreshDatabase;

    public function test_examiner_can_submit_revision_with_points_for_seminar()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Implementasi Poin Revisi Seminar',
            'status' => 'active',
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        $schedule = SeminarSchedule::create([
            'title' => 'Seminar Proposal Gelombang 1',
            'date' => now()->toDateString(),
            'location' => 'Ruang 101',
            'wave_id' => $wave->id,
            'created_by' => $dosen->id,
        ]);

        $detail = SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'activity_name' => 'Seminar Proposal',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'examiner1_id' => $dosen->id,
            'examiner2_id' => null,
        ]);

        $response = $this->actingAs($dosen)->post(route('seminar-examiner.store-revision', $detail->id), [
            'revision_mode' => 'points',
            'revision_points' => [
                'Perbaiki rumusan masalah pada Bab 1',
                'Tambahkan tinjauan pustaka jurnal internasional',
                'Sesuaikan format sitasi APA 7th edition'
            ],
            'additional_notes' => 'Harap diselesaikan dalam waktu 1 minggu.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $revision = SeminarRevision::where('seminar_schedule_detail_id', $detail->id)
            ->where('examiner_id', $dosen->id)
            ->first();

        $this->assertNotNull($revision);
        
        $message = SeminarRevisionMessage::where('seminar_revision_id', $revision->id)
            ->where('sender_id', $dosen->id)
            ->first();

        $this->assertNotNull($message);
        $this->assertStringContainsString('1. Perbaiki rumusan masalah pada Bab 1', $message->message);
        $this->assertStringContainsString('2. Tambahkan tinjauan pustaka jurnal internasional', $message->message);
        $this->assertStringContainsString('3. Sesuaikan format sitasi APA 7th edition', $message->message);
        $this->assertStringContainsString('Catatan Tambahan:', $message->message);
        $this->assertStringContainsString('Harap diselesaikan dalam waktu 1 minggu.', $message->message);
    }

    public function test_examiner_can_submit_revision_with_points_for_defense()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Implementasi Poin Revisi Sidang',
            'status' => 'active',
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        $schedule = ThesisDefenseSchedule::create([
            'title' => 'Sidang Skripsi Gelombang 1',
            'date' => now()->toDateString(),
            'location' => 'Ruang Sidang',
            'wave_id' => $wave->id,
            'created_by' => $dosen->id,
        ]);

        $detail = ThesisDefenseScheduleDetail::create([
            'thesis_defense_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'examiner1_id' => $dosen->id,
            'examiner2_id' => null,
        ]);

        $response = $this->actingAs($dosen)->post(route('defense-examiner.store-revision', $detail->id), [
            'revision_mode' => 'points',
            'revision_points' => [
                'Perjelas kesimpulan pada Bab 5',
                'Perbaiki lampiran kuesioner pengujian sistem'
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $revision = ThesisDefenseRevision::where('thesis_defense_schedule_detail_id', $detail->id)
            ->where('examiner_id', $dosen->id)
            ->first();

        $this->assertNotNull($revision);

        $message = ThesisDefenseRevisionMessage::where('thesis_defense_revision_id', $revision->id)
            ->where('sender_id', $dosen->id)
            ->first();

        $this->assertNotNull($message);
        $this->assertStringContainsString('1. Perjelas kesimpulan pada Bab 5', $message->message);
        $this->assertStringContainsString('2. Perbaiki lampiran kuesioner pengujian sistem', $message->message);
    }

    public function test_examiner_can_submit_freeform_revision_for_backward_compatibility()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Implementasi Revisi Bebas',
            'status' => 'active',
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        $schedule = SeminarSchedule::create([
            'title' => 'Seminar Proposal Gelombang 1',
            'date' => now()->toDateString(),
            'location' => 'Ruang 101',
            'wave_id' => $wave->id,
            'created_by' => $dosen->id,
        ]);

        $detail = SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'activity_name' => 'Seminar Proposal',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'examiner1_id' => $dosen->id,
            'examiner2_id' => null,
        ]);

        $freeformText = "Silakan perbaiki bab 1 dan bab 2 secara keseluruhan sesuai catatan yang dicoret pada naskah cetak.";

        $response = $this->actingAs($dosen)->post(route('seminar-examiner.store-revision', $detail->id), [
            'revision_mode' => 'freeform',
            'revision_notes' => $freeformText,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $revision = SeminarRevision::where('seminar_schedule_detail_id', $detail->id)
            ->where('examiner_id', $dosen->id)
            ->first();

        $message = SeminarRevisionMessage::where('seminar_revision_id', $revision->id)
            ->where('sender_id', $dosen->id)
            ->first();

        $this->assertEquals($freeformText, $message->message);
    }
}

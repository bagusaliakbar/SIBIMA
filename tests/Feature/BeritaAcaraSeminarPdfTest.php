<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\Wave;
use App\Models\SeminarSchedule;
use App\Models\SeminarScheduleDetail;
use App\Models\SeminarRevision;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class BeritaAcaraSeminarPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_berita_acara_seminar_pdf_renders_successfully()
    {
        Storage::fake('local');

        // Create a test signature file on fake local disk
        $signatureContent = 'fake_signature_binary_data';
        $encryptedSignature = Crypt::encrypt($signatureContent);
        Storage::disk('local')->put('signatures/test_sig1.enc', $encryptedSignature);
        Storage::disk('local')->put('signatures/test_sig2.enc', $encryptedSignature);
        Storage::disk('local')->put('signatures/test_p1.enc', $encryptedSignature);

        // 1. Create Users
        $admin = User::factory()->create(['role' => 'admin']);
        $p1 = User::factory()->create([
            'role' => 'dosen',
            'name' => 'Dosen Pembimbing 1',
            'signature' => 'signatures/test_p1.enc'
        ]);
        $p2 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing 2']);
        $examiner1 = User::factory()->create([
            'role' => 'dosen',
            'name' => 'Dosen Penguji 1',
            'signature' => 'signatures/test_sig1.enc'
        ]);
        $examiner2 = User::factory()->create([
            'role' => 'dosen',
            'name' => 'Dosen Penguji 2',
            'signature' => 'signatures/test_sig2.enc'
        ]);
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Mahasiswa Test',
            'identifier' => '12345678'
        ]);

        // 2. Create Wave
        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        // 3. Create Thesis
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Judul Skripsi Test PDF',
            'abstract' => 'Abstrak Skripsi',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'acc_up_p1' => true,
            'acc_up_p2' => true,
        ]);

        // 4. Create Seminar Schedule & Detail
        $schedule = SeminarSchedule::create([
            'title' => 'Seminar Proposal Gelombang 1',
            'date' => now()->addDays(1),
            'chairman_id' => $examiner1->id,
            'moderator_id' => $examiner2->id,
            'location' => 'Ruang Rapat Fasilkom',
            'created_by' => $admin->id,
            'wave_id' => $wave->id,
        ]);

        $detail = SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'activity_name' => 'Seminar Proposal',
            'start_time' => now()->addDays(1)->setHour(9)->setMinute(0),
            'end_time' => now()->addDays(1)->setHour(10)->setMinute(0),
            'examiner1_id' => $examiner1->id,
            'examiner2_id' => $examiner2->id,
            'order' => 1,
        ]);

        // 5. Create Revisions/Scores
        SeminarRevision::create([
            'seminar_schedule_detail_id' => $detail->id,
            'examiner_id' => $examiner1->id,
            'revision_text' => 'Catatan revisi penguji 1',
            'status' => 'approved',
            'score' => 85,
        ]);

        SeminarRevision::create([
            'seminar_schedule_detail_id' => $detail->id,
            'examiner_id' => $examiner2->id,
            'revision_text' => 'Catatan revisi penguji 2',
            'status' => 'approved',
            'score' => 80,
        ]);

        // 6. Access PDF Route as Admin
        $response = $this->actingAs($admin)
            ->get(route('monitoring.seminar-scores.berita-acara', $detail->id));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}

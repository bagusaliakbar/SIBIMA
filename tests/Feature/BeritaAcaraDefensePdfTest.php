<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\Wave;
use App\Models\ThesisDefenseSchedule;
use App\Models\ThesisDefenseScheduleDetail;
use App\Models\ThesisDefenseRevision;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class BeritaAcaraDefensePdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_berita_acara_defense_pdf_renders_successfully()
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

        // 4. Create Thesis Defense Schedule & Detail
        $schedule = ThesisDefenseSchedule::create([
            'title' => 'Sidang Skripsi Gelombang 1',
            'date' => now()->addDays(1),
            'chairman_id' => $examiner1->id,
            'moderator_id' => $examiner2->id,
            'location' => 'Ruang Rapat Fasilkom',
            'created_by' => $admin->id,
            'wave_id' => $wave->id,
        ]);

        $detail = ThesisDefenseScheduleDetail::create([
            'thesis_defense_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'activity_name' => 'Sidang Skripsi',
            'start_time' => now()->addDays(1)->setHour(9)->setMinute(0),
            'end_time' => now()->addDays(1)->setHour(10)->setMinute(0),
            'examiner1_id' => $examiner1->id,
            'examiner2_id' => $examiner2->id,
            'order' => 1,
        ]);

        // 5. Create Revisions/Scores
        ThesisDefenseRevision::create([
            'thesis_defense_schedule_detail_id' => $detail->id,
            'examiner_id' => $examiner1->id,
            'revision_notes' => 'Catatan revisi penguji 1',
            'status' => 'approved',
            'score_presentation' => 85,
            'score_explanation' => 80,
            'score_writing' => 90,
        ]);

        ThesisDefenseRevision::create([
            'thesis_defense_schedule_detail_id' => $detail->id,
            'examiner_id' => $examiner2->id,
            'revision_notes' => 'Catatan revisi penguji 2',
            'status' => 'approved',
            'score_presentation' => 80,
            'score_explanation' => 85,
            'score_writing' => 85,
        ]);

        ThesisDefenseRevision::create([
            'thesis_defense_schedule_detail_id' => $detail->id,
            'examiner_id' => $p1->id,
            'revision_notes' => 'Catatan revisi pembimbing 1',
            'status' => 'approved',
            'score_presentation' => 90,
            'score_explanation' => 90,
            'score_writing' => 90,
        ]);

        // 6. Access PDF Route as Admin
        $response = $this->actingAs($admin)
            ->get(route('monitoring.defense-scores.berita-acara', $detail->id));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}

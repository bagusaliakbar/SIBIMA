<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\Wave;
use App\Models\SeminarApplication;
use App\Models\ThesisDefenseApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicationFileUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_seminar_application_url_submission()
    {
        $p1 = User::factory()->create(['role' => 'dosen']);
        $p2 = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Judul Skripsi',
            'abstract' => 'Abstrak Skripsi',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'acc_up_p1' => true,
            'acc_up_p2' => true,
        ]);

        // 1. Partial submit
        $response = $this->actingAs($student)
            ->post(route('seminar-applications.store'), [
                'file_acc_pembimbing' => 'https://drive.google.com/file/d/acc',
                'file_pembayaran' => 'https://drive.google.com/file/d/bayar',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['file_kartu_bimbingan', 'file_skripsi', 'file_formulir']);

        // 2. Submit all files
        $response = $this->actingAs($student)
            ->post(route('seminar-applications.store'), [
                'file_acc_pembimbing' => 'https://drive.google.com/file/d/acc',
                'file_pembayaran' => 'https://drive.google.com/file/d/bayar',
                'file_kartu_bimbingan' => 'https://drive.google.com/file/d/kartu',
                'file_skripsi' => 'https://drive.google.com/file/d/skripsi',
                'file_formulir' => 'https://drive.google.com/file/d/formulir',
            ]);

        $response->assertRedirect(route('seminar-applications.index'));
        $response->assertSessionHasNoErrors();

        // Assert Application is created
        $application = SeminarApplication::where('thesis_id', $thesis->id)->first();
        $this->assertNotNull($application);
        $this->assertEquals('https://drive.google.com/file/d/acc', $application->file_acc_pembimbing);
        $this->assertEquals('https://drive.google.com/file/d/skripsi', $application->file_skripsi);
    }

    public function test_thesis_defense_application_url_submission()
    {
        $p1 = User::factory()->create(['role' => 'dosen']);
        $p2 = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Judul Skripsi',
            'abstract' => 'Abstrak Skripsi',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'acc_sidang_p1' => true,
            'acc_sidang_p2' => true,
        ]);

        // Submit only 2 of 20 files
        $response = $this->actingAs($student)
            ->post(route('thesis-defense-applications.store'), [
                'file_formulir' => 'https://drive.google.com/file/d/formulir',
                'file_transkrip' => 'https://drive.google.com/file/d/transkrip',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['file_acc_pembimbing', 'file_ktm', 'file_ijazah']);

        // Compile all 20 files needed for second submit
        $files = [
            'file_formulir' => 'https://drive.google.com/file/d/formulir',
            'file_transkrip' => 'https://drive.google.com/file/d/transkrip',
            'file_acc_pembimbing' => 'https://drive.google.com/file/d/acc',
            'file_logbook' => 'https://drive.google.com/file/d/logbook',
            'file_pembayaran' => 'https://drive.google.com/file/d/bayar',
            'file_skripsi' => 'https://drive.google.com/file/d/skripsi',
            'file_ktm' => 'https://drive.google.com/file/d/ktm',
            'file_pkkmb_univ' => 'https://drive.google.com/file/d/pkkmb_u',
            'file_pkkmb_fak' => 'https://drive.google.com/file/d/pkkmb_f',
            'file_makrab' => 'https://drive.google.com/file/d/makrab',
            'file_cisco' => 'https://drive.google.com/file/d/cisco',
            'file_workshop' => 'https://drive.google.com/file/d/workshop',
            'file_organisasi' => 'https://drive.google.com/file/d/organisasi',
            'file_toefl' => 'https://drive.google.com/file/d/toefl',
            'file_kewirausahaan' => 'https://drive.google.com/file/d/wirausaha',
            'file_tahsin' => 'https://drive.google.com/file/d/tahsin',
            'file_komputer' => 'https://drive.google.com/file/d/komputer',
            'file_perpus_pinjam' => 'https://drive.google.com/file/d/perpus1',
            'file_perpus_sumbang' => 'https://drive.google.com/file/d/perpus2',
            'file_ijazah' => 'https://drive.google.com/file/d/ijazah',
        ];

        $response = $this->actingAs($student)
            ->post(route('thesis-defense-applications.store'), $files);

        $response->assertRedirect(route('thesis-defense-applications.index'));
        $response->assertSessionHasNoErrors();

        // Assert Application is created
        $application = ThesisDefenseApplication::where('thesis_id', $thesis->id)->first();
        $this->assertNotNull($application);
        $this->assertEquals('https://drive.google.com/file/d/formulir', $application->file_formulir);
        $this->assertEquals('https://drive.google.com/file/d/ijazah', $application->file_ijazah);
    }

    public function test_seminar_application_redirects_if_already_completed()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $p1 = User::factory()->create(['role' => 'dosen']);
        $p2 = User::factory()->create(['role' => 'dosen']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Judul Skripsi',
            'abstract' => 'Abstrak Skripsi',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'acc_up_p1' => true,
            'acc_up_p2' => true,
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        // Create approved application
        SeminarApplication::create([
            'thesis_id' => $thesis->id,
            'wave_id' => $wave->id,
            'file_acc_pembimbing' => 'file.pdf',
            'file_pembayaran' => 'file.pdf',
            'file_kartu_bimbingan' => 'file.pdf',
            'file_skripsi' => 'file.pdf',
            'file_formulir' => 'file.pdf',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($student)
            ->get(route('seminar-applications.index'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('warning', 'Pendaftaran seminar sudah ditutup karena Anda sudah mendaftar atau melaksanakan seminar.');
    }

    public function test_thesis_defense_application_redirects_if_already_completed()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $p1 = User::factory()->create(['role' => 'dosen']);
        $p2 = User::factory()->create(['role' => 'dosen']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Judul Skripsi',
            'abstract' => 'Abstrak Skripsi',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'acc_sidang_p1' => true,
            'acc_sidang_p2' => true,
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        // Create approved application
        ThesisDefenseApplication::create([
            'thesis_id' => $thesis->id,
            'wave_id' => $wave->id,
            'file_formulir' => 'file.pdf',
            'file_transkrip' => 'file.pdf',
            'file_acc_pembimbing' => 'file.pdf',
            'file_logbook' => 'file.pdf',
            'file_pembayaran' => 'file.pdf',
            'file_skripsi' => 'file.pdf',
            'file_ktm' => 'file.pdf',
            'file_pkkmb_univ' => 'file.pdf',
            'file_pkkmb_fak' => 'file.pdf',
            'file_makrab' => 'file.pdf',
            'file_cisco' => 'file.pdf',
            'file_workshop' => 'file.pdf',
            'file_organisasi' => 'file.pdf',
            'file_toefl' => 'file.pdf',
            'file_kewirausahaan' => 'file.pdf',
            'file_tahsin' => 'file.pdf',
            'file_komputer' => 'file.pdf',
            'file_perpus_pinjam' => 'file.pdf',
            'file_perpus_sumbang' => 'file.pdf',
            'file_ijazah' => 'file.pdf',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($student)
            ->get(route('thesis-defense-applications.index'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('warning', 'Pendaftaran sidang skripsi sudah ditutup karena Anda sudah mendaftar atau melaksanakan sidang.');
    }
}

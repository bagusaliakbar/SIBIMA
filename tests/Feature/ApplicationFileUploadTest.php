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

    public function test_seminar_application_file_preservation_on_validation_failure()
    {
        Storage::fake('local');

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

        // 1. Submit partial files: only acc_pembimbing and pembayaran
        $fileAcc = UploadedFile::fake()->create('acc.pdf', 500);
        $filePembayaran = UploadedFile::fake()->create('bayar.png', 500);

        $response = $this->actingAs($student)
            ->post(route('seminar-applications.store'), [
                'file_acc_pembimbing' => $fileAcc,
                'file_pembayaran' => $filePembayaran,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['file_kartu_bimbingan', 'file_skripsi', 'file_formulir']);

        // Assert files are cached in session and temp folder
        $this->assertTrue(session()->has('seminar_uploads.path.file_acc_pembimbing'));
        $this->assertTrue(session()->has('seminar_uploads.path.file_pembayaran'));
        $this->assertEquals('acc.pdf', session('seminar_uploads.name.file_acc_pembimbing'));

        $tempAccPath = session('seminar_uploads.path.file_acc_pembimbing');
        $tempPembayaranPath = session('seminar_uploads.path.file_pembayaran');

        Storage::disk('local')->assertExists($tempAccPath);
        Storage::disk('local')->assertExists($tempPembayaranPath);

        // 2. Submit the remaining files
        $fileKartu = UploadedFile::fake()->create('kartu.pdf', 500);
        $fileSkripsi = UploadedFile::fake()->create('skripsi.pdf', 2000);
        $fileFormulir = UploadedFile::fake()->create('formulir.docx', 500);

        $response = $this->actingAs($student)
            ->post(route('seminar-applications.store'), [
                'file_kartu_bimbingan' => $fileKartu,
                'file_skripsi' => $fileSkripsi,
                'file_formulir' => $fileFormulir,
            ]);

        $response->assertRedirect(route('seminar-applications.index'));
        $response->assertSessionHasNoErrors();

        // Assert Application is created
        $application = SeminarApplication::where('thesis_id', $thesis->id)->first();
        $this->assertNotNull($application);

        // Assert temp uploads session is cleared
        $this->assertFalse(session()->has('seminar_uploads'));

        // Assert files are moved to permanent folders
        Storage::disk('local')->assertExists($application->file_acc_pembimbing);
        Storage::disk('local')->assertExists($application->file_pembayaran);
        Storage::disk('local')->assertExists($application->file_kartu_bimbingan);
        Storage::disk('local')->assertExists($application->file_skripsi);
        Storage::disk('local')->assertExists($application->file_formulir);

        // Assert temp files are removed/moved
        Storage::disk('local')->assertMissing($tempAccPath);
        Storage::disk('local')->assertMissing($tempPembayaranPath);
    }

    public function test_thesis_defense_application_file_preservation_on_validation_failure()
    {
        Storage::fake('local');

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
        $fileFormulir = UploadedFile::fake()->create('formulir.pdf', 500);
        $fileTranskrip = UploadedFile::fake()->create('transkrip.pdf', 500);

        $response = $this->actingAs($student)
            ->post(route('thesis-defense-applications.store'), [
                'file_formulir' => $fileFormulir,
                'file_transkrip' => $fileTranskrip,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['file_acc_pembimbing', 'file_ktm', 'file_ijazah']);

        $this->assertTrue(session()->has('defense_uploads.path.file_formulir'));
        $this->assertTrue(session()->has('defense_uploads.path.file_transkrip'));

        $tempFormulirPath = session('defense_uploads.path.file_formulir');
        $tempTranskripPath = session('defense_uploads.path.file_transkrip');

        // Compile all the files needed for second submit
        $files = [
            'file_acc_pembimbing' => UploadedFile::fake()->create('acc.pdf', 500),
            'file_logbook' => UploadedFile::fake()->create('logbook.pdf', 500),
            'file_pembayaran' => UploadedFile::fake()->create('pembayaran.pdf', 500),
            'file_skripsi' => UploadedFile::fake()->create('skripsi.pdf', 3000),
            'file_ktm' => UploadedFile::fake()->create('ktm.png', 500),
            'file_pkkmb_univ' => UploadedFile::fake()->create('pkkmb_u.pdf', 500),
            'file_pkkmb_fak' => UploadedFile::fake()->create('pkkmb_f.pdf', 500),
            'file_makrab' => UploadedFile::fake()->create('makrab.pdf', 500),
            'file_cisco' => UploadedFile::fake()->create('cisco.pdf', 500),
            'file_workshop' => UploadedFile::fake()->create('workshop.pdf', 500),
            'file_organisasi' => UploadedFile::fake()->create('organisasi.pdf', 500),
            'file_toefl' => UploadedFile::fake()->create('toefl.pdf', 500),
            'file_kewirausahaan' => UploadedFile::fake()->create('wirausaha.pdf', 500),
            'file_tahsin' => UploadedFile::fake()->create('tahsin.pdf', 500),
            'file_komputer' => UploadedFile::fake()->create('komputer.pdf', 500),
            'file_perpus_pinjam' => UploadedFile::fake()->create('perpus1.pdf', 500),
            'file_perpus_sumbang' => UploadedFile::fake()->create('perpus2.pdf', 500),
            'file_ijazah' => UploadedFile::fake()->create('ijazah.pdf', 500),
        ];

        $response = $this->actingAs($student)
            ->post(route('thesis-defense-applications.store'), $files);

        $response->assertRedirect(route('thesis-defense-applications.index'));
        $response->assertSessionHasNoErrors();

        // Assert Application is created
        $application = ThesisDefenseApplication::where('thesis_id', $thesis->id)->first();
        $this->assertNotNull($application);

        // Assert temp uploads session is cleared
        $this->assertFalse(session()->has('defense_uploads'));

        // Assert all files are present in permanent storage
        Storage::disk('local')->assertExists($application->file_formulir);
        Storage::disk('local')->assertExists($application->file_transkrip);
        Storage::disk('local')->assertExists($application->file_acc_pembimbing);
        Storage::disk('local')->assertExists($application->file_skripsi);

        // Assert temp files are removed/moved
        Storage::disk('local')->assertMissing($tempFormulirPath);
        Storage::disk('local')->assertMissing($tempTranskripPath);
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

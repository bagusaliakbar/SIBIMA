<?php

namespace Tests\Feature;

use App\Models\SeminarApplication;
use App\Models\Thesis;
use App\Models\ThesisDefenseApplication;
use App\Models\User;
use App\Models\Wave;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;
use ZipArchive;

class DefenseDigitalRegistrationFormTest extends TestCase
{
    use RefreshDatabase;

    private function createEligibleSetup(bool $p1HasSignature = false, bool $p2HasSignature = false): array
    {
        $samplePngDataUri = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

        $p1 = User::factory()->create([
            'role' => 'dosen',
            'name' => 'Dr. Pembimbing Satu, M.Kom',
            'identifier' => '0411223344',
            'signature' => $p1HasSignature ? Crypt::encrypt($samplePngDataUri) : null,
            'signature_token' => $p1HasSignature ? 'sig-token-p1-sidang' : null,
        ]);

        $p2 = User::factory()->create([
            'role' => 'dosen',
            'name' => 'Ir. Pembimbing Dua, M.T',
            'identifier' => '0455667788',
            'signature' => $p2HasSignature ? Crypt::encrypt($samplePngDataUri) : null,
            'signature_token' => $p2HasSignature ? 'sig-token-p2-sidang' : null,
        ]);

        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Budi Santoso',
            'identifier' => 'D1A220456',
            'email' => 'budi.santoso@student.unsub.ac.id',
            'entry_year' => 2022,
            'phone_number' => '081277665544',
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang 1 Sidang 2026',
            'is_active' => true,
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(10),
        ]);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Implementasi Sistem Deteksi Dini Anomali Jaringan Berbasis Machine Learning',
            'abstract' => 'Abstrak skripsi final.',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'acc_up_p1' => true,
            'acc_up_p2' => true,
            'acc_sidang_p1' => true,
            'acc_sidang_p2' => true,
            'status' => 'active',
        ]);

        return compact('p1', 'p2', 'student', 'wave', 'thesis');
    }

    private function sampleDriveFiles(): array
    {
        return [
            'file_transkrip' => 'https://drive.google.com/file/d/transkrip123/view',
            'file_acc_pembimbing' => 'https://drive.google.com/file/d/acc123/view',
            'file_logbook' => 'https://drive.google.com/file/d/logbook123/view',
            'file_pembayaran' => 'https://drive.google.com/file/d/bayar123/view',
            'file_skripsi' => 'https://drive.google.com/file/d/skripsi123/view',
            'file_ktm' => 'https://drive.google.com/file/d/ktm123/view',
            'file_pkkmb_univ' => 'https://drive.google.com/file/d/pkkmb_u123/view',
            'file_pkkmb_fak' => 'https://drive.google.com/file/d/pkkmb_f123/view',
            'file_makrab' => 'https://drive.google.com/file/d/makrab123/view',
            'file_cisco' => 'https://drive.google.com/file/d/cisco123/view',
            'file_workshop' => 'https://drive.google.com/file/d/workshop123/view',
            'file_organisasi' => 'https://drive.google.com/file/d/organisasi123/view',
            'file_toefl' => 'https://drive.google.com/file/d/toefl123/view',
            'file_kewirausahaan' => 'https://drive.google.com/file/d/wirausaha123/view',
            'file_tahsin' => 'https://drive.google.com/file/d/tahsin123/view',
            'file_komputer' => 'https://drive.google.com/file/d/komputer123/view',
            'file_perpus_pinjam' => 'https://drive.google.com/file/d/perpus1_123/view',
            'file_perpus_sumbang' => 'https://drive.google.com/file/d/perpus2_123/view',
            'file_ijazah' => 'https://drive.google.com/file/d/ijazah123/view',
        ];
    }

    public function test_student_sees_digital_defense_form_prefilled_from_seminar_without_file_formulir_upload(): void
    {
        $setup = $this->createEligibleSetup(true, false);

        // Seed previous SeminarApplication digital form to test automatic fallback prefill
        SeminarApplication::create([
            'thesis_id' => $setup['thesis']->id,
            'wave_id' => $setup['wave']->id,
            'status' => 'approved',
            'file_acc_pembimbing' => 'https://drive.google.com/file/d/sem_acc',
            'file_pembayaran' => 'https://drive.google.com/file/d/sem_pay',
            'file_kartu_bimbingan' => 'https://drive.google.com/file/d/sem_log',
            'file_skripsi' => 'https://drive.google.com/file/d/sem_draft',
            'form_data' => [
                'full_name' => $setup['student']->name,
                'birth_place' => 'Bandung',
                'birth_date' => '2003-08-17',
                'npm' => $setup['student']->identifier,
                'study_program' => 'Sistem Informasi / S1',
                'semester_number' => 8,
                'academic_year' => 'Ganjil 2026/2027',
                'address' => 'Jl. Otto Iskandardinata No. 12 Subang',
                'postal_code' => '41212',
                'home_phone' => '0260412222',
                'email' => $setup['student']->email,
                'phone_number' => $setup['student']->phone_number,
                'thesis_title' => $setup['thesis']->title,
            ],
            'statement_agreed_at' => now()->subMonth(),
        ]);

        $response = $this->actingAs($setup['student'])->get(route('thesis-defense-applications.index'));

        $response->assertStatus(200);
        $response->assertSee('Formulir Pendaftaran Sidang Digital');
        $response->assertSee('Sistem Informasi / S1');
        $response->assertSee('Budi Santoso');
        $response->assertSee('D1A220456');
        $response->assertSee('Implementasi Sistem Deteksi Dini Anomali Jaringan Berbasis Machine Learning');
        $response->assertSee('Bandung');
        $response->assertSee('Jl. Otto Iskandardinata No. 12 Subang');
        $response->assertSee('41212');
        $response->assertSee('Dr. Pembimbing Satu, M.Kom');
        $response->assertSee('Ir. Pembimbing Dua, M.T');
        $response->assertSee('Kosong + Kotak Materai 10.000');
        $response->assertSee('19 Berkas Google Drive');
        $response->assertSee('openBaak');
        $response->assertSee('handleSubmit');
        $response->assertSee('sibima_defense_draft_');
        $response->assertSee('driveLinkStatus');
        $response->assertDontSee('name="file_formulir"', false);
    }

    public function test_student_submits_digital_defense_form_and_can_download_verified_single_page_pdf(): void
    {
        $setup = $this->createEligibleSetup(true, false);
        $admin = User::factory()->create(['role' => 'admin']);
        $otherStudent = User::factory()->create(['role' => 'mahasiswa']);

        $payload = array_merge($this->sampleDriveFiles(), [
            'birth_place' => 'Subang',
            'birth_date' => '2003-08-17',
            'study_program' => 'Sistem Informasi / S1',
            'semester_number' => 8,
            'academic_year' => 'Ganjil 2026/2027',
            'address' => 'Jl. R.A Kartini KM 3, Kel. Pasirkareumbi, Subang',
            'postal_code' => '41211',
            'home_phone' => '0260411415',
            'email' => 'budi.santoso@student.unsub.ac.id',
            'phone_number' => '081277665544',
            'statement_agreed' => '1',
        ]);

        $storeRes = $this->actingAs($setup['student'])->post(route('thesis-defense-applications.store'), $payload);
        $storeRes->assertRedirect(route('thesis-defense-applications.index'));
        $storeRes->assertSessionHasNoErrors();

        $app = ThesisDefenseApplication::where('thesis_id', $setup['thesis']->id)->first();
        $this->assertNotNull($app);
        $this->assertTrue($app->hasDigitalForm());
        $this->assertNotNull($app->statement_agreed_at);
        $this->assertNotNull($app->verification_token);
        $this->assertNull($app->file_formulir);
        $this->assertEquals('Subang', $app->form_data['birth_place']);
        $this->assertEquals('Sistem Informasi / S1', $app->form_data['study_program']);
        $this->assertStringStartsWith('SBM-SDG-', $app->getIntegrityCode());

        // Biodata synced back to User profile
        $freshStudent = $setup['student']->fresh();
        $this->assertEquals('Subang', $freshStudent->birth_place);
        $this->assertEquals('2003-08-17', $freshStudent->birth_date->format('Y-m-d'));
        $this->assertEquals('Sistem Informasi / S1', $freshStudent->study_program);
        $this->assertEquals('41211', $freshStudent->postal_code);
        $this->assertEquals('0260411415', $freshStudent->home_phone);

        // Status page shows Print & Preview PDF action, clean Indonesian status title, status filter pills, and student self-cancel while pending
        $statusRes = $this->actingAs($freshStudent)->get(route('thesis-defense-applications.index'));
        $statusRes->assertStatus(200);
        $statusRes->assertSee('Status Pengajuan Sidang');
        $statusRes->assertDontSee('Status Pengajuan Sidang: PENDING');
        $statusRes->assertSee('Batalkan Pengajuan');
        $statusRes->assertSee(route('thesis-defense-applications.destroy', $app->id));
        $statusRes->assertSee('statusFilter');
        $statusRes->assertSee('Cetak Formulir (PDF)');
        $statusRes->assertSee('Pratinjau PDF');
        $statusRes->assertSee(route('thesis-defense-applications.form-pdf', $app->id));

        // Verify Option A rendered HTML view directly: P1 centered digital signature (no supervisor QR), P2 blank, Student Materai 10.000, security footer
        $renderedHtml = view('thesis_defenses.form_pdf', [
            'application' => $app->load(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'wave']),
            'formData' => $app->getResolvedFormData(),
            'thesis' => $app->thesis,
            'p1' => $setup['p1'],
            'p2' => $setup['p2'],
            'p1Signature' => $setup['p1']->decrypted_signature,
            'p2Signature' => null,
        ])->render();

        $this->assertStringContainsString('FORMULIR PENDAFTARAN SIDANG SKRIPSI/TUGAS AKHIR', $renderedHtml);
        $this->assertStringContainsString('B.&nbsp;&nbsp;DATA KESIAPAN SIDANG', $renderedHtml);
        $this->assertStringContainsString('BUDI SANTOSO', $renderedHtml);
        $this->assertStringContainsString('SISTEM INFORMASI / S1', $renderedHtml);
        $this->assertStringContainsString('Materai<br>10.000', $renderedHtml);
        $this->assertStringContainsString('alt="TTD Pembimbing 1"', $renderedHtml);
        $this->assertStringNotContainsString('alt="QR Verifikasi Pembimbing 1"', $renderedHtml);
        $this->assertStringNotContainsString('alt="TTD Pembimbing 2"', $renderedHtml);
        $this->assertStringNotContainsString('alt="QR Verifikasi Pembimbing 2"', $renderedHtml);
        $this->assertStringContainsString('SIBIMA FASILKOM UNSUB', $renderedHtml);
        $this->assertStringContainsString('Dokumen Pendaftaran SIDANG', $renderedHtml);
        $this->assertStringContainsString($app->getIntegrityCode(), $renderedHtml);
        $this->assertStringContainsString('alt="QR Verifikasi Keaslian Dokumen"', $renderedHtml);

        // Public verification page (unauthenticated BAAK QR scan)
        $verifyRes = $this->get(route('document.verify', $app->verification_token));
        $verifyRes->assertStatus(200);
        $verifyRes->assertSee('Dokumen Resmi Terverifikasi');
        $verifyRes->assertSee('Formulir Pendaftaran Sidang Skripsi');
        $verifyRes->assertSee('Budi Santoso');
        $verifyRes->assertSee('D1A220456');
        $verifyRes->assertSee($app->getIntegrityCode());
        $verifyRes->assertSee('ACC Sidang Terverifikasi');

        $invalidVerifyRes = $this->get(route('document.verify', 'token-sidang-palsu'));
        $invalidVerifyRes->assertStatus(200);
        $invalidVerifyRes->assertSee('Dokumen Tidak Valid');

        // Student owner can download PDF (attachment) and admin can preview (inline) — verify valid single-page PDF
        $pdfDownload = $this->actingAs($freshStudent)->get(route('thesis-defense-applications.form-pdf', $app->id));
        $pdfDownload->assertStatus(200);
        $this->assertStringContainsString('application/pdf', (string) $pdfDownload->headers->get('content-type'));
        $pdfBytes = $pdfDownload->getContent();
        $this->assertStringStartsWith('%PDF-', $pdfBytes);
        $this->assertStringContainsString('/Count 1', $pdfBytes);

        $pdfInline = $this->actingAs($admin)->get(route('thesis-defense-applications.form-pdf', ['application' => $app->id, 'inline' => 1]));
        $pdfInline->assertStatus(200);
        $this->assertStringContainsString('inline', (string) $pdfInline->headers->get('content-disposition'));
        $this->assertStringStartsWith('%PDF-', $pdfInline->getContent());

        // Supervising dosen allowed, unrelated student forbidden
        $this->actingAs($setup['p1'])->get(route('thesis-defense-applications.form-pdf', $app->id))->assertStatus(200);
        $this->actingAs($otherStudent)->get(route('thesis-defense-applications.form-pdf', $app->id))->assertStatus(403);

        // Admin index shows Formulir (PDF) link, digital form payload & reactive validation modal helpers
        $adminRes = $this->actingAs($admin)->get(route('thesis-defense-applications.index', ['wave_id' => $setup['wave']->id]));
        $adminRes->assertStatus(200);
        $adminRes->assertSee('Formulir (PDF)');
        $adminRes->assertSee('has_digital_form');
        $adminRes->assertSee('openValidationModal');
        $adminRes->assertSee('rejectedReviewCount');

        // Admin ZIP includes auto-generated 1_Formulir_Pendaftaran.pdf
        $zipRes = $this->actingAs($admin)->get(route('thesis-defense-applications.download-zip', $app->id));
        $zipRes->assertStatus(200);
        $zipPath = $zipRes->baseResponse->getFile()->getPathname();
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($zipPath) === true);
        $pdfEntry = $zip->getFromName('1_Formulir_Pendaftaran.pdf');
        $zip->close();
        $this->assertNotFalse($pdfEntry);
        $this->assertStringStartsWith('%PDF-', $pdfEntry);
    }

    public function test_rejection_resubmission_handles_drive_file_and_digital_form_separately(): void
    {
        $setup = $this->createEligibleSetup(false, false);

        $app = ThesisDefenseApplication::create(array_merge($this->sampleDriveFiles(), [
            'thesis_id' => $setup['thesis']->id,
            'wave_id' => $setup['wave']->id,
            'status' => 'rejected',
            'file_pembayaran' => 'https://drive.google.com/file/d/pay_old',
            'form_data' => [
                'full_name' => $setup['student']->name,
                'birth_place' => 'Subang',
                'birth_date' => '2003-08-17',
                'npm' => $setup['student']->identifier,
                'study_program' => 'Sistem Informasi / S1',
                'semester_number' => 8,
                'academic_year' => 'Ganjil 2026/2027',
                'address' => 'Alamat Lama',
                'postal_code' => '41211',
                'home_phone' => '',
                'email' => $setup['student']->email,
                'phone_number' => '081277665544',
                'thesis_title' => $setup['thesis']->title,
            ],
            'statement_agreed_at' => now()->subDay(),
            'file_reviews' => [
                'file_pembayaran' => ['status' => 'rejected', 'note' => 'Bukti transfer buram'],
            ],
        ]));

        // Case 1: Only file_pembayaran rejected — student resubmits ONLY file_pembayaran without re-sending biodata
        $res1 = $this->actingAs($setup['student'])->post(route('thesis-defense-applications.store'), [
            'file_pembayaran' => 'https://drive.google.com/file/d/pay_fixed',
        ]);
        $res1->assertRedirect(route('thesis-defense-applications.index'));
        $res1->assertSessionHasNoErrors();

        $app->refresh();
        $this->assertEquals('pending', $app->status);
        $this->assertEquals('https://drive.google.com/file/d/pay_fixed', $app->file_pembayaran);
        $this->assertEquals('Alamat Lama', $app->form_data['address']);

        // Case 2: Admin rejects file_formulir — student updates digital biodata and clears rejection
        $app->update([
            'status' => 'rejected',
            'file_reviews' => [
                'file_formulir' => ['status' => 'rejected', 'note' => 'Mohon lengkapi alamat dengan RT/RW'],
            ],
        ]);

        $res2 = $this->actingAs($setup['student'])->post(route('thesis-defense-applications.store'), [
            'birth_place' => 'Subang',
            'birth_date' => '2003-08-17',
            'semester_number' => 8,
            'academic_year' => 'Ganjil 2026/2027',
            'address' => 'Jl. R.A Kartini KM 3 RT 02 RW 05 Subang',
            'postal_code' => '41211',
            'home_phone' => '',
            'email' => $setup['student']->email,
            'phone_number' => '081277665544',
            'statement_agreed' => '1',
        ]);
        $res2->assertRedirect(route('thesis-defense-applications.index'));
        $res2->assertSessionHasNoErrors();

        $app->refresh();
        $this->assertEquals('pending', $app->status);
        $this->assertEquals('Jl. R.A Kartini KM 3 RT 02 RW 05 Subang', $app->form_data['address']);
        $this->assertArrayNotHasKey('file_formulir', $app->file_reviews ?? []);
    }
}

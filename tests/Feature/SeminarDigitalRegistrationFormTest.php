<?php

namespace Tests\Feature;

use App\Models\SeminarApplication;
use App\Models\Thesis;
use App\Models\User;
use App\Models\Wave;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;
use ZipArchive;

class SeminarDigitalRegistrationFormTest extends TestCase
{
    use RefreshDatabase;

    private function createEligibleSetup(bool $p1HasSignature = false, bool $p2HasSignature = false): array
    {
        // Minimal 1x1 transparent PNG data URI for signature test
        $samplePngDataUri = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

        $p1 = User::factory()->create([
            'role' => 'dosen',
            'name' => 'Dr. Pembimbing Satu, M.Kom',
            'identifier' => '0411223344',
            'signature' => $p1HasSignature ? Crypt::encrypt($samplePngDataUri) : null,
            'signature_token' => $p1HasSignature ? 'sig-token-p1-valid' : null,
        ]);

        $p2 = User::factory()->create([
            'role' => 'dosen',
            'name' => 'Ir. Pembimbing Dua, M.T',
            'identifier' => '0455667788',
            'signature' => $p2HasSignature ? Crypt::encrypt($samplePngDataUri) : null,
            'signature_token' => $p2HasSignature ? 'sig-token-p2-valid' : null,
        ]);

        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Rina Kartika',
            'identifier' => 'D1A220123',
            'email' => 'rina.kartika@student.unsub.ac.id',
            'entry_year' => 2022,
            'phone_number' => '081299887766',
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang 1 Seminar 2026',
            'is_active' => true,
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(10),
        ]);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Rancang Bangun Sistem Informasi Akademik Berbasis Web',
            'abstract' => 'Abstrak proposal penelitian.',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'acc_up_p1' => true,
            'acc_up_p2' => true,
            'status' => 'active',
        ]);

        return compact('p1', 'p2', 'student', 'wave', 'thesis');
    }

    public function test_student_sees_digital_registration_form_without_file_formulir_upload(): void
    {
        $setup = $this->createEligibleSetup(true, false);

        $response = $this->actingAs($setup['student'])->get(route('seminar-applications.index'));

        $response->assertStatus(200);
        $response->assertSee('Formulir Pendaftaran Seminar Digital');
        $response->assertSee('Sistem Informasi / S1');
        $response->assertSee('Rina Kartika');
        $response->assertSee('D1A220123');
        $response->assertSee('Rancang Bangun Sistem Informasi Akademik Berbasis Web');
        $response->assertSee('Dr. Pembimbing Satu, M.Kom');
        $response->assertSee('Ir. Pembimbing Dua, M.T');
        $response->assertSee('Kosong + Kotak Materai 10.000');
        $response->assertSee('4 Berkas Google Drive');
        $response->assertSee('openBaak');
        $response->assertSee('handleSubmit');
        $response->assertSee('sibima_seminar_draft_');
        $response->assertSee('driveLinkStatus');
        $response->assertDontSee('name="file_formulir"', false);
    }

    public function test_student_submits_digital_form_syncs_biodata_and_can_download_official_pdf(): void
    {
        $setup = $this->createEligibleSetup(true, false);
        $admin = User::factory()->create(['role' => 'admin']);
        $otherStudent = User::factory()->create(['role' => 'mahasiswa']);

        $payload = [
            'birth_place' => 'Subang',
            'birth_date' => '2004-03-20',
            'study_program' => 'Sistem Informasi / S1',
            'semester_number' => 8,
            'academic_year' => 'Ganjil 2026/2027',
            'address' => 'Jl. R.A Kartini KM 3, Kel. Pasirkareumbi, Subang',
            'postal_code' => '41211',
            'home_phone' => '0260411415',
            'email' => 'rina.kartika@student.unsub.ac.id',
            'phone_number' => '081299887766',
            'statement_agreed' => '1',
            'file_pembayaran' => 'https://drive.google.com/file/d/pay123/view',
            'file_acc_pembimbing' => 'https://drive.google.com/file/d/acc123/view',
            'file_kartu_bimbingan' => 'https://drive.google.com/file/d/log123/view',
            'file_skripsi' => 'https://drive.google.com/file/d/draft123/view',
        ];

        $storeRes = $this->actingAs($setup['student'])->post(route('seminar-applications.store'), $payload);
        $storeRes->assertRedirect(route('seminar-applications.index'));
        $storeRes->assertSessionHasNoErrors();

        $app = SeminarApplication::where('thesis_id', $setup['thesis']->id)->first();
        $this->assertNotNull($app);
        $this->assertTrue($app->hasDigitalForm());
        $this->assertNotNull($app->statement_agreed_at);
        $this->assertNotNull($app->verification_token);
        $this->assertNull($app->file_formulir);
        $this->assertEquals('Subang', $app->form_data['birth_place']);
        $this->assertEquals('Sistem Informasi / S1', $app->form_data['study_program']);

        // Biodata synced back to User profile for reuse in future Sidang registration
        $freshStudent = $setup['student']->fresh();
        $this->assertEquals('Subang', $freshStudent->birth_place);
        $this->assertEquals('2004-03-20', $freshStudent->birth_date->format('Y-m-d'));
        $this->assertEquals('Sistem Informasi / S1', $freshStudent->study_program);
        $this->assertEquals('41211', $freshStudent->postal_code);
        $this->assertEquals('0260411415', $freshStudent->home_phone);

        // Status page shows Print PDF action, clean Indonesian status title, and student self-cancel while pending
        $statusRes = $this->actingAs($freshStudent)->get(route('seminar-applications.index'));
        $statusRes->assertStatus(200);
        $statusRes->assertSee('Status Pengajuan Seminar');
        $statusRes->assertDontSee('Status Pengajuan: PENDING');
        $statusRes->assertSee('Batalkan Pengajuan');
        $statusRes->assertSee(route('seminar-applications.destroy', $app->id));
        $statusRes->assertSee('Cetak Formulir (PDF)');
        $statusRes->assertSee(route('seminar-applications.form-pdf', $app->id));

        // Verify Option A rendered HTML view directly: P1 has centered digital signature (no supervisor QR), P2 left blank, Student has Materai 10.000 box
        $renderedHtml = view('seminars.form_pdf', [
            'application' => $app->load(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'wave']),
            'formData' => $app->getResolvedFormData(),
            'thesis' => $app->thesis,
            'p1' => $setup['p1'],
            'p2' => $setup['p2'],
            'p1Signature' => $setup['p1']->decrypted_signature,
            'p2Signature' => null,
        ])->render();

        $this->assertStringContainsString('FORMULIR PENDAFTARAN SEMINAR SKRIPSI/TUGAS AKHIR', $renderedHtml);
        $this->assertStringContainsString('RINA KARTIKA', $renderedHtml);
        $this->assertStringContainsString('SISTEM INFORMASI / S1', $renderedHtml);
        $this->assertStringContainsString('Materai<br>10.000', $renderedHtml);
        $this->assertStringContainsString('alt="TTD Pembimbing 1"', $renderedHtml);
        $this->assertStringNotContainsString('alt="QR Verifikasi Pembimbing 1"', $renderedHtml);
        $this->assertStringNotContainsString('alt="TTD Pembimbing 2"', $renderedHtml);
        $this->assertStringNotContainsString('alt="QR Verifikasi Pembimbing 2"', $renderedHtml);
        $this->assertStringContainsString('SIBIMA FASILKOM UNSUB', $renderedHtml);
        $this->assertStringContainsString($app->getIntegrityCode(), $renderedHtml);
        $this->assertStringContainsString('alt="QR Verifikasi Keaslian Dokumen"', $renderedHtml);

        // Public verification page (unauthenticated BAAK QR scan) shows verified details & matching integrity code
        $verifyRes = $this->get(route('document.verify', $app->verification_token));
        $verifyRes->assertStatus(200);
        $verifyRes->assertSee('Dokumen Resmi Terverifikasi');
        $verifyRes->assertSee('Formulir Pendaftaran Seminar');
        $verifyRes->assertSee('Rina Kartika');
        $verifyRes->assertSee('D1A220123');
        $verifyRes->assertSee($app->getIntegrityCode());
        $verifyRes->assertSee('ACC UP Terverifikasi');

        $invalidVerifyRes = $this->get(route('document.verify', 'token-palsu-tidak-ada'));
        $invalidVerifyRes->assertStatus(200);
        $invalidVerifyRes->assertSee('Dokumen Tidak Valid');

        // Student owner can download PDF (attachment) and preview PDF (inline) — verify valid single-page PDF
        $pdfDownload = $this->actingAs($freshStudent)->get(route('seminar-applications.form-pdf', $app->id));
        $pdfDownload->assertStatus(200);
        $this->assertStringContainsString('application/pdf', (string) $pdfDownload->headers->get('content-type'));
        $pdfBytes = $pdfDownload->getContent();
        $this->assertStringStartsWith('%PDF-', $pdfBytes);
        $this->assertStringContainsString('/Count 1', $pdfBytes);

        $pdfInline = $this->actingAs($admin)->get(route('seminar-applications.form-pdf', ['application' => $app->id, 'inline' => 1]));
        $pdfInline->assertStatus(200);
        $this->assertStringContainsString('inline', (string) $pdfInline->headers->get('content-disposition'));
        $this->assertStringStartsWith('%PDF-', $pdfInline->getContent());

        // Supervising dosen allowed, unrelated student forbidden
        $this->actingAs($setup['p1'])->get(route('seminar-applications.form-pdf', $app->id))->assertStatus(200);
        $this->actingAs($otherStudent)->get(route('seminar-applications.form-pdf', $app->id))->assertStatus(403);

        // Admin index shows Formulir (PDF) link & reactive validation modal helpers
        $adminRes = $this->actingAs($admin)->get(route('seminar-applications.index', ['wave_id' => $setup['wave']->id]));
        $adminRes->assertStatus(200);
        $adminRes->assertSee('Formulir (PDF)');
        $adminRes->assertSee('has_digital_form');
        $adminRes->assertSee('openValidationModal');
        $adminRes->assertSee('rejectedReviewCount');

        // Admin ZIP includes auto-generated 5_Formulir_Seminar.pdf
        $zipRes = $this->actingAs($admin)->get(route('seminar-applications.download-zip', $app->id));
        $zipRes->assertStatus(200);
        $zipPath = $zipRes->baseResponse->getFile()->getPathname();
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($zipPath) === true);
        $pdfEntry = $zip->getFromName('5_Formulir_Seminar.pdf');
        $zip->close();
        $this->assertNotFalse($pdfEntry);
        $this->assertStringStartsWith('%PDF-', $pdfEntry);
    }

    public function test_rejection_resubmission_handles_drive_file_and_digital_form_separately(): void
    {
        $setup = $this->createEligibleSetup(false, false);

        $app = SeminarApplication::create([
            'thesis_id' => $setup['thesis']->id,
            'wave_id' => $setup['wave']->id,
            'status' => 'rejected',
            'file_acc_pembimbing' => 'https://drive.google.com/file/d/acc1',
            'file_pembayaran' => 'https://drive.google.com/file/d/pay_old',
            'file_kartu_bimbingan' => 'https://drive.google.com/file/d/log1',
            'file_skripsi' => 'https://drive.google.com/file/d/draft1',
            'form_data' => [
                'full_name' => $setup['student']->name,
                'birth_place' => 'Subang',
                'birth_date' => '2004-03-20',
                'npm' => $setup['student']->identifier,
                'study_program' => 'Sistem Informasi / S1',
                'semester_number' => 8,
                'academic_year' => 'Ganjil 2026/2027',
                'address' => 'Alamat Lama',
                'postal_code' => '41211',
                'home_phone' => '',
                'email' => $setup['student']->email,
                'phone_number' => '081299887766',
                'thesis_title' => $setup['thesis']->title,
            ],
            'statement_agreed_at' => now()->subDay(),
            'file_reviews' => [
                'file_pembayaran' => ['status' => 'rejected', 'note' => 'Bukti transfer buram'],
            ],
        ]);

        // Case 1: Only file_pembayaran rejected — student resubmits ONLY file_pembayaran without re-sending biodata
        $res1 = $this->actingAs($setup['student'])->post(route('seminar-applications.store'), [
            'file_pembayaran' => 'https://drive.google.com/file/d/pay_fixed',
        ]);
        $res1->assertRedirect(route('seminar-applications.index'));
        $res1->assertSessionHasNoErrors();

        $app->refresh();
        $this->assertEquals('pending', $app->status);
        $this->assertEquals('https://drive.google.com/file/d/pay_fixed', $app->file_pembayaran);
        $this->assertEquals('Alamat Lama', $app->form_data['address']);

        // Case 2: Admin rejects file_formulir — student updates biodata and clears rejection
        $app->update([
            'status' => 'rejected',
            'file_reviews' => [
                'file_formulir' => ['status' => 'rejected', 'note' => 'Mohon lengkapi alamat dengan RT/RW'],
            ],
        ]);

        $res2 = $this->actingAs($setup['student'])->post(route('seminar-applications.store'), [
            'birth_place' => 'Subang',
            'birth_date' => '2004-03-20',
            'semester_number' => 8,
            'academic_year' => 'Ganjil 2026/2027',
            'address' => 'Jl. R.A Kartini KM 3 RT 02 RW 05 Subang',
            'postal_code' => '41211',
            'home_phone' => '',
            'email' => $setup['student']->email,
            'phone_number' => '081299887766',
            'statement_agreed' => '1',
        ]);
        $res2->assertRedirect(route('seminar-applications.index'));
        $res2->assertSessionHasNoErrors();

        $app->refresh();
        $this->assertEquals('pending', $app->status);
        $this->assertEquals('Jl. R.A Kartini KM 3 RT 02 RW 05 Subang', $app->form_data['address']);
        $this->assertArrayNotHasKey('file_formulir', $app->file_reviews ?? []);
    }
}

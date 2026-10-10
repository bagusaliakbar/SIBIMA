<?php

namespace Tests\Feature;

use App\Models\SeminarApplication;
use App\Models\Thesis;
use App\Models\ThesisDefenseApplication;
use App\Models\User;
use App\Models\Wave;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicationPreviewFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_kaprodi_can_see_preview_button_and_modal_data_on_seminar_applications()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kaprodi = User::factory()->create(['role' => 'kaprodi']);
        $p1 = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Pembimbing Satu']);
        $p2 = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Pembimbing Dua']);

        $wave = Wave::create([
            'name' => 'Gelombang Preview Seminar',
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(5),
            'is_active' => true,
        ]);

        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Budi Santoso Preview',
            'identifier' => 'D1A220999',
            'phone_number' => '081234567890',
        ]);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Rancang Bangun Preview Seminar System',
            'abstract' => 'Abstrak sistem preview',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
        ]);

        $seminarApp = SeminarApplication::create([
            'thesis_id' => $thesis->id,
            'wave_id' => $wave->id,
            'status' => 'pending',
            'file_acc_pembimbing' => 'SeminarApplication_files/test_acc.pdf',
            'file_pembayaran' => 'SeminarApplication_files/test_pay.pdf',
            'file_kartu_bimbingan' => 'SeminarApplication_files/test_card.pdf',
            'file_skripsi' => 'https://drive.google.com/file/d/123456789/view?usp=sharing',
            'file_formulir' => 'SeminarApplication_files/test_form.pdf',
        ]);

        foreach ([$admin, $kaprodi] as $user) {
            $response = $this->actingAs($user)->get(route('seminar-applications.index', ['wave_id' => $wave->id]));

            $response->assertStatus(200);
            $response->assertSee('Preview');
            $response->assertSee('openPreviewModal');
            $response->assertSee('openPreview');
            $response->assertSee('Budi Santoso Preview');
            $response->assertSee('Rancang Bangun Preview Seminar System');
            $response->assertSee('Dr. Pembimbing Satu');
            $response->assertSee('formatPreviewUrl');
        }
    }

    public function test_admin_and_kaprodi_can_see_preview_button_and_category_tabs_on_thesis_defense_applications()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kaprodi = User::factory()->create(['role' => 'kaprodi']);
        $p1 = User::factory()->create(['role' => 'dosen', 'name' => 'Prof. Penguji Satu']);
        $p2 = User::factory()->create(['role' => 'dosen', 'name' => 'Prof. Penguji Dua']);

        $wave = Wave::create([
            'name' => 'Gelombang Preview Sidang',
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(5),
            'is_active' => true,
        ]);

        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Ani Lestari Sidang',
            'identifier' => 'D1A220888',
            'phone_number' => '089876543210',
        ]);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Implementasi In-App Preview Pada Ujian Sidang Skripsi',
            'abstract' => 'Abstrak sidang skripsi',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
        ]);

        $defenseApp = ThesisDefenseApplication::create([
            'thesis_id' => $thesis->id,
            'wave_id' => $wave->id,
            'status' => 'pending',
            'file_formulir' => 'ThesisDefenseApplication_files/test_form.pdf',
            'file_transkrip' => 'ThesisDefenseApplication_files/test_transkrip.pdf',
            'file_acc_pembimbing' => 'ThesisDefenseApplication_files/test_acc.pdf',
            'file_logbook' => 'ThesisDefenseApplication_files/test_logbook.pdf',
            'file_pembayaran' => 'ThesisDefenseApplication_files/test_pembayaran.pdf',
            'file_skripsi' => 'ThesisDefenseApplication_files/test_skripsi.pdf',
            'file_ktm' => 'ThesisDefenseApplication_files/test_ktm.pdf',
            'file_pkkmb_univ' => 'ThesisDefenseApplication_files/test_pkkmb_univ.pdf',
            'file_pkkmb_fak' => 'ThesisDefenseApplication_files/test_pkkmb_fak.pdf',
            'file_makrab' => 'ThesisDefenseApplication_files/test_makrab.pdf',
            'file_cisco' => 'ThesisDefenseApplication_files/test_cisco.pdf',
            'file_workshop' => 'ThesisDefenseApplication_files/test_workshop.pdf',
            'file_organisasi' => 'ThesisDefenseApplication_files/test_organisasi.pdf',
            'file_toefl' => 'ThesisDefenseApplication_files/test_toefl.pdf',
            'file_kewirausahaan' => 'ThesisDefenseApplication_files/test_kewirausahaan.pdf',
            'file_tahsin' => 'ThesisDefenseApplication_files/test_tahsin.pdf',
            'file_komputer' => 'ThesisDefenseApplication_files/test_komputer.pdf',
            'file_perpus_pinjam' => 'ThesisDefenseApplication_files/test_perpus_pinjam.pdf',
            'file_perpus_sumbang' => 'ThesisDefenseApplication_files/test_perpus_sumbang.pdf',
            'file_ijazah' => 'ThesisDefenseApplication_files/test_ijazah.pdf',
        ]);

        foreach ([$admin, $kaprodi] as $user) {
            $response = $this->actingAs($user)->get(route('thesis-defense-applications.index', ['wave_id' => $wave->id]));

            $response->assertStatus(200);
            $response->assertSee('Preview');
            $response->assertSee('openPreviewModal');
            $response->assertSee('openPreview');
            $response->assertSee('Dokumen Utama');
            $response->assertSee('Kemahasiswaan');
            $response->assertSee('Sertifikat');
            $response->assertSee('Perpus & Ijazah', false);
            $response->assertSee('Ani Lestari Sidang');
            $response->assertSee('Implementasi In-App Preview Pada Ujian Sidang Skripsi');
        }
    }

    public function test_private_download_endpoint_supports_inline_preview_for_authorized_users()
    {
        $disk = Storage::disk(config('filesystems.default'));
        $filePath = 'SeminarApplication_files/test_preview_doc.pdf';
        $disk->put($filePath, '%PDF-1.4 test document content');

        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'identifier' => 'D1A220111']);
        $otherStudent = User::factory()->create(['role' => 'mahasiswa', 'identifier' => 'D1A220222']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Skripsi Test Download Inline',
            'status' => 'active',
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang Test Inline',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'is_active' => true,
        ]);

        $app = SeminarApplication::create([
            'thesis_id' => $thesis->id,
            'wave_id' => $wave->id,
            'status' => 'pending',
            'file_acc_pembimbing' => $filePath,
            'file_pembayaran' => 'SeminarApplication_files/pay.pdf',
            'file_kartu_bimbingan' => 'SeminarApplication_files/card.pdf',
            'file_skripsi' => 'SeminarApplication_files/skripsi.pdf',
            'file_formulir' => 'SeminarApplication_files/form.pdf',
        ]);

        try {
            // 1. Admin with inline=1 should get inline Content-Disposition
            $responseInline = $this->actingAs($admin)->get(route('download.private', [
                'path' => $filePath,
                'inline' => 1,
            ]));
            $responseInline->assertStatus(200);
            $contentDisposition = $responseInline->headers->get('content-disposition');
            $this->assertNotNull($contentDisposition);
            $this->assertStringStartsWith('inline', $contentDisposition);

            // 2. Admin without inline parameter should get attachment Content-Disposition
            $responseDownload = $this->actingAs($admin)->get(route('download.private', [
                'path' => $filePath,
            ]));
            $responseDownload->assertStatus(200);
            $downloadDisposition = $responseDownload->headers->get('content-disposition');
            $this->assertNotNull($downloadDisposition);
            $this->assertStringContainsString('attachment', $downloadDisposition);

            // 3. Thesis owner student with inline=1 can preview
            $responseStudent = $this->actingAs($student)->get(route('download.private', [
                'path' => $filePath,
                'inline' => 1,
            ]));
            $responseStudent->assertStatus(200);
            $this->assertStringStartsWith('inline', $responseStudent->headers->get('content-disposition'));

            // 4. Unauthorized student gets 403
            $responseForbidden = $this->actingAs($otherStudent)->get(route('download.private', [
                'path' => $filePath,
                'inline' => 1,
            ]));
            $responseForbidden->assertStatus(403);
        } finally {
            // Cleanup test file
            $disk->delete($filePath);
        }
    }
}

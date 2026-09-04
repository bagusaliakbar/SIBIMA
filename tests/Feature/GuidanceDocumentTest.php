<?php

namespace Tests\Feature;

use App\Models\GuidanceDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GuidanceDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(config('filesystems.default'));
    }

    public function test_authenticated_users_can_view_guidance_documents_catalog(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $activeDoc = GuidanceDocument::create([
            'title' => 'Buku Pedoman Skripsi 2026',
            'description' => 'Pedoman resmi penulisan skripsi.',
            'category' => 'panduan_skripsi',
            'file_path' => 'guidance_documents/sample.pdf',
            'original_name' => 'sample.pdf',
            'file_size' => 10240,
            'file_extension' => 'pdf',
            'download_count' => 0,
            'is_active' => true,
        ]);

        $inactiveDoc = GuidanceDocument::create([
            'title' => 'Draf Panduan Rahasia',
            'description' => 'Draf panduan belum rilis.',
            'category' => 'panduan_skripsi',
            'file_path' => 'guidance_documents/draft.pdf',
            'original_name' => 'draft.pdf',
            'file_size' => 5120,
            'file_extension' => 'pdf',
            'download_count' => 0,
            'is_active' => false,
        ]);

        $response = $this->actingAs($student)->get(route('guidance-documents.index'));

        $response->assertStatus(200);
        $response->assertSee('Buku Pedoman Skripsi 2026');
        $response->assertDontSee('Draf Panduan Rahasia');
        $response->assertDontSee('Unggah Dokumen');
    }

    public function test_admin_and_kaprodi_can_see_active_and_inactive_documents_and_upload_button(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        GuidanceDocument::create([
            'title' => 'Draf Panduan Internal',
            'category' => 'pedoman_bimbingan',
            'file_path' => 'guidance_documents/draft.pdf',
            'original_name' => 'draft.pdf',
            'file_size' => 5120,
            'file_extension' => 'pdf',
            'download_count' => 0,
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('guidance-documents.index'));

        $response->assertStatus(200);
        $response->assertSee('Draf Panduan Internal');
        $response->assertSee('Unggah Dokumen');
    }

    public function test_admin_can_upload_guidance_document(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $fakeFile = UploadedFile::fake()->create('template_skripsi.docx', 500, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($admin)->post(route('guidance-documents.store'), [
            'title' => 'Template Naskah Skripsi Word',
            'description' => 'Format template skripsi standar prodi.',
            'category' => 'format_template',
            'document_file' => $fakeFile,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('guidance-documents.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('guidance_documents', [
            'title' => 'Template Naskah Skripsi Word',
            'category' => 'format_template',
            'original_name' => 'template_skripsi.docx',
            'file_extension' => 'docx',
            'is_active' => true,
            'uploaded_by' => $admin->id,
        ]);

        $doc = GuidanceDocument::where('title', 'Template Naskah Skripsi Word')->first();
        Storage::disk(config('filesystems.default'))->assertExists($doc->file_path);
    }

    public function test_student_and_lecturer_cannot_upload_guidance_document(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $dosen = User::factory()->create(['role' => 'dosen']);
        $fakeFile = UploadedFile::fake()->create('sample.pdf', 100, 'application/pdf');

        $payload = [
            'title' => 'Dokumen Liar',
            'category' => 'panduan_skripsi',
            'document_file' => $fakeFile,
        ];

        $this->actingAs($student)->post(route('guidance-documents.store'), $payload)->assertStatus(403);
        $this->actingAs($dosen)->post(route('guidance-documents.store'), $payload)->assertStatus(403);

        $this->assertDatabaseMissing('guidance_documents', ['title' => 'Dokumen Liar']);
    }

    public function test_admin_can_update_and_toggle_status_of_guidance_document(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $disk = config('filesystems.default');

        $filePath = 'guidance_documents/old_doc.pdf';
        Storage::disk($disk)->put($filePath, 'sample content');

        $doc = GuidanceDocument::create([
            'title' => 'Panduan Lama',
            'description' => 'Deskripsi lama',
            'category' => 'panduan_skripsi',
            'file_path' => $filePath,
            'original_name' => 'old_doc.pdf',
            'file_size' => 1024,
            'file_extension' => 'pdf',
            'download_count' => 0,
            'is_active' => true,
        ]);

        // Update title & category
        $response = $this->actingAs($admin)->put(route('guidance-documents.update', $doc->id), [
            'title' => 'Panduan Baru Terupdate',
            'description' => 'Deskripsi terupdate',
            'category' => 'pedoman_bimbingan',
            'is_active' => false,
        ]);

        $response->assertRedirect(route('guidance-documents.index'));
        $this->assertDatabaseHas('guidance_documents', [
            'id' => $doc->id,
            'title' => 'Panduan Baru Terupdate',
            'category' => 'pedoman_bimbingan',
            'is_active' => false,
        ]);

        // Toggle status
        $this->actingAs($admin)->patch(route('guidance-documents.toggle', $doc->id));
        $this->assertTrue($doc->fresh()->is_active);
    }

    public function test_admin_can_delete_guidance_document(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $disk = config('filesystems.default');

        $filePath = 'guidance_documents/to_delete.pdf';
        Storage::disk($disk)->put($filePath, 'sample content');

        $doc = GuidanceDocument::create([
            'title' => 'Dokumen Akan Dihapus',
            'category' => 'lainnya',
            'file_path' => $filePath,
            'original_name' => 'to_delete.pdf',
            'file_size' => 1024,
            'file_extension' => 'pdf',
            'download_count' => 0,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('guidance-documents.destroy', $doc->id));

        $response->assertRedirect(route('guidance-documents.index'));
        $this->assertDatabaseMissing('guidance_documents', ['id' => $doc->id]);
        Storage::disk($disk)->assertMissing($filePath);
    }

    public function test_download_increments_download_counter(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $disk = config('filesystems.default');

        $filePath = 'guidance_documents/buku_pedoman.pdf';
        Storage::disk($disk)->put($filePath, 'pdf dummy binary');

        $doc = GuidanceDocument::create([
            'title' => 'Buku Pedoman',
            'category' => 'panduan_skripsi',
            'file_path' => $filePath,
            'original_name' => 'buku_pedoman.pdf',
            'file_size' => 1024,
            'file_extension' => 'pdf',
            'download_count' => 0,
            'is_active' => true,
        ]);

        $response = $this->actingAs($student)->get(route('guidance-documents.download', $doc->id));

        $response->assertStatus(200);
        $this->assertEquals(1, $doc->fresh()->download_count);
    }

    public function test_student_cannot_download_inactive_document(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $disk = config('filesystems.default');

        $filePath = 'guidance_documents/secret.pdf';
        Storage::disk($disk)->put($filePath, 'secret');

        $doc = GuidanceDocument::create([
            'title' => 'Dokumen Nonaktif',
            'category' => 'panduan_skripsi',
            'file_path' => $filePath,
            'original_name' => 'secret.pdf',
            'file_size' => 1024,
            'file_extension' => 'pdf',
            'download_count' => 0,
            'is_active' => false,
        ]);

        $response = $this->actingAs($student)->get(route('guidance-documents.download', $doc->id));
        $response->assertStatus(404);
    }

    public function test_pdf_can_be_previewed_inline(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $disk = config('filesystems.default');

        $filePath = 'guidance_documents/view_test.pdf';
        Storage::disk($disk)->put($filePath, '%PDF-1.4 dummy pdf');

        $doc = GuidanceDocument::create([
            'title' => 'Dokumen Preview',
            'category' => 'panduan_skripsi',
            'file_path' => $filePath,
            'original_name' => 'view_test.pdf',
            'file_size' => 1024,
            'file_extension' => 'pdf',
            'download_count' => 0,
            'is_active' => true,
        ]);

        $response = $this->actingAs($student)->get(route('guidance-documents.view', $doc->id));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }
}

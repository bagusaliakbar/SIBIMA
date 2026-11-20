<?php

namespace Tests\Feature;

use App\Models\AdvisorDecree;
use App\Models\Thesis;
use App\Models\User;
use App\Services\BackupService;
use App\Support\UrlSecurity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_cannot_access_advisor_decrees_index_show_or_pdf(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);
        $dosen = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $decree = AdvisorDecree::create([
            'decree_number' => '001/D/FIK-US/XI/2026',
            'title' => 'SK Pembimbing Skripsi',
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'target_type' => 'collective',
            'dosen_id' => $dosen->id,
            'decree_date' => now()->toDateString(),
            'signatory_title' => 'Dekan',
            'signatory_name' => 'Dekan Fasilkom',
            'signatory_identifier' => '0413056812',
            'theses_data' => [],
            'total_students' => 0,
            'verification_token' => 'test-token-12345',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($mahasiswa)->get(route('advisor-decrees.index'))->assertStatus(403);
        $this->actingAs($mahasiswa)->get(route('advisor-decrees.show', $decree))->assertStatus(403);
        $this->actingAs($mahasiswa)->get(route('advisor-decrees.pdf', $decree))->assertStatus(403);

        // Admin still has access
        $this->actingAs($admin)->get(route('advisor-decrees.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('advisor-decrees.show', $decree))->assertStatus(200);
    }

    public function test_dosen_cannot_generate_surat_tugas_for_other_lecturers_students_or_forge_signatory(): void
    {
        $dosenA = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
        $dosenB = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
        $studentA = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);
        $studentB = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);

        $thesisA = Thesis::create([
            'student_id' => $studentA->id,
            'title' => 'Skripsi Mahasiswa A',
            'status' => 'active',
            'pembimbing1_id' => $dosenA->id,
        ]);

        $thesisB = Thesis::create([
            'student_id' => $studentB->id,
            'title' => 'Skripsi Mahasiswa B',
            'status' => 'active',
            'pembimbing1_id' => $dosenB->id,
        ]);

        // Attempt to include another lecturer's student must be blocked with 403
        $this->actingAs($dosenA)
            ->post(route('advisor-decrees.generate-surat-tugas'), [
                'academic_year' => '2026/2027',
                'semester' => 'Ganjil',
                'decree_date' => now()->toDateString(),
                'selected_theses' => [$thesisB->id],
            ])
            ->assertStatus(403);

        // Generating for own student succeeds, but forged custom_decree_number and signatory_name are ignored
        $response = $this->actingAs($dosenA)
            ->post(route('advisor-decrees.generate-surat-tugas'), [
                'academic_year' => '2026/2027',
                'semester' => 'Ganjil',
                'decree_date' => now()->toDateString(),
                'selected_theses' => [$thesisA->id],
                'custom_decree_number' => 'FORGED-999/FAKE',
                'signatory_name' => 'FORGED DEAN NAME',
            ]);

        $response->assertRedirect();
        $decree = AdvisorDecree::latest('id')->first();
        $this->assertNotNull($decree);
        $this->assertNotSame('FORGED-999/FAKE', $decree->decree_number);
        $this->assertNotSame('FORGED DEAN NAME', $decree->signatory_name);
    }

    public function test_mahasiswa_cannot_access_online_users_or_check_dosen_availability(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);
        $dosen = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($mahasiswa)->getJson(route('dashboard.online-users'))->assertStatus(403);
        $this->actingAs($dosen)->getJson(route('dashboard.online-users'))->assertStatus(403);
        $this->actingAs($admin)->getJson(route('dashboard.online-users'))->assertStatus(200);

        $payload = [
            'dosen_ids' => [$dosen->id],
            'date' => '2026-11-25',
            'start_time' => '09:00',
            'end_time' => '10:30',
            'schedule_type' => 'seminar',
        ];

        $this->actingAs($mahasiswa)->postJson(route('check-dosen-availability'), $payload)->assertStatus(403);
        $this->actingAs($dosen)->postJson(route('check-dosen-availability'), $payload)->assertStatus(200);
        $this->actingAs($admin)->postJson(route('check-dosen-availability'), $payload)->assertStatus(200);
    }

    public function test_kaprodi_cannot_restore_upload_restore_or_delete_backups(): void
    {
        $kaprodi = User::factory()->create(['role' => 'kaprodi', 'is_active' => true]);

        $this->actingAs($kaprodi)->get(route('admin.backups.index'))->assertStatus(200);
        $this->actingAs($kaprodi)->post(route('admin.backups.restore', 'dummy.sql'))->assertStatus(403);
        $this->actingAs($kaprodi)->post(route('admin.backups.upload-restore'), [
            'backup_file' => UploadedFile::fake()->create('test.sql', 10),
        ])->assertStatus(403);
        $this->actingAs($kaprodi)->delete(route('admin.backups.destroy', 'dummy.sql'))->assertStatus(403);
    }

    public function test_backup_service_blocks_prepare_trigger_and_executable_mysql_comments(): void
    {
        $service = new class extends BackupService {
            public function check(string $sql): void
            {
                $this->assertSafeBackupStatement($sql);
            }
        };

        $dangerousStatements = [
            "PREPARE stmt FROM 'SELECT 1';",
            'CREATE TRIGGER evil_trig AFTER INSERT ON users FOR EACH ROW BEGIN END;',
            'SELECT /*!50000 1 */ INTO OUTFILE "/tmp/x";',
            'CREATE EVENT evil_ev ON SCHEDULE EVERY 1 MINUTE DO SELECT 1;',
        ];

        foreach ($dangerousStatements as $sql) {
            $blocked = false;
            try {
                $service->check($sql);
            } catch (\RuntimeException $e) {
                $blocked = str_contains($e->getMessage(), 'dilarang oleh kebijakan keamanan');
            }
            $this->assertTrue($blocked, "Expected SQL statement to be blocked: {$sql}");
        }
    }

    public function test_user_json_serialization_hides_sensitive_fields_while_property_access_works(): void
    {
        $user = User::factory()->create([
            'phone_number' => '081234567890',
            'signature_token' => 'secret-sig-token-abc',
            'signature' => 'encrypted-sig-blob',
        ]);

        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
        $this->assertArrayNotHasKey('signature', $array);
        $this->assertArrayNotHasKey('signature_token', $array);
        $this->assertArrayNotHasKey('phone_number', $array);

        // Explicit property access remains unaffected
        $this->assertSame('081234567890', $user->phone_number);
        $this->assertSame('secret-sig-token-abc', $user->signature_token);
    }

    public function test_url_security_blocks_ipv6_loopback_private_ips_and_ssrf_redirect_hops(): void
    {
        $this->assertFalse(UrlSecurity::isSafeExternalUrl('http://[::1]/admin'));
        $this->assertFalse(UrlSecurity::isSafeExternalUrl('http://[fc00::1]/secret'));
        $this->assertFalse(UrlSecurity::isSafeExternalUrl('http://127.0.0.1/admin'));
        $this->assertFalse(UrlSecurity::isSafeExternalUrl('http://169.254.169.254/latest/meta-data'));

        // Public URL that redirects to internal loopback must be blocked by safeHttpGet
        Http::fake([
            'https://93.184.216.34/start' => Http::response('', 302, ['Location' => 'http://127.0.0.1/internal']),
            'http://127.0.0.1/internal' => Http::response('SECRET', 200),
        ]);

        $res = UrlSecurity::safeHttpGet('https://93.184.216.34/start', [], 5, 3);
        $this->assertNull($res);
    }

    public function test_clear_cache_blocks_cross_site_get_csrf_for_admin_cookie(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)
            ->withHeaders([
                'Sec-Fetch-Site' => 'cross-site',
                'Sec-Fetch-Mode' => 'no-cors',
            ])
            ->get('/clear-cache')
            ->assertStatus(403);
    }

    public function test_calendar_events_rejects_invalid_date_query_without_500_error(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);

        $this->actingAs($user)
            ->getJson(route('calendar.events', ['start' => 'not-a-valid-date']))
            ->assertStatus(422);
    }

    public function test_student_cannot_create_thesis_for_another_student_via_student_id_idor(): void
    {
        $attacker = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);
        $victim = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);

        $this->actingAs($attacker)
            ->post(route('theses.store'), [
                'student_id' => $victim->id,
                'title' => 'Sistem Informasi Pengujian Otorisasi Pengajuan Skripsi',
                'abstract' => 'Abstrak pengujian keamanan IDOR pada pengajuan judul skripsi.',
            ])
            ->assertRedirect(route('theses.index'));

        $this->assertDatabaseMissing('theses', ['student_id' => $victim->id]);
        $this->assertDatabaseHas('theses', ['student_id' => $attacker->id]);
    }

    public function test_student_cannot_book_mentoring_session_with_non_advisor_user(): void
    {
        $advisor = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
        $otherDosen = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
        $student = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);

        Thesis::create([
            'student_id' => $student->id,
            'title' => 'Skripsi Bimbingan Mahasiswa Uji',
            'abstract' => 'Abstrak bimbingan',
            'status' => 'active',
            'pembimbing1_id' => $advisor->id,
        ]);

        $this->actingAs($student)
            ->post(route('mentoring-sessions.store'), [
                'dosen_id' => $otherDosen->id,
                'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i'),
                'topic' => 'Percobaan Booking Dosen Bukan Pembimbing',
                'type' => 'offline',
                'location' => 'Ruang Dosen',
            ])
            ->assertSessionHasErrors('dosen_id');

        $this->assertDatabaseCount('mentoring_sessions', 0);
    }

    public function test_bug_report_and_bookmark_reject_javascript_uri_xss(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);

        $this->actingAs($student)
            ->postJson(route('bug-reports.store'), [
                'title' => 'Test XSS Bug Report',
                'category' => 'ui_ux',
                'severity' => 'low',
                'page_url' => 'javascript:alert(document.cookie)',
                'description' => 'Mencoba injeksi javascript URI.',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('page_url');

        $this->actingAs($student)
            ->postJson(route('repositories.bookmarks.toggle'), [
                'journal_identifier' => 'xss-test-001',
                'title' => 'Malicious Bookmark',
                'url' => 'javascript://%0aalert(1)',
                'pdf_url' => 'javascript:alert(2)',
                'doi' => 'javascript:alert(3)',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['url', 'pdf_url', 'doi']);
    }

    public function test_avatar_endpoint_blocks_svg_data_uri_xss(): void
    {
        $viewer = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $target = User::factory()->create([
            'role' => 'mahasiswa',
            'is_active' => true,
            'avatar' => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"/>'),
        ]);

        $response = $this->actingAs($viewer)->get(route('users.avatar', $target));
        $response->assertStatus(200);
        $this->assertStringNotContainsString('onload="alert(1)"', (string) $response->getContent());
    }
}

<?php

namespace Tests\Feature;

use App\Models\Thesis;
use App\Models\User;
use App\Models\Wave;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class DatePickerComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_date_picker_blade_component_renders_with_sunday_red_and_iso_input(): void
    {
        $html = Blade::render(
            '<x-date-picker id="birth_date" name="birth_date" value="2003-05-14" min="1980-01-01" max="2026-11-27" required />'
        );

        $this->assertStringContainsString('sibimaDatePicker', $html);
        $this->assertStringContainsString('x-modelable="value"', $html);
        $this->assertStringContainsString('id="birth_date"', $html);
        $this->assertStringContainsString('name="birth_date"', $html);
        $this->assertStringContainsString('value="2003-05-14"', $html);
        $this->assertStringContainsString('text-rose-600 dark:text-rose-400">Min</span>', $html);
        $this->assertStringContainsString('aria-label="Pilih bulan"', $html);
        $this->assertStringContainsString('aria-label="Pilih tahun"', $html);
        $this->assertStringContainsString('Hari Ini', $html);
        $this->assertStringNotContainsString('type="date"', $html);
    }

    public function test_key_pages_render_custom_date_picker_without_native_date_inputs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $p1 = User::factory()->create(['role' => 'dosen']);
        $p2 = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        Wave::create([
            'name' => 'Gelombang 1 2026',
            'is_active' => true,
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(14),
        ]);

        Thesis::create([
            'student_id' => $student->id,
            'title' => 'Pengembangan Sistem Informasi Akademik',
            'abstract' => 'Abstrak uji coba.',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'acc_up_p1' => true,
            'acc_up_p2' => true,
            'acc_sidang_p1' => true,
            'acc_sidang_p2' => true,
            'status' => 'active',
        ]);

        $studentRoutes = [
            route('seminar-applications.index'),
            route('thesis-defense-applications.index'),
            route('mentoring-sessions.create'),
            route('profile.edit'),
        ];

        foreach ($studentRoutes as $url) {
            $res = $this->actingAs($student)->get($url);
            $res->assertStatus(200);
            $res->assertSee('sibimaDatePicker', false);
            $this->assertDoesNotMatchRegularExpression('/<input\b[^>]*\btype=["\']date["\']/i', $res->getContent(), "Found native date input on {$url}");
        }

        $dosenRoutes = [
            route('seminar-schedules.index'),
            route('thesis-defense-schedules.index'),
        ];

        foreach ($dosenRoutes as $url) {
            $res = $this->actingAs($p1)->get($url);
            $res->assertStatus(200);
            $res->assertSee('sibimaDatePicker', false);
            $this->assertDoesNotMatchRegularExpression('/<input\b[^>]*\btype=["\']date["\']/i', $res->getContent(), "Found native date input on {$url}");
        }

        $adminRoutes = [
            route('waves.index'),
            route('users.create'),
            route('seminar-schedules.create'),
            route('thesis-defense-schedules.create'),
            route('monitoring.weekly'),
            route('monitoring.activity', ['period' => 'custom']),
            route('graduations.index'),
            route('analytics.index'),
        ];

        foreach ($adminRoutes as $url) {
            $res = $this->actingAs($admin)->get($url);
            $res->assertStatus(200);
            $res->assertSee('sibimaDatePicker', false);
            $this->assertDoesNotMatchRegularExpression('/<input\b[^>]*\btype=["\']date["\']/i', $res->getContent(), "Found native date input on {$url}");
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqHelpCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_sibima_faqs_are_seeded_across_all_categories_and_visible_by_role(): void
    {
        $this->assertGreaterThanOrEqual(30, Faq::count());

        foreach (array_keys(Faq::categories()) as $category) {
            $this->assertGreaterThan(0, Faq::where('category', $category)->count(), "Category {$category} must have FAQs.");
        }

        // Public guest can access help center
        $this->get(route('faqs.index'))
            ->assertOk()
            ->assertSee('Pusat Bantuan SIBIMA')
            ->assertSee('Kartu Bimbingan Digital')
            ->assertDontSee('**');

        // Dosen sees dosen-specific FAQs
        $dosen = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
        $this->actingAs($dosen)
            ->get(route('faqs.index'))
            ->assertOk()
            ->assertSee('Live Attendance')
            ->assertSee('Lapor Kendala');
    }

    public function test_formatted_answer_renders_markdown_safely_and_escapes_html(): void
    {
        $faq = new Faq([
            'answer' => 'Gunakan **bold** dan `code` bukan <script>alert(1)</script>',
        ]);

        $html = $faq->formatted_answer;

        $this->assertStringContainsString('<strong', $html);
        $this->assertStringContainsString('<code', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
    }
}

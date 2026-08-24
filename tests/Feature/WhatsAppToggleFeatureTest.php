<?php

namespace Tests\Feature;

use App\Channels\FonnteChannel;
use App\Models\Setting;
use App\Models\User;
use App\Models\WaTemplate;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppToggleFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::setWhatsAppEnabled(true);
    }

    public function test_admin_can_view_wa_templates_page()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('wa-templates.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen WhatsApp Gateway');
        $response->assertSee('MASTER SWITCH: AKTIF');
    }

    public function test_admin_can_toggle_global_master_switch()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue(Setting::isWhatsAppEnabled());

        // Toggle to OFF
        $response = $this->actingAs($admin)->post(route('wa-templates.toggle-global'));
        $response->assertRedirect();
        $this->assertFalse(Setting::isWhatsAppEnabled());

        // Toggle back to ON
        $response = $this->actingAs($admin)->post(route('wa-templates.toggle-global'));
        $response->assertRedirect();
        $this->assertTrue(Setting::isWhatsAppEnabled());
    }

    public function test_admin_can_toggle_individual_template_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $template = WaTemplate::create([
            'code' => 'test_toggle_template',
            'name' => 'Test Toggle Template',
            'category' => 'Bimbingan',
            'content' => 'Halo {nama_mahasiswa}, tes notifikasi.',
            'is_active' => true,
        ]);

        $this->assertTrue($template->is_active);
        $this->assertNotEmpty(WaTemplate::parse('test_toggle_template', ['nama_mahasiswa' => 'Budi']));

        // Toggle to OFF
        $response = $this->actingAs($admin)->post(route('wa-templates.toggle-status', $template));
        $response->assertRedirect();

        $template->refresh();
        $this->assertFalse($template->is_active);

        // When deactivated, WaTemplate::parse returns empty string
        $this->assertSame('', WaTemplate::parse('test_toggle_template', ['nama_mahasiswa' => 'Budi']));

        // Toggle back to ON
        $response = $this->actingAs($admin)->post(route('wa-templates.toggle-status', $template));
        $response->assertRedirect();

        $template->refresh();
        $this->assertTrue($template->is_active);
        $this->assertStringContainsString('Halo Budi', WaTemplate::parse('test_toggle_template', ['nama_mahasiswa' => 'Budi']));
    }

    public function test_whatsapp_service_skips_when_globally_disabled()
    {
        Http::fake();

        // 1. When Global ON, WhatsAppService makes HTTP request
        Setting::setWhatsAppEnabled(true);
        config(['services.whatsapp.token' => 'dummy_token']);

        $service = new WhatsAppService();
        $service->sendMessage('08123456789', 'Test message');
        Http::assertSentCount(1);

        // 2. When Global OFF, WhatsAppService skips immediately without HTTP request
        Setting::setWhatsAppEnabled(false);
        $result = $service->sendMessage('08123456789', 'Test message 2');
        $this->assertFalse($result);
        Http::assertSentCount(1); // Still 1, no new HTTP request sent!
    }

    public function test_fonnte_channel_skips_when_globally_disabled()
    {
        Http::fake();
        config(['services.whatsapp.token' => 'dummy_token']);

        $channel = new FonnteChannel();
        $user = User::factory()->create(['phone_number' => '08123456789']);

        $notification = new class extends \Illuminate\Notifications\Notification {
            public function toFonnte($notifiable) {
                return 'Test message content';
            }
        };

        // 1. Global OFF -> FonnteChannel skips
        Setting::setWhatsAppEnabled(false);
        $channel->send($user, $notification);
        Http::assertSentCount(0);

        // 2. Global ON -> FonnteChannel sends
        Setting::setWhatsAppEnabled(true);
        $channel->send($user, $notification);
        Http::assertSentCount(1);
    }
}

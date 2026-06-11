<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SettingCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/settings');

        $response->assertOk();
        $response->assertViewIs('settings.index');
    }

    public function test_settings_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/settings', [
                'contact_whatsapp' => '081234567890',
                'company_address' => 'Test Address',
                'site_title' => 'Test Title',
            ]);

        $response->assertRedirect(route('settings.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('settings', [
            'key' => 'contact_whatsapp',
            'value' => '081234567890',
        ]);

        $this->assertEquals('Test Title', setting('site_title'));
    }

    public function test_unauthenticated_user_cannot_access_settings(): void
    {
        $response = $this->get('/settings');
        $response->assertRedirect('/login');

        $response = $this->post('/settings', [
            'contact_whatsapp' => '123'
        ]);
        $response->assertRedirect('/login');
    }

    public function test_whatsapp_helper_formats_number_correctly(): void
    {
        $this->assertEquals('6281234567890', format_wa_number('0812-3456-7890'));
        $this->assertEquals('6281234567890', format_wa_number('+62 812 3456 7890'));
    }
}

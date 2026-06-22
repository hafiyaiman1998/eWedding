<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_update_settings_persists_setting_rows(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);

        $response = $this->actingAs($admin)->postJson(route('admin.settings.update'), [
            'max_cards_per_user' => 5,
            'default_card_expiry' => 180,
            'allow_custom_domains' => true,
            'enable_analytics' => true,
            'auto_approve_cards' => false,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('settings', ['key' => 'max_cards_per_user', 'value' => '5']);
        $this->assertDatabaseHas('settings', ['key' => 'default_card_expiry_days', 'value' => '180']);
        $this->assertDatabaseHas('settings', ['key' => 'allow_custom_domains', 'value' => '1']);
        $this->assertDatabaseHas('settings', ['key' => 'enable_analytics_tracking', 'value' => '1']);
        $this->assertDatabaseHas('settings', ['key' => 'auto_approve_cards', 'value' => '0']);

        $this->assertSame(5, Setting::get('max_cards_per_user'));
        $this->assertFalse(Setting::get('auto_approve_cards'));
    }

    public function test_update_settings_validation_fails_returns_422(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);

        $response = $this->actingAs($admin)->postJson(route('admin.settings.update'), [
            'max_cards_per_user' => 0,
            'default_card_expiry' => 0,
        ]);

        // characterizes current behavior: updateSettings catches the
        // ValidationException and returns a JSON 422 with success=false.
        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }
}

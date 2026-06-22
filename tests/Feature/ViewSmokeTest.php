<?php

namespace Tests\Feature;

use App\Models\CardAnalytic;
use App\Models\Rsvp;
use App\Models\User;
use App\Models\WeddingCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Guardrail smoke tests for Blade pages that render the status/type columns
 * which are being migrated to enum casts. These pages echo/compare those
 * attributes; the tests assert the pages return HTTP 200 (not 500) so that a
 * broken enum integration is caught.
 */
class ViewSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['type' => 'admin']);
    }

    private function user(): User
    {
        return User::factory()->create(['type' => 'user']);
    }

    /**
     * Seed a published card owned by the given user with a mix of analytics and
     * RSVP rows so the count/where expressions in the views actually execute.
     */
    private function seededCard(User $owner): WeddingCard
    {
        $card = WeddingCard::factory()->for($owner)->approved()->create();

        CardAnalytic::factory()->for($card, 'weddingCard')->view()->count(3)->create();
        CardAnalytic::factory()->for($card, 'weddingCard')->share()->count(2)->create();

        Rsvp::factory()->for($card, 'weddingCard')->attending()->count(2)->create();
        Rsvp::factory()->for($card, 'weddingCard')->notAttending()->create();

        return $card;
    }

    public function test_admin_card_show_page_loads(): void
    {
        $admin = $this->admin();
        $card = $this->seededCard($this->user());

        $response = $this->actingAs($admin)->get(route('admin.cards.show', $card));

        $response->assertOk();
        // analytics view count is rendered on the page
        $response->assertSee('3');
    }

    public function test_admin_published_cards_page_loads(): void
    {
        $admin = $this->admin();
        $this->seededCard($this->user());

        $response = $this->actingAs($admin)->get(route('admin.cards.published'));

        $response->assertOk();
    }

    public function test_admin_users_index_page_loads(): void
    {
        $admin = $this->admin();
        $this->user();

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertOk();
    }

    public function test_admin_users_edit_page_loads(): void
    {
        $admin = $this->admin();
        $client = $this->user();

        $response = $this->actingAs($admin)->get(route('admin.users.edit', $client));

        $response->assertOk();
    }

    public function test_admin_users_show_page_loads(): void
    {
        $admin = $this->admin();
        $client = $this->user();

        $response = $this->actingAs($admin)->get(route('admin.users.show', $client));

        $response->assertOk();
    }

    public function test_admin_dashboard_page_loads(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
    }

    public function test_admin_profile_page_loads(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get(route('admin.profile'));

        $response->assertOk();
    }

    public function test_user_dashboard_page_loads(): void
    {
        $user = $this->user();
        $this->seededCard($user);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertOk();
    }

    public function test_user_rsvps_index_page_loads(): void
    {
        $user = $this->user();
        $card = $this->seededCard($user);

        $response = $this->actingAs($user)->get(route('user.cards.rsvps', $card));

        $response->assertOk();
    }

    public function test_user_sharing_index_page_loads(): void
    {
        $user = $this->user();
        $card = $this->seededCard($user);

        $response = $this->actingAs($user)->get(route('user.cards.share', $card));

        $response->assertOk();
    }
}

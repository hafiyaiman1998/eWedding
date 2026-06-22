<?php

namespace Tests\Feature;

use App\Models\WeddingCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_card_public_url_loads_and_records_view(): void
    {
        $card = WeddingCard::factory()->published()->create();

        $response = $this->get(route('wedding-card.view', $card->unique_url));

        $response->assertOk();
        $this->assertDatabaseHas('card_analytics', [
            'wedding_card_id' => $card->id,
            'event_type' => 'view',
        ]);
    }

    public function test_unpublished_card_public_url_is_404(): void
    {
        $card = WeddingCard::factory()->create(['is_published' => false]);

        $response = $this->get(route('wedding-card.view', $card->unique_url));

        $response->assertNotFound();
    }

    public function test_expired_published_card_shows_expired_view_without_tracking(): void
    {
        $card = WeddingCard::factory()->published()->expired()->create();

        $response = $this->get(route('wedding-card.view', $card->unique_url));

        // characterizes current behavior: expired cards render the expired view
        // (still HTTP 200) and do not record a view analytic.
        $response->assertOk();
        $this->assertDatabaseMissing('card_analytics', [
            'wedding_card_id' => $card->id,
            'event_type' => 'view',
        ]);
    }

    public function test_analytics_track_route_stores_a_card_analytic(): void
    {
        $card = WeddingCard::factory()->create();

        $response = $this->postJson(route('analytics.track'), [
            'wedding_card_id' => $card->id,
            'event_type' => 'share',
            'metadata' => ['channel' => 'whatsapp'],
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('card_analytics', [
            'wedding_card_id' => $card->id,
            'event_type' => 'share',
        ]);
    }

    public function test_analytics_track_validates_wedding_card_exists(): void
    {
        $response = $this->postJson(route('analytics.track'), [
            'wedding_card_id' => 999999,
            'event_type' => 'view',
        ]);

        $response->assertStatus(422);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Rsvp;
use App\Models\WeddingCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RsvpTest extends TestCase
{
    use RefreshDatabase;

    private function publishedCard(): WeddingCard
    {
        return WeddingCard::factory()->published()->create();
    }

    public function test_guest_can_submit_rsvp_to_published_card(): void
    {
        $card = $this->publishedCard();

        // characterizes current behavior: guest_phone must be supplied. The
        // controller reads $validated['guest_phone'] directly, so omitting the
        // (nominally optional) phone triggers an "Undefined array key" error.
        $response = $this->postJson(route('rsvp.store', $card->unique_url), [
            'guest_name' => 'Alice',
            'guest_email' => 'alice@example.com',
            'guest_phone' => '012-3456789',
            'attendance_status' => 'yes',
            'number_of_guests' => 2,
            'message' => 'Cannot wait!',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('rsvps', [
            'wedding_card_id' => $card->id,
            'guest_email' => 'alice@example.com',
            'attendance_status' => 'yes',
            'number_of_guests' => 2,
        ]);

        // store also records a card analytic event
        $this->assertDatabaseHas('card_analytics', [
            'wedding_card_id' => $card->id,
            'event_type' => 'rsvp_yes',
        ]);
    }

    public function test_duplicate_email_rsvp_is_rejected(): void
    {
        $card = $this->publishedCard();
        Rsvp::factory()->for($card, 'weddingCard')->create([
            'guest_email' => 'dup@example.com',
        ]);

        $response = $this->postJson(route('rsvp.store', $card->unique_url), [
            'guest_name' => 'Dup',
            'guest_email' => 'dup@example.com',
            'attendance_status' => 'yes',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_rsvp_to_unpublished_card_is_404(): void
    {
        $card = WeddingCard::factory()->create(['is_published' => false]);

        $response = $this->postJson(route('rsvp.store', $card->unique_url), [
            'guest_name' => 'Bob',
            'guest_email' => 'bob@example.com',
            'attendance_status' => 'no',
        ]);

        $response->assertNotFound();
    }

    public function test_guest_can_update_existing_rsvp(): void
    {
        $card = $this->publishedCard();
        $rsvp = Rsvp::factory()->for($card, 'weddingCard')->attending()->create();

        // characterizes current behavior: guest_phone AND message must be
        // supplied; update() reads $validated['guest_phone'] / ['message']
        // directly, so omitting either triggers an "Undefined array key" error.
        $response = $this->putJson(route('rsvp.update', [$card->unique_url, $rsvp->id]), [
            'guest_name' => 'Updated Guest',
            'guest_phone' => '019-9999999',
            'attendance_status' => 'no',
            'message' => 'Sorry, cannot make it',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $fresh = $rsvp->fresh();
        $this->assertSame('Updated Guest', $fresh->guest_name);
        $this->assertSame('no', $fresh->attendance_status);
    }

    public function test_update_rsvp_mismatched_card_is_404(): void
    {
        $card = $this->publishedCard();
        $otherCard = $this->publishedCard();
        $rsvp = Rsvp::factory()->for($otherCard, 'weddingCard')->create();

        $response = $this->putJson(route('rsvp.update', [$card->unique_url, $rsvp->id]), [
            'guest_name' => 'X',
            'attendance_status' => 'yes',
        ]);

        $response->assertNotFound();
    }

    public function test_check_email_reports_existing_rsvp(): void
    {
        $card = $this->publishedCard();
        Rsvp::factory()->for($card, 'weddingCard')->create([
            'guest_email' => 'known@example.com',
        ]);

        $response = $this->postJson(route('rsvp.check-email', $card->unique_url), [
            'email' => 'known@example.com',
        ]);

        $response->assertOk();
        $response->assertJson(['has_rsvp' => true]);
    }

    public function test_check_email_reports_no_rsvp_for_unknown_email(): void
    {
        $card = $this->publishedCard();

        $response = $this->postJson(route('rsvp.check-email', $card->unique_url), [
            'email' => 'nobody@example.com',
        ]);

        $response->assertOk();
        $response->assertJson(['has_rsvp' => false]);
    }
}

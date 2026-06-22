<?php

namespace Tests\Feature\User;

use App\Models\DesignTemplate;
use App\Models\Setting;
use App\Models\User;
use App\Models\WeddingCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeddingCardTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create(['type' => 'user']);
    }

    public function test_user_can_view_their_cards_index(): void
    {
        $user = $this->user();

        $response = $this->actingAs($user)->get(route('user.cards.index'));

        $response->assertOk();
    }

    public function test_user_can_store_a_card(): void
    {
        $user = $this->user();
        $template = DesignTemplate::factory()->create();

        $response = $this->actingAs($user)->post(route('user.cards.store'), [
            'design_template_id' => $template->id,
            'title' => 'Our Wedding',
            'card_details' => ['bride_name' => 'Jane', 'groom_name' => 'John'],
            'custom_message' => 'Join us',
        ]);

        $card = WeddingCard::where('user_id', $user->id)->first();
        $this->assertNotNull($card);
        $response->assertRedirect(route('user.cards.edit', $card));
        $this->assertSame('Our Wedding', $card->title);

        // characterizes current behavior: auto_approve_cards defaults to true,
        // so a stored card is "approved" but NOT auto-published.
        $this->assertSame('approved', $card->approval_status);
        $this->assertFalse($card->is_published);
        $this->assertNotNull($card->approved_at);
    }

    public function test_user_can_update_own_card(): void
    {
        $user = $this->user();
        $template = DesignTemplate::factory()->create();
        $card = WeddingCard::factory()->for($user)->create([
            'design_template_id' => $template->id,
        ]);

        $response = $this->actingAs($user)->put(route('user.cards.update', $card), [
            'design_template_id' => $template->id,
            'title' => 'Updated Title',
            'card_details' => ['bride_name' => 'A', 'groom_name' => 'B'],
        ]);

        $response->assertRedirect(route('user.cards.edit', $card));
        $this->assertSame('Updated Title', $card->fresh()->title);
    }

    public function test_user_can_delete_own_card(): void
    {
        $user = $this->user();
        $card = WeddingCard::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('user.cards.destroy', $card));

        $response->assertRedirect(route('user.cards.index'));
        $this->assertDatabaseMissing('wedding_cards', ['id' => $card->id]);
    }

    public function test_user_can_toggle_published_with_auto_approve(): void
    {
        Setting::set('auto_approve_cards', '1', 'boolean');
        $user = $this->user();
        $card = WeddingCard::factory()->for($user)->create(['is_published' => false]);

        $response = $this->actingAs($user)->post(route('user.cards.toggle-published', $card));

        $response->assertRedirect();
        $fresh = $card->fresh();
        $this->assertTrue($fresh->is_published);
        $this->assertSame('approved', $fresh->approval_status);
    }

    public function test_per_user_card_limit_is_enforced(): void
    {
        Setting::set('max_cards_per_user', '1', 'integer');
        $user = $this->user();
        $template = DesignTemplate::factory()->create();
        WeddingCard::factory()->for($user)->create();

        $response = $this->actingAs($user)->post(route('user.cards.store'), [
            'design_template_id' => $template->id,
            'title' => 'Second Card',
            'card_details' => [],
        ]);

        // characterizes current behavior: at/over the limit the store is rejected
        // with a redirect back to the index and a flashed error message.
        $response->assertRedirect(route('user.cards.index'));
        $response->assertSessionHas('error');
        $this->assertSame(1, WeddingCard::where('user_id', $user->id)->count());
    }

    public function test_user_cannot_update_another_users_card(): void
    {
        $owner = $this->user();
        $other = $this->user();
        $template = DesignTemplate::factory()->create();
        $card = WeddingCard::factory()->for($owner)->create();

        $response = $this->actingAs($other)->put(route('user.cards.update', $card), [
            'design_template_id' => $template->id,
            'title' => 'Hijacked',
            'card_details' => [],
        ]);

        $response->assertForbidden();
        $this->assertNotSame('Hijacked', $card->fresh()->title);
    }

    public function test_user_cannot_delete_another_users_card(): void
    {
        $owner = $this->user();
        $other = $this->user();
        $card = WeddingCard::factory()->for($owner)->create();

        $response = $this->actingAs($other)->delete(route('user.cards.destroy', $card));

        $response->assertForbidden();
        $this->assertDatabaseHas('wedding_cards', ['id' => $card->id]);
    }
}

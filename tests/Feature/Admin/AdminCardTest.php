<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\WeddingCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCardTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['type' => 'admin']);
    }

    public function test_admin_can_approve_a_pending_card(): void
    {
        $admin = $this->admin();
        $card = WeddingCard::factory()->pending()->create(['is_published' => false]);

        $response = $this->actingAs($admin)
            ->post(route('admin.cards.approve', $card));

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $fresh = $card->fresh();
        $this->assertSame('approved', $fresh->approval_status);
        $this->assertTrue($fresh->is_published);
        $this->assertSame($admin->id, $fresh->approved_by);
        $this->assertNotNull($fresh->approved_at);
    }

    public function test_approve_fails_when_card_not_pending(): void
    {
        $admin = $this->admin();
        $card = WeddingCard::factory()->approved()->create();

        $response = $this->actingAs($admin)
            ->post(route('admin.cards.approve', $card));

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    public function test_admin_can_reject_a_pending_card_with_reason(): void
    {
        $admin = $this->admin();
        $card = WeddingCard::factory()->pending()->create(['is_published' => false]);

        $response = $this->actingAs($admin)
            ->post(route('admin.cards.reject', $card), [
                'reason' => 'Inappropriate content',
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $fresh = $card->fresh();
        $this->assertSame('rejected', $fresh->approval_status);
        $this->assertFalse($fresh->is_published);
        $this->assertSame('Inappropriate content', $fresh->rejection_reason);
        $this->assertSame($admin->id, $fresh->approved_by);
    }

    public function test_reject_requires_a_reason(): void
    {
        $admin = $this->admin();
        $card = WeddingCard::factory()->pending()->create();

        $response = $this->actingAs($admin)
            ->postJson(route('admin.cards.reject', $card), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('reason');
    }

    public function test_non_admin_user_cannot_access_approve(): void
    {
        $user = User::factory()->create(['type' => 'user']);
        $card = WeddingCard::factory()->pending()->create();

        $response = $this->actingAs($user)
            ->post(route('admin.cards.approve', $card));

        // characterizes current behavior: the admin middleware redirects
        // non-admins to /login rather than returning a 403.
        $response->assertRedirect('/login');
    }
}

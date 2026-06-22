<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\WeddingCard;
use App\Policies\WeddingCardPolicy;
use Tests\TestCase;

class WeddingCardPolicyTest extends TestCase
{
    private WeddingCardPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new WeddingCardPolicy;
    }

    private function user(int $id, string $type = 'user'): User
    {
        $user = new User;
        $user->id = $id;
        $user->type = $type;

        return $user;
    }

    private function cardOwnedBy(int $userId): WeddingCard
    {
        $card = new WeddingCard;
        $card->user_id = $userId;

        return $card;
    }

    public function test_owner_can_view_update_and_delete(): void
    {
        $user = $this->user(1);
        $card = $this->cardOwnedBy(1);

        $this->assertTrue($this->policy->view($user, $card));
        $this->assertTrue($this->policy->update($user, $card));
        $this->assertTrue($this->policy->delete($user, $card));
    }

    public function test_non_owner_cannot_view_update_or_delete(): void
    {
        $user = $this->user(2);
        $card = $this->cardOwnedBy(1);

        $this->assertFalse($this->policy->view($user, $card));
        $this->assertFalse($this->policy->update($user, $card));
        $this->assertFalse($this->policy->delete($user, $card));
    }

    public function test_view_any_and_create_require_user_type(): void
    {
        // characterizes current behavior: viewAny/create return $user->isUser().
        $client = $this->user(1, 'user');
        $admin = $this->user(2, 'admin');

        $this->assertTrue($this->policy->viewAny($client));
        $this->assertTrue($this->policy->create($client));

        $this->assertFalse($this->policy->viewAny($admin));
        $this->assertFalse($this->policy->create($admin));
    }
}

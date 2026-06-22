<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WeddingCard;

class WeddingCardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isUser();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WeddingCard $weddingCard): bool
    {
        return $user->id === $weddingCard->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isUser();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WeddingCard $weddingCard): bool
    {
        return $user->id === $weddingCard->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WeddingCard $weddingCard): bool
    {
        return $user->id === $weddingCard->user_id;
    }
}

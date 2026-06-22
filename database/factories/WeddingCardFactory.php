<?php

namespace Database\Factories;

use App\Models\DesignTemplate;
use App\Models\User;
use App\Models\WeddingCard;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeddingCard>
 */
class WeddingCardFactory extends Factory
{
    protected $model = WeddingCard::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'design_template_id' => DesignTemplate::factory(),
            'title' => fake()->words(3, true),
            'card_details' => [
                'bride_name' => fake()->firstNameFemale(),
                'groom_name' => fake()->firstNameMale(),
                'wedding_date' => fake()->date(),
                'venue' => fake()->address(),
            ],
            'custom_message' => fake()->sentence(),
            'is_published' => false,
            'approval_status' => 'pending',
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
            'unique_url' => Str::random(10),
            'expiry_date' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'approval_status' => 'pending',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => [
            'approval_status' => 'approved',
            'approved_at' => now(),
            'is_published' => true,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'approval_status' => 'rejected',
            'is_published' => false,
            'rejection_reason' => fake()->sentence(),
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_published' => true,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes): array => [
            'expiry_date' => now()->subDay(),
        ]);
    }
}

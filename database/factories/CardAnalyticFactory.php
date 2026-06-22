<?php

namespace Database\Factories;

use App\Models\CardAnalytic;
use App\Models\WeddingCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CardAnalytic>
 */
class CardAnalyticFactory extends Factory
{
    protected $model = CardAnalytic::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wedding_card_id' => WeddingCard::factory(),
            'event_type' => fake()->randomElement(['view', 'share', 'rsvp_yes', 'rsvp_no']),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'referrer' => fake()->url(),
            'metadata' => [],
        ];
    }

    public function view(): static
    {
        return $this->state(fn (array $attributes): array => [
            'event_type' => 'view',
        ]);
    }

    public function share(): static
    {
        return $this->state(fn (array $attributes): array => [
            'event_type' => 'share',
        ]);
    }
}

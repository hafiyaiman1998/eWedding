<?php

namespace Database\Factories;

use App\Models\Rsvp;
use App\Models\WeddingCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rsvp>
 */
class RsvpFactory extends Factory
{
    protected $model = Rsvp::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wedding_card_id' => WeddingCard::factory(),
            'guest_name' => fake()->name(),
            'guest_email' => fake()->safeEmail(),
            'guest_phone' => fake()->phoneNumber(),
            'attendance_status' => fake()->randomElement(['yes', 'no']),
            'number_of_guests' => fake()->numberBetween(1, 5),
            'message' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    public function attending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'attendance_status' => 'yes',
        ]);
    }

    public function notAttending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'attendance_status' => 'no',
            'number_of_guests' => 1,
        ]);
    }
}

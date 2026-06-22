<?php

namespace Database\Factories;

use App\Models\Gift;
use App\Models\WeddingCard;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gift>
 */
class GiftFactory extends Factory
{
    protected $model = Gift::class;

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
            'amount' => fake()->randomFloat(2, 10, 500),
            'currency' => 'MYR',
            'bill_code' => null,
            'bill_payment_id' => null,
            'external_reference_no' => 'GIFT-'.now()->format('YmdHis').'-'.Str::random(6),
            'status' => 'pending',
            'payment_url' => null,
            'message' => fake()->sentence(),
            'toyyibpay_response' => null,
            'paid_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'paid',
            'paid_at' => now(),
            'bill_code' => Str::random(8),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'failed',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'cancelled',
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setting>
 */
class SettingFactory extends Factory
{
    protected $model = Setting::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(),
            'value' => fake()->word(),
            'type' => 'string',
            'description' => fake()->sentence(),
        ];
    }

    public function boolean(bool $value = true): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => 'boolean',
            'value' => $value ? '1' : '0',
        ]);
    }

    public function integer(int $value = 0): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => 'integer',
            'value' => (string) $value,
        ]);
    }
}

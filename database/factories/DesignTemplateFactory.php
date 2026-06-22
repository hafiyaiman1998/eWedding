<?php

namespace Database\Factories;

use App\Models\DesignTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DesignTemplate>
 */
class DesignTemplateFactory extends Factory
{
    protected $model = DesignTemplate::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'blade_template' => '<div>{{ bride_name }} & {{ groom_name }}</div>',
            'full_html_template' => '<html><body>{{ bride_name }} & {{ groom_name }}</body></html>',
            'category' => fake()->randomElement(['general', 'malaysian', 'modern', 'traditional']),
            'is_malaysian_design' => false,
            'preview_image' => null,
            'default_variables' => ['bride_name' => 'Jane', 'groom_name' => 'John'],
            'is_active' => true,
        ];
    }

    public function malaysian(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_malaysian_design' => true,
            'category' => 'malaysian',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}

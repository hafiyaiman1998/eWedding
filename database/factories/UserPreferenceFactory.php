<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPreference>
 */
class UserPreferenceFactory extends Factory
{
    protected $model = UserPreference::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return array_merge(
            ['user_id' => User::factory()],
            UserPreference::getDefaults()
        );
    }
}

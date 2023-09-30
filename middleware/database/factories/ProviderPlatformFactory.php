<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProviderPlatform>
 */
class ProviderPlatformFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'type' => fake()->text($maxNbChars = 20),
            'type' => fake()->word,
            'name' => fake()->sentence($nbWords = 3),
            // 'name' => fake()->words($nb = 2),
            'description' => fake()->sentence(),
            'icon_url' => fake()->imageUrl(),
            'account_id' => Str::random(10),
            'access_key' => Str::random(16),
            'base_url' => fake()->url(),

        ];
    }
}

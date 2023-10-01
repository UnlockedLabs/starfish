<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConsumerPlatform>
 */
class ConsumerPlatformFactory extends Factory
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
            'api_key' => fake()->unique()->password($minLength = 30, $maxLength = 128),
            'base_url' => fake()->url(),
        ];
    }
}

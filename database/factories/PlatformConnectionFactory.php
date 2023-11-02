<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlatformConnection>
 */
class PlatformConnectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'consumer_platform_id' => $this->faker->numberBetween(1, 10),
            'provider_platform_id' => $this->faker->numberBetween(1, 10),
            'state' => 'enabled',
        ];
    }
}

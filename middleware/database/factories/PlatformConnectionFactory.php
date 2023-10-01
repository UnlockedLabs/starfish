<?php

namespace Database\Factories;

use App\Models\ConsumerPlatform;
use App\Models\ProviderPlatform;
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
        $consumer = ConsumerPlatform::pluck('id')->toArray();
        $provider = ProviderPlatform::pluck('id')->toArray();
        return [
            'consumer_platform_id' => fake()->randomElement($consumer),
            'provider_platform_id' => fake()->randomElement($provider),
        ];
    }
}

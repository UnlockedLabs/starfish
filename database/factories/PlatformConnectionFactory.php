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
        return [
            'consumer_platform_id' => ConsumerPlatform::factory()->create()->id,
            'provider_platform_id' => ProviderPlatform::factory()->create()->id,
            'state' => 'enabled',
        ];
    }
}

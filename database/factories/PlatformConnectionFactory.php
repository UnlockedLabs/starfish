<?php

namespace Database\Factories;

use App\Models\PlatformConnection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlatformConnection>
 */
class PlatformConnectionFactory extends Factory
{
    protected $model = PlatformConnection::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'consumer_platform_id' => \App\Models\ConsumerPlatform::factory(),
            'provider_platform_id' => \App\Models\ProviderPlatform::factory(),
            'state' => 'enabled',
        ];
    }
}

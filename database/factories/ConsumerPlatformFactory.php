<?php

namespace Database\Factories;

use App\Models\ConsumerPlatform;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConsumerPlatform>
 */
class ConsumerPlatformFactory extends Factory
{
    protected $model = ConsumerPlatform::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'canvas_cloud',
            'name' => $this->faker->sentence,
            'api_key' => $this->faker->imageUrl,
            'base_url' => $this->faker->randomNumber,
        ];
    }
}

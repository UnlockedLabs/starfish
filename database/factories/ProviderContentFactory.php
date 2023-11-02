<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProviderContent>
 */
class ProviderContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_content_id' => $this->faker->randomDigitNotNull(),
            'type' => 'course',
            'external_resource_id' => $this->faker->randomDigitNotNull(),
            'provider_platform_id' => $this->faker->randomDigitNotNull(),
        ];
    }
}

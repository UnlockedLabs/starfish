<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentEnrollment>
 */
class StudentEnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "provider_user_id" => $this->faker->randomDigitNotNull,              // Student ID in our system (maps to appropriate ID)
            "provider_content_id" => $this->faker->randomDigitNotNull,           // Course ID
            "provider_platform_id" => $this->faker->randomDigitNotNull,
            "status" => "enabled",                        // Enum (ProviderUserResourceStatus)
        ];
    }
}

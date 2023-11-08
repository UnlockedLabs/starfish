<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Student;
use App\Models\ProviderPlatform;
use App\Models\ConsumerPlatform;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentEnrollment>
 */
class StudentMappingFactory extends Factory
{
    public function definition()
    {
        return [
            'student_id' => Student::factory()->create(),
            'provider_user_id' => $this->faker->randomDigitNotNull,
            'provider_platform_id' => ProviderPlatform::factory()->create(),
            'consumer_user_id' => $this->faker->randomDigitNotNull,
            'consumer_platform_id' => ConsumerPlatform::factory()->create(),
        ];
    }
}

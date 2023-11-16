<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentEnrollment>
 */
class StudentMappingFactory extends Factory
{
    protected $model = \App\Models\StudentMapping::class;

    public function definition()
    {
        return [
            'student_id' => \App\Models\Student::factory(),
            'provider_user_id' => $this->faker->randomDigitNotNull(),
            'provider_platform_id' => \App\Models\ProviderPlatform::factory(),
            'consumer_platform_id' => \App\Models\ConsumerPlatform::factory(),
            'consumer_user_id' => $this->faker->randomDigitNotNull,
        ];
    }
}

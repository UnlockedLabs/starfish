<?php

namespace Database\Factories;

use App\Models\ProviderPlatform;
use App\Models\StudentEnrollment;
use App\Models\StudentMapping;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentEnrollment>
 */
class StudentEnrollmentFactory extends Factory
{
    protected $model = StudentEnrollment::class;
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "provider_user_id" =>  \App\Models\StudentMapping::factory(),
            "provider_content_id" =>  \App\Models\ProviderContent::factory(),
            "provider_platform_id" => \App\Models\ProviderPlatform::factory(),
            "status" => "enabled",                        // Enum (ProviderUserResourceStatus)
        ];
    }
}

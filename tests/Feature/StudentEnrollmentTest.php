<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Factories\ConsumerPlatformFactory;
use App\Models\StudentEnrollment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\TestCase;

class StudentEnrollmentControllerTest extends TestCase
{
    use RefreshDatabase; // This ensures a fresh database for each test.

    public function testShow()
    {
        $response = $this->get('/api/v1/students/1/courses');

        $response->assertStatus(200);
    }

    public function testEdit()
    {
        $studentEnrollmeent = ['data' => [
            'provider_user_id' => 2,
            'provider_content_id' => 183,
            'provider_platform_id' => 1,
            'status' => 'in_progress',
        ]];

        $response = $this->patch('/api/v1/students/2/courses', $studentEnrollmeent);

        $response->assertStatus(200);
    }
}

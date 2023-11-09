<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudentEnrollmentControllerTest extends TestCase
{
    public function testShow()
    {
        $response = $this->get('/api/v1/students/1/courses');

        $response->assertStatus(200);
    }

    public function testEdit()
    {
        $studentEnrollment = [
            'provider_user_id' => '1',
            'provider_content_id' => '6',
            'provider_platform_id' => '4',
            'status' => 'in_progress',
        ];

        $response = $this->patch('/api/v1/students/1/courses', $studentEnrollment);

        $response->assertStatus(200);
    }
}

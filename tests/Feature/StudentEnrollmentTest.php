<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudentEnrollmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testShow()
    {
        $this->seed();
        $consumerId = \App\Models\StudentMapping::factory()->createOne();
        $show = \App\Models\StudentEnrollment::factory()->create();
        $response = $this->get('/api/v1/students/' . $consumerId->consumer_user_id . '/courses');

        $response->assertStatus(200);
    }

    public function testEdit()
    {
        $this->seed();
        $consumerId = \App\Models\StudentMapping::factory()->createOne();
        $show = \App\Models\StudentEnrollment::factory()->create();
        time_nanosleep(1, 0);
        $response = $this->patch('/api/v1/students/' . $consumerId->consumer_user_id . '/courses', $show->toArray());

        $response->assertStatus(200);
    }
}

<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Factories\ConsumerPlatformFactory;
use App\Models\ConsumerPlatform;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\TestCase;

class ConsumerPlatformTest extends TestCase
{
    use RefreshDatabase; // This ensures a fresh database for each test.
    public function testIndex()
    {
        $response = $this->get('/api/v1/consumer_platforms');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'name',
                        'api_key',
                        'base_url',
                    ]
                ]
            ]);
    }

    public function testStore()
    {
        $data = [
            'type' => 'canvas_cloud',
            'name' => 'TestPlatform',
            'api_key' => 'testkey123',
            'base_url' => 'https://test.com/api',
        ];
        $response = $this->post('/api/v1/consumer_platforms', $data);

        $response->assertStatus(201);
    }

    public function testShow()
    {
        $response = $this->get('/api/v1/consumer_platforms/1');

        $response->assertStatus(200);
    }

    public function testEdit()
    {
        $platform = ['data' => [
            'type' => 'canvas_cloud',
            'name' => 'TestPlatform',
            'api_key' => 'testkey123',
            'base_url' => 'https://test.com/api',
        ]];

        $response = $this->patch('/api/v1/consumer_platforms/2', $platform);

        $response->assertStatus(200);
    }

    public function testDestroy()
    {
        $response = $this->delete('/api/v1/consumer_platforms/1');

        $response->assertStatus(200)
            ->assertJson([
                'success' => 'consumer platform deleted',
            ]);
    }
}

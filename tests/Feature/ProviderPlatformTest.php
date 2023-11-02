<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderPlatformTest extends TestCase
{
    use RefreshDatabase; // This ensures a fresh database for each test.

    /**
     * Test retrieving provider platforms.
     */
    public function testGetProviderPlatforms()
    {
        $response = $this->get('/api/v1/provider_platforms');

        $response->assertStatus(200); // Check if the response code is OK.
        $response->assertJsonStructure([ // Check the JSON structure of the response.
            'data' => [
                '*' => [
                    'name',
                    'type',
                    'description',
                    'icon_url',
                    'account_id',
                    'access_key',
                    'base_url',
                ],
            ],
        ]);
    }

    /**
     * Test creating a provider platform.
     */
    public function testCreateProviderPlatform()
    {
        $data = [
            'name' => 'TestPlatform',
            'type' => 'canvas_cloud',
            'description' => 'Test Platform Description',
            'icon_url' => 'https://test.com/icon.jpg',
            'account_id' => 123456,
            'access_key' => 'testkey123',
            'base_url' => 'https://test.com/api',
        ];

        $response = $this->post('/api/v1/provider_platforms', $data);

        $response->assertStatus(201); // Check if the response code indicates a successful creation.
        $response->assertJsonFragment(['name' => 'TestPlatform']); // Check if the response contains the created data.
    }
}

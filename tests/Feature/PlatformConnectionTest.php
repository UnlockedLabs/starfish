<?php

use App\Models\ConsumerPlatform;
use App\Models\PlatformConnection;
use App\Models\ProviderPlatform;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformConnectionTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $this->seed();

        $response = $this->get('/api/v1/platform_connections');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'consumer_platform_id',
                        'provider_platform_id',
                        'state'
                    ]
                ]
            ]);
    }

    public function testShow()
    {
        $conn = PlatformConnection::factory()->create();
        $consumer_platform_id = $conn->consumer_platform_id;

        $response = $this->get('/api/v1/consumer_platforms/' . $consumer_platform_id . '/platform_connections');
        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                "data" =>
                [
                    "*" => [
                        "provider_platform_id",
                        "type",
                        "name",
                        "description",
                        "icon_url",
                        "account_id",
                        "access_key",
                        "base_url",
                        "state",
                        "connection_id",

                    ]
                ]
            ]
        );
    }

    public function testStore()
    {
        $this->seed();
        $consumer = ConsumerPlatform::factory()->create();
        $provider = ProviderPlatform::factory()->create();

        $data = [
            "consumer_platform_id" => $consumer->id,
            "provider_platform_id" => $provider->id,
            "state" => "enabled",
        ];
        $response = $this->post('/api/v1/consumer_platforms/' . $consumer->id . '/platform_connections', $data);

        $response->assertStatus(200);
    }


    public function testEdit()
    {
        $this->seed();

        $platform = PlatformConnection::factory()->create();
        $data = [
            "consumer_platform_id" => $platform->consumer_platform_id,
            "provider_platform_id" => $platform->provider_platform_id,
            "state" => "disabled",
        ];
        $platform['state'] = 'disabled';

        $response = $this->put('/api/v1/consumer_platforms/' . $platform->consumer_platform_id . '/platform_connections/', $data);

        $response->assertStatus(200);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PlatformConnection
 *
 * @property int $id
 * @property int $consumer_platform_id
 * @property int $provider_platform_id
 * @property string $state
 */
class PlatformConnection extends Model
{
    use HasFactory;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Create a new PlatformConnection and register the connection.
     *
     * @param int $consumerPlatformId
     * @param int $providerPlatformId
     * @return PlatformConnection
     */
    public static function createAndRegister(int $consumerPlatformId, int $providerPlatformId): PlatformConnection
    {
        $connection = new static();
        $connection->consumer_platform_id = $consumerPlatformId;
        $connection->provider_platform_id = $providerPlatformId;
        $connection->state = 'Enabled';
        $connection->registerConnection();

        return $connection;
    }

    /**
     * Register the connection if it doesn't already exist.
     */
    public function registerConnection(): void
    {
        self::updateOrCreate(
            [
                'consumer_platform_id' => $this->consumer_platform_id,
                'provider_platform_id' => $this->provider_platform_id,
            ],
            [
                'state' => 'Enabled',
            ]
        );
    }
}

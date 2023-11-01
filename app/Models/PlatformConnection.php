<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected $fillable = [
        'state',
        'consumer_platform',
        'provider_platform',
    ];
    public function consumerPlatform(): BelongsTo
    {
        return $this->belongsTo(ConsumerPlatform::class);
    }

    public function providerPlatform(): BelongsTo
    {
        return $this->belongsTo(ProviderPlatform::class);
    }
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}

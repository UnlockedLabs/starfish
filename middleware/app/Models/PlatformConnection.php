<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'consumer_platform_id',
        'provider_platform_id',
        // 'state', // commented out b/c the default state is "enabled"
    ];

    public function consumerPlatform(): BelongsTo
    {
        return $this->belongsTo(ConsumerPlatform::class);
    }

    public function providerPlatform(): BelongsTo
    {
        return $this->belongsTo(ProviderPlatform::class);
    }
}

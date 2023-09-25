<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'state',
        'consumer_platform',
        'provider_platform',
    ];

    protected $casts = [
        'state' => PlatformConnectionState::class
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

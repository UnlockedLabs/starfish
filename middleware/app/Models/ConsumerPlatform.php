<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsumerPlatform extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'api_key',
        'base_url',
    ];

    protected $hidden = [
        'api_key',
    ];

    protected $casts = [
        'type' => ConsumerPlatformType::class
    ];

    public function platformConnections(): HasMany
    {
        return $this->hasMany(PlatformConnections::class);
    }

    public function providerPlatforms(): BelongsToMany
    {
        return $this->belongsToMany(ProviderPlatform::class, 'platform_connections')->withPivot('state', 'id');
    }
}

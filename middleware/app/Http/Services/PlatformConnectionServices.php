<?php

namespace App\Services;

use App\Models\PlatformConnection;
use App\Http\Requests\PlatformConnectionRequest;
use App\Models\ConsumerPlatform;
use App\Models\ProviderPlatform;
use App\Http\Resources\PlatformConnectionResource;

class PlatformConnectionServices
{
    public static function showConnections(PlatformConnectionRequest $request): PlatformConnectionResource
    {
        // Show all connections for a certain consumer platform
        $validated = $request->validated();
        if ($validated->consumer_platform_id != null) {
            return $connections = PlatformConnection::where('consumer_platform_id', $request->consumer_platform_id)->get();
        }
    }
}

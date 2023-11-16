<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Requests\StorePlatformConnectionRequest;
use App\Http\Requests\UpdatePlatformConnectionRequest;
use App\Http\Resources\ConsumerProviderResource;
use App\Models\PlatformConnection;
use App\Http\Resources\PlatformConnectionResource;
use App\Models\ConsumerPlatform;
use Illuminate\Http\Request;

class PlatformConnectionController extends Controller
{

    /* Get all platform connections */
    //*************************************************************
    // GET: /api/v1/platform_connections
    // Request $req example:
    // *************************************************************
    public function index()
    {
        return PlatformConnectionResource::collection(PlatformConnection::all());
    }

    // look up a relative connection per consumer platform id
    // *************************************************************
    // GET: /api/v1/consumer_platforms/{id}/platform_connection/{id}
    // Request $req example:
    // *************************************************************
    public function show(string $id)
    {
        $platform = ConsumerPlatform::find($id);
        return ConsumerProviderResource::collection($platform->providerPlatforms);
    }

    /* Create a new platform connection */
    //*************************************************************
    // POST: /api/v1/consumer_platforms/{id}/platform_connection/
    // PlatformConnectionRequest $req example:
    // { "consumer_id": 1, "provider_id": 1, "state": "enabled" }
    // *************************************************************
    public function store(StorePlatformConnectionRequest $req): PlatformConnectionResource
    {
        try {
            $platform_connection = new PlatformConnection($req->validated());
        } catch (\Exception) {
            return response()->json(INVALID_REQUEST_BODY, 401);
        }

        return new PlatformConnectionResource($platform_connection);
    }

    // Update a platform connection (The only possible update here would be the state)
    // *************************************************************
    // PUT: /api/v1/consumer_platforms/{id}/platform_connections/{request_body}
    // Request $req example:
    // { "consumer_id": 1, "provider_id": 1, "state": "enabled" }
    // *************************************************************
    public function update(string $id, UpdatePlatformConnectionRequest $req): PlatformConnectionResource
    {
        // they should have both the id's, and a new state
        $conn = PlatformConnection::where('consumer_platform_id', $id)->first();
        $validated = $req->validated();
        $conn->update($validated);
        return new PlatformConnectionResource($conn);
    }
}

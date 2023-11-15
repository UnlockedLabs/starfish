<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Requests\StorePlatformConnectionRequest;
use App\Http\Requests\UpdatePlatformConnectionRequest;
use App\Http\Resources\ConsumerProviderResource;
use App\Models\PlatformConnection;
use App\Http\Resources\PlatformConnectionResource;
use App\Models\ConsumerPlatform;

class PlatformConnectionController extends Controller
{

    /* Get all platform connections */
    //*************************************************************
    // GET: /api/v1/consumer_platforms/{id}/platform_connections
    // Request $req example:
    // *************************************************************
    public function index(string $id): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return  ConsumerProviderResource::collection(PlatformConnection::all()->find($id));
    }

    // look up a relative connection per consumer platform id
    // *************************************************************
    // GET: /api/v1/consumer_platforms/{id}/platform_connection/{id}
    // Request $req example:
    // *************************************************************
    public function show(string $id): ConsumerProviderResource
    {
        $platform = ConsumerPlatform::find($id);
        return ConsumerProviderResource::make($platform->providerPlatform);
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
        $conn->update('state', $validated['state']);
        $conn->save();
        return new PlatformConnectionResource($conn);
    }

    // Delete a platform connection
    // *************************************************************
    // DELETE: /api/v1/consumer_platforms/{id}/platform_connection/{request_body}
    // Request $req example:
    // { "consumer_id": 1, "provider_id": 1 }
    // *************************************************************
    public function delete(string $id): \Illuminate\Http\JsonResponse
    {
        try {
            PlatformConnection::where('consumer_platform_id', $id)->delete();
            return response()->json(['success' => 'Platform connection deleted successfully'], 200);
        } catch (\Exception) {
            return response()->json(INVALID_REQUEST_BODY, 401);
        }
    }
}

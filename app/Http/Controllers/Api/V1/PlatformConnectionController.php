<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Requests\PlatformConnectionRequest;
use App\Http\Requests\ShowPlatformConnectionRequest;
use App\Http\Requests\StorePlatformConnectionRequest;
use App\Http\Requests\UpdatePlatformConnectionRequest;
use App\Models\PlatformConnection;
use App\Http\Resources\PlatformConnectionResource;


class PlatformConnectionController extends Controller
{

    /* Get all platform connections */
    //*************************************************************
    // GET: /api/platform_connection/
    // Request $req example:
    // { "consumer_id": 1 }
    // *************************************************************
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return  PlatformConnectionResource::collection(PlatformConnection::all());
    }

    /* Create a new platform connection */
    //*************************************************************
    // POST: /api/v1/platform_connection/
    // PlatformConnectionRequest $req example:
    // { "consumer_id": 1, "provider_id": 1, "state": "enabled" }
    // *************************************************************
    public function store(StorePlatformConnectionRequest $req): PlatformConnectionResource
    {
        $validated = $req->validated();
        $exists = PlatformConnection::where($validated)->first();
        if ($exists) {
            return response()->json(['error' => 'Platform connection already exists'], 401);
        }
        try {
            $platform_connection = PlatformConnection::create($req->validated());
            return new PlatformConnectionResource($platform_connection);
        } catch (\Exception) {
            return response()->json(INVALID_REQUEST_BODY, 401);
        }
    }

    // Get a specific platform connection by consumer or provider id
    // *************************************************************
    // GET: /api/v1/platform_connection/{id}
    // Request $req example:
    //  "consumer_id": 1 || "provider_id": 1
    // *************************************************************
    public function show(ShowPlatformConnectionRequest $req): PlatformConnectionResource
    {
        try {
            $validated = $req->validated();
            return new PlatformConnectionResource(PlatformConnection::where($validated)->first());
        } catch (\Exception) {
            return response()->json(INVALID_REQUEST_BODY, 401);
        }
    }

    // Update a platform connection (The only possible update here would be the state)
    // *************************************************************
    // PUT: /api/v1/platform_connection/{request_body}
    // Request $req example:
    // { "consumer_id": 1, "provider_id": 1, "state": "enabled" }
    // *************************************************************
    public function update(UpdatePlatformConnectionRequest $req): PlatformConnectionResource
    {
        $validated = $req->validated();
        $PlatformConnection = PlatformConnection::where($validated)->first();
        $PlatformConnection->state = $validated['state'];
        return new PlatformConnectionResource($PlatformConnection->save());
        if (!$PlatformConnection) {
            return response()->json(['error' => 'No matching platform connection found'], 401);
        }
    }

    // Delete a platform connection
    // *************************************************************
    // DELETE: /api/v1/platform_connection/{request_body}
    // Request $req example:
    // { "consumer_id": 1, "provider_id": 1 }
    // *************************************************************
    public function delete(ShowPlatformConnectionRequest $req): \Illuminate\Http\JsonResponse
    {
        try {
            PlatformConnectionRequest::where($req->validated())->delete();
            return response()->json(['success' => 'Platform connection deleted successfully'], 200);
        } catch (\Exception) {
            return response()->json(INVALID_REQUEST_BODY, 401);
        }
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\api\V1\Controllers\Controller;
use App\Http\Requests\PlatformConnectionRequest;
use App\Http\Requests\ShowPlatformConnectionRequest;
use App\Http\Requests\ShowStudentEnrollmentRequest;
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
    public function index(): PlatformConnectionResource
    {
        return PlatformConnectionResource::collection(PlatformConnection::all());
    }

    /* Create a new platform connection */
    //*************************************************************
    // POST: /api/platform_connection/
    // PlatformConnectionRequest $req example:
    // { "consumer_id": 1, "provider_id": 1, "state": "enabled" }
    // *************************************************************
    public function store(PlatformConnectionRequest $req): PlatformConnectionResource
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
            return response()->json(['error' => 'Invalid request body'], 401);
        }
    }

    // Get a specific platform connection by consumer or provider id
    // *************************************************************
    // GET: /api/platform_connection/{id}
    // Request $req example:
    //  "consumer_id": 1 || "provider_id": 1
    // *************************************************************
    public function show(ShowPlatformConnectionRequest $req): PlatformConnectionResource
    {
        try {
            $validated = $req->validated();
            return new PlatformConnectionResource(PlatformConnection::where($validated)->first());
        } catch (\Exception) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
    }

    // Update a platform connection (The only possible update here would be the state)
    // *************************************************************
    // PUT: /api/platform_connection/{request_body}
    // Request $req example:
    // { "consumer_id": 1, "provider_id": 1, "state": "enabled" }
    // *************************************************************
    public function update(PlatformConnectionRequest $req): PlatformConnectionResource
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
    // DELETE: /api/platform_connection/{request_body}
    // Request $req example:
    // { "consumer_id": 1, "provider_id": 1 }
    // *************************************************************
    public function delete(PlatformConnectionRequest $req): Illuminate\Http\JsonResponse
    {
        try {
            PlatformConnectionRequest::where($req->validated())->delete();
            return response()->json(['success' => 'Platform connection deleted successfully'], 200);
        } catch (\Exception) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
    }
}

<?php

namespace App\Http\Controllers\api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PlatformConnection;

class PlatformConnectionController extends Controller
{

    /* Get all platform connections */
    //*************************************************************
    //GET: /api/platform_connection/
    // Request $req example:
    // { "consumer_id": 1 }
    // *************************************************************
    public function index(): \Illuminate\Http\JsonResponse
    {
        try {
            $platform_connections = PlatformConnection::all(['*'])->toArray();
            return response()->json($platform_connections);
        } catch (\Exception) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
    }

    /* Create a new platform connection */
    //*************************************************************
    //POST: /api/platform_connection/
    // Request $req example:
    // { "consumer_id": 1, "provider_id": 1, "state": "enabled" }
    // *************************************************************
    public function store(Request $req): \Illuminate\Http\JsonResponse
    {
        try {
            $consumer_id = $req->input('consumer_id');
            $provider_id = $req->input('provider_id');
            $state = $req->input('state');
        } catch (\Exception) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
        try {
            $platform_connection = PlatformConnection::create([
                'consumer_id' => $consumer_id,
                'provider_id' => $provider_id,
                'state' => $state,
            ]);
            return response()->json($platform_connection);
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
    public function show(Request $req): \Illuminate\Http\JsonResponse
    {
        if ($req->input('provider_id') != null) {
            $platform_connection = PlatformConnection::where('platform_id', $req->input('platform_id'))->first();
        } else {
            $platform_connection = PlatformConnection::where('consumer_id', $req->input('consumer_id'))->first();
        }
        if (!$platform_connection) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
        return response()->json_encode($platform_connection, JSON_PRETTY_PRINT);
    }

    // Update a platform connection
    // *************************************************************
    // PUT: /api/platform_connection/{request_body}
    // Request $req example:
    // { "consumer_id": 1, "provider_id": 1, "state": "enabled" }
    // *************************************************************
    public function update(Request $req): \Illuminate\Http\JsonResponse
    {
        try {
            $consumer_id = $req->input('consumer_id');
            $provider_id = $req->input('provider_id');
            $state = $req->input('state');
        } catch (\Exception) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
        try {
            $platform_connection = PlatformConnection::where('consumer_id', $consumer_id)->where('provider_id', $provider_id)->first();
            $platform_connection->state = $state;
            $platform_connection->save();
            return response()->json($platform_connection);
        } catch (\Exception) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
    }

    // Delete a platform connection
    // *************************************************************
    // DELETE: /api/platform_connection/{request_body}
    // Request $req example:
    // { "consumer_id": 1, "provider_id": 1 }
    // *************************************************************
    public function delete(Request $req): \Illuminate\Http\JsonResponse
    {
        try {
            $consumer_id = $req->input('consumer_id');
            $provider_id = $req->input('provider_id');
        } catch (\Exception) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
        try {
            $platform_connection = PlatformConnection::where('consumer_id', $consumer_id)->where('provider_id', $provider_id)->first();
            $platform_connection->delete();
            return response()->json(['status' => 'success']);
        } catch (\Exception) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
    }
}

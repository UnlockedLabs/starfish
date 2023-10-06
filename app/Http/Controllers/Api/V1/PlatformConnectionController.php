<?php

<<<<<<< HEAD:app/Http/Controllers/Api/V1/PlatformConnectionController.php
namespace App\Http\Controllers\Api\V1;


use App\Http\Requests\PlatformConnectionRequest;
use App\Http\Requests\ShowPlatformConnectionRequest;
use App\Http\Requests\StorePlatformConnectionRequest;
use App\Http\Requests\UpdatePlatformConnectionRequest;
use App\Models\PlatformConnection;
use App\Http\Resources\PlatformConnectionResource;

=======
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PlatformConnection;
>>>>>>> 0feb532 (fix items in review with nokie. PR closes UN-102):middleware/app/Http/Controllers/PlatformConnectionController.php

class PlatformConnectionController extends Controller
{

    /* Get all platform connections */
    //*************************************************************
<<<<<<< HEAD:app/Http/Controllers/Api/V1/PlatformConnectionController.php
    // GET: /api/platform_connection/
    // Request $req example:
    // { "consumer_id": 1 }
    // *************************************************************
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return  PlatformConnectionResource::collection(PlatformConnection::all());
=======
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
>>>>>>> 0feb532 (fix items in review with nokie. PR closes UN-102):middleware/app/Http/Controllers/PlatformConnectionController.php
    }

    /* Create a new platform connection */
    //*************************************************************
<<<<<<< HEAD:app/Http/Controllers/Api/V1/PlatformConnectionController.php
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
=======
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
>>>>>>> 0feb532 (fix items in review with nokie. PR closes UN-102):middleware/app/Http/Controllers/PlatformConnectionController.php
        }
    }

    // Get a specific platform connection by consumer or provider id
    // *************************************************************
<<<<<<< HEAD:app/Http/Controllers/Api/V1/PlatformConnectionController.php
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
=======
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
>>>>>>> 0feb532 (fix items in review with nokie. PR closes UN-102):middleware/app/Http/Controllers/PlatformConnectionController.php
        }
    }

    // Delete a platform connection
    // *************************************************************
<<<<<<< HEAD:app/Http/Controllers/Api/V1/PlatformConnectionController.php
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
=======
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
>>>>>>> 0feb532 (fix items in review with nokie. PR closes UN-102):middleware/app/Http/Controllers/PlatformConnectionController.php
        }
    }
}

<?php

namespace App\Http\Controllers\api\V1;

use App\Models\ProviderPlatform;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\StoreProviderPlatformRequest;
use App\Http\Resources\ProviderPlatformResource;

use const App\Http\Controllers\Api\V1\INVALID_REQUEST_BODY as V1INVALID_REQUEST_BODY;

const INVALID_REQUEST_BODY = response()->json(['error', 'Invalid request body'], 401);

class ProviderPlatformController extends Controller
{

    // List all provider platforms known to the system
    // ****************************************************
    // GET: /api/provider_platforms/
    // ****************************************************
    public function index()
    {
        return ProviderPlatformResource::collection(ProviderPlatform::all(['*']));
    }
    //
    // List information on a specific provider platform
    // ****************************************************
    // GET: /api/provider_platforms/{request_body}
    // @param Request $request
    // @return JsonResponse
    // ****************************************************
    public function show(Request $request, $id): ProviderPlatformResource|\Illuminate\Http\JsonResponse
    {
        $providerPlatform = ProviderPlatform::where('id', $id)->first();
        if ($providerPlatform) {
            return new ProviderPlatformResource($providerPlatform);
        } else {
            return V1INVALID_REQUEST_BODY;
        }
    }
    // Create a new provider platform (does not register connection)
    // ****************************************************
    //  POST: /api/provider_platform/{request_body}
    //  Request $request
    // ****************************************************
    public function store(StoreProviderPlatformRequest $req): ProviderPlatformResource
    {
        try {
            $validated = $req->validated();
        } catch (\Exception) {
            return INVALID_REQUEST_BODY;
        }
        $provider = ProviderPlatform::create($validated);
        // Create a new platform connection.
        return new ProviderPlatformResource($provider);
    }

    // Update a provider platform
    // ****************************************************
    // PUT: /api/provider_platform/{request_body}
    // @param Request $request
    // @return JsonResponse
    // Request $request
    // ****************************************************
    public function update(StoreProviderPlatformRequest $request)
    {
        $validated = $request->validated();
        $providerPlatform = ProviderPlatform::where($validated)->first();
        if (!$providerPlatform) {
            return response()->json(['error' => 'Invalid provider ID'], 401);
        } else {
            $providerPlatform->update($validated);
            return new ProviderPlatformResource($providerPlatform);
        }
    }

    // Delete a provider platform
    // ****************************************************
    // DELETE: /api/provider_platform/{request_body}
    // Request $req example:
    // { "provider_id": 1 }
    // ****************************************************
    public function destroy(Request $request, $providerId): \Illuminate\Http\JsonResponse
    {
        $providerPlatform = ProviderPlatform::where('id', $providerId)->first();
        if (!$providerPlatform) {
            return response()->json(['error' => 'Invalid provider ID'], 401);
        } else {
            $providerPlatform->delete();
            return response()->json(json_encode($providerPlatform));
        }
    }
}

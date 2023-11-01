<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ProviderPlatform;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\StoreProviderPlatformRequest;
use App\Http\Requests\ShowProviderPlatformRequest;
use App\Http\Resources\ProviderPlatformResource;

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
    public function show(ShowProviderPlatformRequest $request, $id): ProviderPlatformResource|\Illuminate\Http\JsonResponse
    {
        $providerPlatform = ProviderPlatform::where('id', $id)->first();
        if ($providerPlatform) {
            return new ProviderPlatformResource($providerPlatform);
        } else {
            return response()->json(INVALID_REQUEST_BODY, 401);
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
            return response()->json(INVALID_REQUEST_BODY, 401);
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

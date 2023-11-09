<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ProviderPlatform;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\StoreProviderPlatformRequest;
use App\Http\Resources\ProviderPlatformResource;

class ProviderPlatformController extends Controller
{

    // List all provider platforms known to the system
    // ****************************************************
    // GET: /api/provider_platforms/
    // ****************************************************
    public function index()
    {
        return ProviderPlatformResource::collection(ProviderPlatform::all());
    }


    // List information on a specific provider platform
    // ****************************************************
    // GET: /api/v1/provider_platforms/{id}
    // @param Request $request
    // @return JsonResponse
    // ****************************************************
    public function show(string $id): ProviderPlatformResource|\Illuminate\Http\JsonResponse
    {
        $providerPlatform = ProviderPlatform::where('id', $id)->get()->first();

        if ($providerPlatform) {
            return ProviderPlatformResource::make($providerPlatform);
        } else {
            return response()->json(INVALID_REQUEST_BODY, 401);
        }
    }

    // Create a new provider platform
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
        $provider = new ProviderPlatform($validated);
        $provider->save();
        // Create a new platform connection.
        return new ProviderPlatformResource($provider);
    }

    // Update a provider platform (require all fields in JSON request body)
    // ****************************************************
    // PUT: /api/provider_platform/{request_body}
    // @param Request $request
    // @return JsonResponse
    // Request $request
    // ****************************************************
    public function update(StoreProviderPlatformRequest $request): ProviderPlatformResource|\Illuminate\Http\JsonResponse
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

    // Delete a provider platform (use id from path parameter, not request body)
    // ****************************************************
    // DELETE: /api/provider_platform/{id}
    // Request $req example:
    // { "provider_id": 1 }
    // ****************************************************
    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        $providerPlatform = ProviderPlatform::where('id', $id)->first();
        if (!$providerPlatform) {
            return response()->json(['error' => 'Invalid provider ID'], 401);
        } else {
            $providerPlatform->delete();
            return response()->json(json_encode($providerPlatform));
        }
    }
}

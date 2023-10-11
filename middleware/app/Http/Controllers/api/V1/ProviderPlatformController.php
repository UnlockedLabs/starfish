<?php

namespace App\Http\Controllers\api\V1;

use App\Models\ProviderPlatform;
use Illuminate\Http\Request;
use ProviderPlatformServices;
use App\Http\Controllers\Controller;

class ProviderPlatformController extends Controller
{
    // List all provider platforms known to the system
    // ****************************************************
    // GET: /api/provider_platform/
    // ****************************************************
    public function index()
    {
        return response()->json(json_encode(ProviderPlatform::all()));
    }
    // List information on a specific provider platform
    // ****************************************************
    // GET: /api/provider_platform/{request_body}
    // @param Request $request
    // @return JsonResponse
    //
    // Request $req example:
    // { "provider_id": 1 }
    // ****************************************************
    public function show(Request $request)
    {
        $providerPlatform = ProviderPlatform::where('id', $request->input('provider_id'))->first();
        if (!$providerPlatform) {
            return response()->json(['error' => 'Invalid provider ID'], 401);
        } else {
            return response()->json(json_encode($providerPlatform));
        }
    }
    // Create a new provider platform (does not register connection)
    // ****************************************************
    //  POST: /api/provider_platform/{request_body}
    // Request $req example:
    //
    // { "type": "canvas", "account_id": 123, "account_name": "WashU",
    // "access_key": "12u3n4gh3k69jj21k_gh27", "base_url": "https://canvas.instructure.com",
    // "iconUrl": "https://canvas.instructure.com/favicon.ico", "comsumer_id": 1 }
    //
    // ****************************************************
    public function store(Request $req): \Illuminate\Http\JsonResponse
    {
        try {
            $consumer_id = $req->input('consumer_id');
            $type = $req->input('type');
            $account_id = $req->input('account_id');
            $account_name = $req->input('account_name');
            $access_key = $req->input('access_key');
            $base_url = $req->input('base_url');
            $icon_url = $req->input('iconUrl');
        } catch (\Exception) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }

        // Create a new provider in the database, register the connection and return the provider ID
        $provider = ProviderPlatformServices::createProviderPlatform($type, $account_id, $account_name, $access_key, $base_url, $icon_url, $consumer_id);

        // Create a new platform connection.
        return response()->json($provider);
    }
    // Update a provider platform
    // ****************************************************
    // PUT: /api/provider_platform/{request_body}
    // @param Request $request
    // @return JsonResponse
    // Request $req example:
    // { "provider_id": 1, "account_id": 123, "account_name": "WashU",
    // "access_key": "12u3n4gh3k69jj21k_gh27", "base_url": "https://canvas.instructure.com",
    // "iconUrl": "https://canvas.instructure.com/favicon.ico" }
    // ****************************************************
    //
    public function update(Request $request)
    {
        if (!$request->input('provider_id')) {
            return response()->json(['error' => 'Invalid provider ID'], 401);
        }
        $providerPlatform = ProviderPlatform::where('id', $request->input('provider_id'))->first();
        if (!$providerPlatform) {
            return response()->json(['error' => 'Invalid provider ID'], 401);
        } else {
            $providerPlatform->update($request->all());
            return response()->json(json_encode($providerPlatform));
        }
    }

    // Delete a provider platform
    // ****************************************************
    // DELETE: /api/provider_platform/{request_body}
    // Request $req example:
    // { "provider_id": 1 }
    // ****************************************************
    public function destroy(Request $request)
    {
        if (!$request->input('provider_id')) {
            return response()->json(['error' => 'Invalid provider ID'], 401);
        }
        $providerPlatform = ProviderPlatform::where('id', $request->input('provider_id'))->first();
        if (!$providerPlatform) {
            return response()->json(['error' => 'Invalid provider ID'], 401);
        } else {
            $providerPlatform->delete();
            return response()->json(json_encode($providerPlatform));
        }
    }
}

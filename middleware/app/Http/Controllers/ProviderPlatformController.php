<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ProviderServices;

class ProviderPlatformController extends Controller
{
    // ****************************************************
    //  POST: /api/provider_platform/{request_body}
    // Request $req example:
    //
    // { "type": "canvas", "account_id": 123, "account_name": "WashU",
    // "access_key": "12u3n4gh3k69jj21k_gh27", "base_url": "https://canvas.instructure.com",
    // "iconUrl": "https://canvas.instructure.com/favicon.ico", "comsumer_id": 1 }
    //
    // ****************************************************
    public function registerProviderConnection(Request $req): \Illuminate\Http\JsonResponse
    {
        try {
            $consumer_id = $req->input('consumer_id');
            $type = $req->input('type');
            $account_id = $req->input('account_id');
            $account_name = $req->input('account_name');
            $access_key = $req->input('access_key');
            $base_url = $req->input('base_url');
            $icon_url = $req->input('iconUrl');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }

        // Create a new provider in the database, register the connection and return the provider ID
        $provider = ProviderServices::registerPlatformProvider($type, $account_id, $account_name, $access_key, $base_url, $icon_url, $consumer_id);

        // Create a new platform connection.
        return response()->json($provider);
    }
}

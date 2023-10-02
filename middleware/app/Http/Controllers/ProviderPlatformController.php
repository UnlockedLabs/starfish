<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlatformConnection;

class ProviderPlatformController extends Controller
{
    // ****************************************************
    //  POST: /api/provider_platform/{provider}
    // ****************************************************
    public function registerProvider(Request $req): \Illuminate\Http\JsonResponse
    {
        $consumer_id = $req->input('consumer_id');
        $type = $req->input('type');
        $account_id = $req->input('account_id');
        $account_name = $req->input('account_name');
        $access_key = $req->input('access_key');
        $base_url = $req->input('base_url');
        $icon_url = $req->input('iconUrl');

        // Create a new provider, return the provider ID
        $provider = new \ProviderUtil($type, $account_id, $account_name, $access_key, $base_url, $icon_url);

        // Create a new platform connection.
        new PlatformConnection($consumer_id, $provider->getProviderId());

        return response()->json($provider->getProviderId());
    }
}

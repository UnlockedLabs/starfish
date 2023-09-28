<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProviderPlatformController extends Controller
{
    // ****************************************************
    // response to POST: /api/providers/{provider}
    // ****************************************************
    public function registerProvider(Request $req): \Illuminate\Http\JsonResponse
    {
        $Type = $req->input('type');
        $AccountId = $req->input('account_id');
        $AccountName = $req->input('account_name');
        $access_key = $req->input('access_key');
        $base_url = $req->input('base_url');
        $IconUrl = $req->input('iconUrl');

        // Create a new provider, return the provider ID
        $provider = new \ProviderUtil($Type, $AccountId, $AccountName, $access_key, $base_url, $IconUrl);
        return response()->json($provider->getProviderId());
    }
}

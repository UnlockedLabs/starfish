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
        $AccessKey = $req->input('access_key');
        $BaseUrl = $req->input('base_url');
        $IconUrl = $req->input('iconUrl');
        // Here we get register the provdider into our database, and generate/get the UUID
        // for the provider
        $provider = new \ProviderUtil($Type, $AccountId, $AccountName, $AccessKey, $BaseUrl, $IconUrl);
        return response()->json($provider->getProviderId());
    }
}

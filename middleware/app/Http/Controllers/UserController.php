<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Utilities\CanvasUtil;
use App\Utilities\ProviderUtil;

class UserController extends Controller
{
    public function returnUserData(Request $req): \Illuminate\Http\JsonResponse
    {
        $provider = \ProviderUtil::getProvider($req);
        $user = $provider->getUser($req->input('id'));
        return response()->json($user);
    }

    public function getUserData(Request $request, $userid)
    {
        // Get data from the request
        $accountId = $request->input('account_id');
        $url = $request->input('url'); 
        $instanceId = $request->input('instance_id'); 
        $type = $request->input("type");
        match($type) {
            "cloud" => $type = CanvasType::CLOUD,
            _ => $type = CanvasType::OSS,
        };

        $canvasUtil = new \CanvasUtil($accountId, $url, CanvasType::CLOUD, $instanceId);

        try {
            $userData = $canvasUtil->listUsers($userid);

            // Process $userData as needed

            // Return a JSON response
            return response()->json($userData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

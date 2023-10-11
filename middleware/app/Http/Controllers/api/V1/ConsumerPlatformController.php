<?php

declare(strict_types=1);

namespace App\Http\Controllers\api\V1;

use App\Models\ConsumerPlatform;
use ConsumerPlatformServices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConsumerPlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET: /api/consumer_platforms/
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json(json_encode(ConsumerPlatform::all()));
    }

    /**
     * Show the form for creating a new resource.
     * POST /api/consumer_platforms/
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * example request:
     * { "type": "unlocked_v1", "name": "MDOC_v1", "api_key": abcdefghijklm1234345,
     * "base_url": "http://unlocked_labs.v1.com",
     * }
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $type = $request->input('type');
            $name = $request->input('name');
            $api_key = $request->input('api_key');
            $base_url = $request->input('base_url');
        } catch (\Exception) {
            return response()->json(['error' => 'invalid request'], 400);
        }
        $consumerPlatform = ConsumerPlatformServices::createConsumerPlatform($type, $name, $api_key, $base_url);
        return response()->json($consumerPlatform);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $id = $request->input('id');
        } catch (\Exception) {
            return response()->json(['error' => 'invalid request'], 400);
        }
        try {
            $consumerPlatform = ConsumerPlatform::where('id', $id)->first();

            return response()->json($consumerPlatform);
        } catch (\Exception) {
            return response()->json(['error' => 'consumer platform not found'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request): \Illuminate\Http\JsonResponse
    {
        $consumerPlatform = ConsumerPlatform::where('id', $request->input('id'))->first();
        if ($consumerPlatform === null) {
            return response()->json(['error' => 'consumer platform not found'], 404);
        }
        $consumerPlatform->type = $request->input('type');
        $consumerPlatform->name = $request->input('name');
        $consumerPlatform->api_key = $request->input('api_key');
        $consumerPlatform->base_url = $request->input('base_url');
        $consumerPlatform->save();
        return response()->json(['success' => $consumerPlatform], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ConsumerPlatform $consumerPlatform)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {
        $consumerPlatform = ConsumerPlatform::where('id', $request->input('id'))->first();
        if ($consumerPlatform === null) {
            return response()->json(['error' => 'consumer platform not found'], 404);
        } else {
            $consumerPlatform->delete();
        }
        return response()->json(['success' => 'consumer platform deleted'], 200);
    }
}

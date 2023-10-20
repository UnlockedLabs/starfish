<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\ConsumerPlatform;
use Illuminate\Http\Request;
use App\Http\Requests\StoreConsumerPlatformRequest;
use App\Http\Requests\UpdateConsumerPlatformRequest;
use App\Http\Resources\ConsumerPlatformResource;
use App\Http\Controllers\Api\V1\Controller;

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
     * @param StoreConsumerPlatformRequest $request
     * @return ConsumerPlatformResource
     */
    public function store(StoreConsumerPlatformRequest $request): ConsumerPlatformResource
    {
        try {
            $validated = $request->validated();
            $cp = ConsumerPlatform::create($validated);
        } catch (\Exception) {
            return response()->json(['error' => 'invalid request'], 400);
        }
        return new ConsumerPlatformResource($cp);
    }

    /**
     * Display the specified resource.
     * GET /api/v1/consumer_platforms/{id}
     * @param string $id
     * @return ConsunmerPlatformResource
     */
    public function show($id): ConsumerPlatformResource
    {
        try {
            $consumerPlatform = ConsumerPlatform::where('id', $id)->first();
            return new ConsumerPlatformResource($consumerPlatform);
        } catch (\Exception) {
            return response()->json(['error' => 'consumer platform not found'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UpdateConsumerPlatformRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $consumerPlatform = ConsumerPlatform::where('id', $validated['id'])->first();
        if ($consumerPlatform === null) {
            return response()->json(['error' => 'consumer platform not found'], 404);
        }
        $consumerPlatform->update(['type' => $validated['type'], 'api_key' => $validated['api_key'], 'name' => $validated['name'], 'base_url' => $validated['base_url']]);
        return response()->json(['success' => $consumerPlatform], 200);
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

<?php

declare(strict_types=1);


namespace App\Http\Controllers\Api\V1;

use App\Models\ConsumerPlatform;
use Illuminate\Http\Request;
use App\Http\Requests\StoreConsumerPlatformRequest;
use App\Http\Requests\UpdateConsumerPlatformRequest;
use App\Http\Resources\ConsumerPlatformResource;
use App\Http\Controllers\Api\V1\Controller;

const INVALID_REQUEST_BODY = response()->json(['error' => 'Invalid request body'], 401);
const PLATFORM_NOT_FOUND = response()->json(['error' => 'consumer platform not found'], 404);

class ConsumerPlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET: /api/consumer_platforms/
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return ConsumerPlatformResource::collection(ConsumerPlatform::all(['*']));
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
            return INVALID_REQUEST_BODY;
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
            return PLATFORM_NOT_FOUND;
        }
    }

    /**
     * Update the specified resource in storage
     * PATCH /api/v1/consumer_platforms/{id}
     * @param UpdateConsumerPlatformRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function edit(UpdateConsumerPlatformRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $consumerPlatform = ConsumerPlatform::where('id', $validated['id'])->first();
        if ($consumerPlatform === null) {
            return PLATFORM_NOT_FOUND;
        }
        $consumerPlatform->update($validated);
        return response()->json(['success' => $consumerPlatform], 200);
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/v1/consumer_platforms/{id}
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {
        $consumerPlatform = ConsumerPlatform::where('id', $request->input('id'))->first();
        if ($consumerPlatform === null) {
            return PLATFORM_NOT_FOUND;
        } else {
            $consumerPlatform->delete();
        }
        return response()->json(['success' => 'consumer platform deleted'], 200);
    }
}

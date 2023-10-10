<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsumerPlatformRequest;
use App\Http\Requests\UpdateConsumerPlatformRequest;
use App\Http\Resources\ConsumerPlatformResource;
use App\Models\ConsumerPlatform;

class ConsumerPlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ConsumerPlatformResource::collection(ConsumerPlatform::all());

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConsumerPlatformRequest $request)
    {
        $consumerPlatform = ConsumerPlatform::create($request->validated());

        return ConsumerPlatformResource::make($consumerPlatform);
    }

    /**
     * Display the specified resource.
     */
    public function show(ConsumerPlatform $consumerPlatform)
    {
        return ConsumerPlatformResource::make($consumerPlatform);
    }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(ConsumerPlatform $consumerPlatform)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConsumerPlatformRequest $request, ConsumerPlatform $consumerPlatform)
    {
        $consumerPlatform->update($request->validated());

        return ConsumerPlatformResource::make($consumerPlatform);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConsumerPlatform $consumerPlatform)
    {
        //
    }
}

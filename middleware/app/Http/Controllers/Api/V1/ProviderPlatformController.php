<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProviderPlatformRequest;
use App\Http\Requests\UpdateProviderPlatformRequest;
use App\Http\Resources\ProviderPlatformResource;
use App\Models\ProviderPlatform;

class ProviderPlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProviderPlatformResource::collection(ProviderPlatform::all());

        // return ProviderPlatform::all();
    }

    public function hello(string $message)
    {
        return $message;
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
    public function store(StoreProviderPlatformRequest $request)
    {
        $providerPlatform = ProviderPlatform::create($request->validated());

        return ProviderPlatformResource::make($providerPlatform);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProviderPlatform $providerPlatform)
    {
        return ProviderPlatformResource::make($providerPlatform);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProviderPlatform $providerPlatform)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProviderPlatformRequest $request, ProviderPlatform $providerPlatform)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProviderPlatform $providerPlatform)
    {
        //
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlatformConnectionRequest;
use App\Http\Requests\UpdatePlatformConnectionRequest;
use App\Http\Resources\PlatformConnectionResource;
use App\Models\PlatformConnection;

class PlatformConnectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PlatformConnectionResource::collection(PlatformConnection::all());
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
    public function store(StorePlatformConnectionRequest $request)
    {
        $platformConnection = PlatformConnection::create($request->validated());

        return PlatformConnectionResource::make($platformConnection);
    }

    /**
     * Display the specified resource.
     */
    public function show(PlatformConnection $platformConnection)
    {
        return PlatformConnectionResource::make($platformConnection);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlatformConnection $platformConnection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlatformConnectionRequest $request, PlatformConnection $platformConnection)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlatformConnection $platformConnection)
    {
        //
    }
}

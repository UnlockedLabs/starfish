<?php

use App\Http\Controllers\Api\V1\ChangePlatformConnectionStateController;
use App\Http\Controllers\Api\V1\ConsumerPlatformController;
use App\Http\Controllers\Api\V1\PlatformConnectionController;
use App\Http\Controllers\Api\V1\ProviderPlatformController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::get('/{consumer_platform_id}/provider_platforms', [ProviderPlatformController::class, 'hello']);
    Route::apiResource('/provider_platforms', ProviderPlatformController::class);
    Route::apiResource('/consumer_platforms', ConsumerPlatformController::class);
    Route::apiResource('/platform_connections', PlatformConnectionController::class);
    Route::get('/{consumer_platform_id}/platform_connections', [PlatformConnectionController::class, 'showAllProvidersForConsumer']);
    Route::patch('/platform_connections/{platform_connection}/change_connection_state', ChangePlatformConnectionStateController::class);
});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

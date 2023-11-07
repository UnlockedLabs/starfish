<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProviderPlatformController;
use App\Http\Controllers\Api\V1\ConsumerPlatformController;
use App\Http\Controllers\Api\V1\PlatformConnectionController;
use App\Http\Controllers\Api\V1\StudentEnrollmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::apiResource('provider_platforms', ProviderPlatformController::class);
    Route::apiResource('consumer_platforms', ConsumerPlatformController::class);
    Route::apiResource('platform_connections', PlatformConnectionController::class);

    Route::get('students/{id}/courses', [StudentEnrollmentController::class, 'show']);
    Route::patch('students/{id}/courses', [StudentEnrollmentController::class, 'update']);
});

<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/provider_users/{user_id}/courses', [App\Http\Controllers\ProviderUserController::class, 'getProviderUserResources']);
Route::post('/provider_platforms', [App\Http\Controllers\ProviderPlatformController::class, 'registerProvider']);

// Naming scheme for routes needs to be decided

// get all courses fo all users
Route::get('/api/courses', [App\Http\Controllers\ProviderUserResourceController::class, 'index']);
// add course for a specific user
Route::post('api/users/{user_id}/courses', [App\Http\Controllers\ProviderUserResourceController::class, 'store']);
// delete a course for a specific user
Route::delete('api/users/{user_id}/courses', [App\Http\Controllers\ProviderUserResourceController::class, 'destroy']);
// show all courses for a specific user
Route::get('api/v1/users/{user_id}/courses/', [App\Http\Controllers\ProviderUserResourceController::class, 'show']);

// get all provider platforms
Route::get('api/provider_platforms', [App\Http\Controllers\ProviderPlatformController::class, 'index']);
// create new provdider platform
Route::post('api/provider_platforms', [App\Http\Controllers\ProviderPlatformController::class, 'store']);
// get specific provider platform
Route::get('api/provider_platforms/{provider_id}', [App\Http\Controllers\ProviderPlatformController::class, 'show']);
// delete a provider platform
Route::delete('api/provider_platforms/{provider_id}', [App\Http\Controllers\ProviderPlatformController::class, 'destroy']);
// update a provider platform
Route::put('api/provider_platforms/{provider_id}', [App\Http\Controllers\ProviderPlatformController::class, 'update']);

// get all consumer platforms
Route::get('api/consumer_platforms', [App\Http\Controllers\ConsumerPlatformController::class, 'index']);
// get specified consumer platform
Route::get('api/consumer_platforms/{consumer_id}', [App\Http\Controllers\ConsumerPlatformController::class, 'show']);
// create new consumer platform (does not register a connection)
Route::post('api/consumer_platforms/', [App\Http\Controllers\ConsumerPlatformController::class, 'store']);
// delete a consumer platform
Route::delete('api/consumer_platforms/{consumer_id}', [App\Http\Controllers\ConsumerPlatformController::class, 'destroy']);
// update an existing consumer platform
Route::put('api/consumer_platforms/{consumer_id}', [App\Http\Controllers\ConsumerPlatformController::class, 'update']);

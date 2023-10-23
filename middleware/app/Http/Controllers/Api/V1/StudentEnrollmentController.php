<?php

declare(strict_types=1);

namespace App\Http\Controllers\api\V1;

use App\Models\StudentEnrollment;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\StudentEnrollmentRequest;
use App\Http\Resources\StudentEnrollmentResource;
use App\Enums\StudentEnrollmentStatus;
use App\Models\ProviderContent;
use App\Http\Requests\StoreProviderContentRequest;
use App\Http\Requests\ProviderContentRequest;

class StudentEnrollmentController extends Controller
{
    public function show(StudentEnrollmentRequest $request): StudentEnrollmentResource
    {
        $validated = $request->validated();
        $enrollments = StudentEnrollment::where('user_id', $validated['provider_user_id'])->and('status', StudentEnrollmentStatus::IN_PROGRESS)->get();
        return new StudentEnrollmentResource($enrollments);
    }

    // Get all courses for all users (Probably not needed?)
    // GET: /api/courses/
    // ****************************************************
    // @param Request $request
    // @return Illuminate\Http\JsonResponse
    // ****************************************************
    public function index(): \Illuminate\Http\JsonResponse
    {
        $providerUserResources = ProviderContent::all();
        return response()->json(json_encode($providerUserResources));
    }
    // Add a course to a user's account
    // ****************************************************
    // POST: /api/users/{user_id}/courses/{request_body}
    // @param Request $request
    // @return Illuminate\Http\JsonResponse
    // ****************************************************
    // Request $req example:
    // { "provider_id": 1, "provider_resource_id": 1, "user_id": 1 }
    public function store(StoreProviderContentRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();
        } catch (\Exception) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
        $providerContent = ProviderContent::create([$validated]);
        return response()->json($providerContent);
    }

    // Remove a course from a user's account
    // ****************************************************
    // DELETE: /api/users/{user_id}/courses/{request_body}
    // @param Request $request
    // @return Illuminate\Http\JsonResponse
    // ****************************************************
    // Request $req example:
    // { "provider_id": 1, "provider_resource_id": 1, "user_id": 1 }
    public function destroy(ProviderContentRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $providerId = $request->input('provider_id');
            $providerResourceId = $request->input('provider_resource_id');
            $userId = $request->input('user_id');
        } catch (\Exception) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
        $providerUserResource = ProviderContent::where('provider_id', $providerId)
            ->where('provider_resource_id', $providerResourceId)
            ->where('user_id', $userId)
            ->first();
        if (!$providerUserResource) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
        $providerUserResource->delete();
        return response()->json(['success' => 'true']);
    }

    // Changes the status of a course for a user
    // ****************************************************
    // PUT: /api/users/{user_id}/courses/{request_body}
    // @param Request $request
    // @return Illuminate\Http\JsonResponse
    // ****************************************************
    // Request $req example:
    // { "provider_id": 1, "provider_resource_id": 1, "user_id": 1 }
    public function edit(StoreProviderContentRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();
        } catch (\Exception) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
        $content = ProviderContent::where('provider_id', $validated['provider_id'])
            ->where('provider_resource_id', $validated['provider_resource_id'])
            ->where('user_id', $validated['user_id'])
            ->first();

        if (!$content) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
        $content->status = $validated['status'];
        $content->save();
        return response()->json(['success' => 'true']);
    }
}

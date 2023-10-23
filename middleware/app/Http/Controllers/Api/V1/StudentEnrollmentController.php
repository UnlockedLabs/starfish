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

class StudentEnrollmentController extends Controller
{
    // Show all courses for a user
    //*****************************************************
    // GET: /api/v1/students/{student_id}/courses
    // @param StudentEnrollmentRequest $request
    // @return StudentEnrollmentResource
    // ****************************************************
    public function show(StudentEnrollmentRequest $request): StudentEnrollmentResource
    {
        $validated = $request->validated();
        $enrollments = StudentEnrollment::where('user_id', $validated['provider_user_id'])->and('status', StudentEnrollmentStatus::IN_PROGRESS)->get();
        return new StudentEnrollmentResource($enrollments);
    }

    // Changes the status of a course for a user
    // ****************************************************
    // PUT: /api/v1/students/{student_id}/courses/{request_body}
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

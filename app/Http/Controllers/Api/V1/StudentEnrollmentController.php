<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\StudentEnrollment;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Resources\StudentEnrollmentResource;
use App\Http\Requests\UpdateStudentEnrollmentRequest;
use App\Models\StudentMapping;

class StudentEnrollmentController extends Controller
{
    // Show all courses for a user
    //*****************************************************
    // GET: /api/v1/students/{student_id}/courses
    // @param StudentEnrollmentRequest $request
    // @return StudentEnrollmentResource
    // ****************************************************
    public function show(string $id)
    {
        $mapping = StudentMapping::where('student_id', $id)->get();
        $enrollments = StudentEnrollment::where('provider_user_id', $mapping->provider_user_id)
            ->where('provider_platform_id', $mapping->provider_platform_id)->where('status', 'in_progress')->all();

        return StudentEnrollmentResource::collection($enrollments);
    }

    // Changes the status of a course for a user
    // ****************************************************
    // PUT: /api/v1/students/{student_id}/courses/{request_body}
    // @param UpdateStudentEnrollmentRequest $request
    // @return Illuminate\Http\JsonResponse
    // ****************************************************
    public function edit(string $id, UpdateStudentEnrollmentRequest $request): \Illuminate\Http\JsonResponse
    {
        $student = StudentMapping::where('student_id', $id);
        $validated = $request->validated();
        $mapping = StudentEnrollment::where('provider_user_id', $student->provider_user_id)
            ->where('provider_platform_id', $student->provider_platform_id)
            ->where('provider_content_id', $validated['provider_content_id'])->first();

        if (!$mapping) {
            return response()->json(INVALID_REQUEST_BODY, 401);
        }
        $mapping->status = $validated['status'];
        $mapping->save();
        return response()->json(['success' => 'true'], 200);
    }
}

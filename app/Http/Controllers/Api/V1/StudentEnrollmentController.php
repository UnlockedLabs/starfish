<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\StudentEnrollment;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Resources\StudentEnrollmentResource;
use App\Enums\StudentEnrollmentStatus;
use App\Models\ProviderContent;
use App\Http\Requests\UpdateStudentEnrollmentRequest;

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
        $enrollments = StudentEnrollment::where('id', $id)->get();
        return StudentEnrollmentResource::collection($enrollments);
    }

    // Changes the status of a course for a user
    // ****************************************************
    // PUT: /api/v1/students/{student_id}/courses/{request_body}
    // @param UpdateStudentEnrollmentRequest $request
    // @return Illuminate\Http\JsonResponse
    // ****************************************************
    public function edit(UpdateStudentEnrollmentRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();
        } catch (\Exception) {
            return response()->json(INVALID_REQUEST_BODY, 401);
        }
        $content = ProviderContent::where(
            'provider_id',
            $validated['provider_id']
        )
            ->where('provider_resource_id', $validated['provider_resource_id'])
            ->where('provider_user_id', $validated['provider_user_id'])
            ->first();

        if (!$content) {
            return response()->json(INVALID_REQUEST_BODY, 401);
        }
        $content->status = $validated['status'];
        $content->save();
        return response()->json(['success' => 'true'], 200);
    }
}

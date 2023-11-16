<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\StudentEnrollment;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Resources\StudentEnrollmentResource;
use App\Http\Requests\UpdateStudentEnrollmentRequest;
use App\Models\StudentMapping;
use Illuminate\Support\Facades\Log;

class StudentEnrollmentController extends Controller
{
    // Show all courses for a user
    //*****************************************************
    // GET: /api/v1/students/{student_id}/courses
    // @param string $id
    // @return StudentEnrollmentResource::Collection
    // ****************************************************
    public function show(string $id): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $mapping = StudentMapping::where('consumer_user_id', $id)->first();
        Log::info($mapping);
        $provider_user_id = $mapping->provider_user_id;
        $enrollments = StudentEnrollment::where('provider_user_id', $provider_user_id)->get();
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
        // get the student mapping, all the consumer has will be the consumer_user_id
        // remember, this could return lots of different mappings!
        $validated = $request->validated();
        $enrollment = StudentEnrollment::where('provider_content_id', $validated['provider_content_id'])->first();
        $enrollment->update($validated);
        $enrollment->save();
        return response()->json(['success' => 'true'], 200);
    }
}

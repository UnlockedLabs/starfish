<?php

declare(strict_types=1);

namespace App\Http\Controllers\api\V1;

use App\Models\ProviderPlatform;
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
    // This is the cache refresh function (makes API calls directly)
    // ****************************************************
    // route::get('/api/users/{user_id}/courses')
    //*****************************************************
    // @param Request $request
    // @return Illuminate\Http\JsonResponse
    // ****************************************************

    public function refreshEnrollmentCahce(StudentEnrollmentRequest $request): \Illuminate\Http\JsonResponse
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['error' => 'Missing user_id'], 400);
        }
        // Get all the available provider IDs
        $providerIds = ProviderPlatform::select('id')->get()->toArray();
        $links = [];

        if (!count($providerIds)) {
            return response()->json(['error' => 'There are no registerd providers for this student ID'], 400);
        } else {
            foreach ($providerIds as $id) {
                // Each iteration will instantiate a new CanvasUtil object
                // and query the Canvas API for the user's courses
                $canvasUtil = \CanvasServices::byProviderId($id);
                $enrollments = $canvasUtil->listCoursesForUser($userId);
                // Obviously we would just query the DB and return the enrollments for the user,
                // but for the prototype we demonstrate how the information is fetched.
                $links = [];
                foreach ($enrollments as $course) {
                    // Create the LTI deep linking JSON structure
                    $link = $canvasUtil->formatLtiLinking($course);
                    // append each resource link
                    $links[] = $link;
                }
                if (empty($links)) {
                    return response()->json(['error' => 'No Provider Resources Found'], 400);
                }
                // Wrap the links in a container object if needed
                $response = ['@context' => 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem', 'items' => $links];
                return response()->json($response);
            }
        }
        return response()->json(['error' => 'invalid request body'], 400);
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

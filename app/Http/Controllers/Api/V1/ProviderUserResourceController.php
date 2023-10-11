<?php

declare(strict_types=1);
/*
 * ProviderResource - Course

* UserResource - Enrollment
*/

namespace App\Http\Controllers\api\V1;

use Illuminate\Http\Request;
use App\Models\ProviderPlatform;
use App\Models\ProviderUserResource;
use App\Http\Controllers\Controller;

class ProviderUserResourceController extends Controller
{
    // This is the cache refresh function (makes API calls directly)
    // ****************************************************
    // route::get('/api/users/{user_id}/courses')
    //*****************************************************
    // @param Request $request
    // @return Illuminate\Http\JsonResponse
    // ****************************************************
    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['error' => 'Missing user_id'], 400);
        }
        // Get all the available provider IDs
        $providerIds = ProviderPlatform::select('id')->get()->toArray();
        $links = [];

        if (!count($providerIds)) {
            // Here is where we would
            return response()->json(['error' => 'There are no registerd providers for this student ID'], 400);
        } else {
            foreach ($providerIds as $id) {
                // Each iteration will instantiate a new CanvasUtil object
                // and query the Canvas API for the user's courses
                $canvasUtil = \CanvasServices::getByProviderId($id);
                $enrollments = $canvasUtil->listCoursesForUser($userId);
                // Obviously we would just query the DB and return the enrollments for the user,
                // but for the prototype we demonstrate how the information is fetched.
                $links = [];
                foreach ($enrollments as $course) {
                    // Create the LTI deep linking JSON structure
                    $link = \ProviderPlatformServices::formatLtiDeepLinkFromCanvasCourse($course, $canvasUtil->getBaseUrl());
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
        $providerUserResources = ProviderUserResource::all();
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
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $providerId = $request->input('provider_id');
            $providerResourceId = $request->input('provider_resource_id');
            $userId = $request->input('user_id');
        } catch (\Exception) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
        $providerUserResource = ProviderUserResource::create([
            'provider_id' => $providerId,
            'provider_resource_id' => $providerResourceId,
            'user_id' => $userId,
            'status' => 'incomplete'
        ]);
        return response()->json($providerUserResource);
    }
    // Remove a course from a user's account
    // ****************************************************
    // DELETE: /api/users/{user_id}/courses/{request_body}
    // @param Request $request
    // @return Illuminate\Http\JsonResponse
    // ****************************************************
    // Request $req example:
    // { "provider_id": 1, "provider_resource_id": 1, "user_id": 1 }
    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $providerId = $request->input('provider_id');
            $providerResourceId = $request->input('provider_resource_id');
            $userId = $request->input('user_id');
        } catch (\Exception) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
        $providerUserResource = ProviderUserResource::where('provider_id', $providerId)
            ->where('provider_resource_id', $providerResourceId)
            ->where('user_id', $userId)
            ->first();
        if (!$providerUserResource) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
        $providerUserResource->delete();
        return response()->json(['success' => 'true']);
    }
    //
    // Changes the status of a course for a user
    // ****************************************************
    // PUT: /api/users/{user_id}/courses/{request_body}
    // @param Request $request
    // @return Illuminate\Http\JsonResponse
    // ****************************************************
    // Request $req example:
    // { "provider_id": 1, "provider_resource_id": 1, "user_id": 1 }
    public function edit(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $providerId = $request->input('provider_id');
            $providerResourceId = $request->input('provider_resource_id');
            $userId = $request->input('user_id');
            $status = $request->input('status');
        } catch (\Exception) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
        $providerUserResource = ProviderUserResource::where('provider_id', $providerId)
            ->where('provider_resource_id', $providerResourceId)
            ->where('user_id', $userId)
            ->first();
        if (!$providerUserResource) {
            return response()->json(['error' => 'Invalid request body'], 401);
        }
        $providerUserResource->status = $status;
        $providerUserResource->save();
        return response()->json(['success' => 'true']);
    }
}

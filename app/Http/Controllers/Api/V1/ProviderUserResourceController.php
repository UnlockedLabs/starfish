<?php

/*
 * ProviderResource - Course

* UserResource - Enrollment
*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProviderPlatform;
use App\Models\ProviderUserResource;

class ProviderUserResourceController extends Controller
{
    // This is the cache refresh function
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
            // Here is where we would call a function to register the student in all downstream providers
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
                    $link = \ProviderServices::formatLtiDeepLinkFromCanvasCourse($course, $canvasUtil->getBaseUrl());
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

    // This would be the cached version of the above function to simply query the DB
    /* ****************************************************
    * route::get('/api/users/{user_id}/courses')
    * *****************************************************
    */
    public function showCached(Request $request): string | \InvalidArgumentException
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['error' => 'Missing user_id'], 400);
        }
        $enrollments = \App\Models\ProviderUserResource::where('user_id', $userId)->get();
        $links = [];
        foreach ($enrollments as $enrollment) {
            $links[] = \ProviderServices::
        }
        if (empty($links)) {
            return response()->json(['error' => 'No Courses Found'], 400);
        }
        $response = ['@context' => 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem', 'items' => $links];
        return response()->json($response);
    }
}

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

class ProviderUserController extends Controller
{
    // ****************************************************
    // route::get('/api/users/{user_id}/courses')
    //*****************************************************
    // Eventually we will establish some kind of caching system, possibly with REDIS.
    // storing the date of the last request, matching against the current date and
    // making sure we only perform these calls once a day.
    public function getProviderUserResources(Request $request): Illuminate\Http\JsonResponse
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['error' => 'Missing user_id'], 400);
        }
        // Get all the available provider IDs
        $providerIds = ProviderPlatform::getAllProviderIds();
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
                    $link = \ProviderServices::encodeDeepLinkingJson($course->id, $course->name, $canvasUtil->getBaseUrl());
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
    }

    // This would be the cached version of the above function
    /* ****************************************************
    * route::get('/api/users/{user_id}/courses')
    * *****************************************************
    * Eventually we will establish some kind of caching system, possibly with REDIS.
    * storing the date of the last request, matching against the current date and
    * making sure we only perform these calls once a day.
    */
    public function getUserCoursesCached(Request $request): string | \InvalidArgumentException
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['error' => 'Missing user_id'], 400);
        }
        $enrollments = \App\Models\ProviderUserResource::where('user_id', $userId)->get();
        $links = [];
        foreach ($enrollments as $enrollment) {
            $links[] = \ProviderServices::encodeDeepLinkingJson($enrollment->resource_id, $enrollment->resource_name, $enrollment->resource_url);
        }
        if (empty($links)) {
            return response()->json(['error' => 'No Courses Found'], 400);
        }
        $response = ['@context' => 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem', 'items' => $links];
        return response()->json($response);
    }
}

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
    public function getProviderUserResources(Request $request): \Illuminate\Http\JsonResponse
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
                foreach ($enrollments as $course) {
                    $courseId = $course->id;
                    $courseName = $course->name;
                    $canvasApiUrl = $canvasUtil->getBaseUrl() . "api/v1/courses/" . $courseId;

                    // Create the LTI deep linking JSON structure
                    $link = [
                        'type' => 'link',
                        'title' => $courseName,
                        'url' => $canvasApiUrl,
                    ];

                    $links[] = $link;
                }
            }
            if (empty($links)) {
                return response()->json(['error' => 'No Courses Found'], 400);
            }
            // Wrap the links in a container object if needed
            $response = ['@context' => 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem', 'items' => $links];

            return response()->json($response);
        }
    }

    public function getUserCoursesCached(Request $request): \Illuminate\Http\JsonResponse
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['error' => 'Missing user_id'], 400);
        }

        // Get all the available provider IDs
        $providerIds = ProviderPlatform::getAllProviderIds();
        $links = [];

        if (!count($providerIds)) {
            return response()->json(['error' => 'There are no registerd providers for this student ID'], 400);
        } else {
            foreach ($providerIds as $id) {
                $userResource = ProviderUserResource::where('user_id', $userId)->where('provider_id', $id)->first();
                // we need to query the UserResource table to see if we have courses cached for this user
                if ($userResource) {
                    // we have cached courses for this user, so we will use them

                    // Create the LTI deep linking JSON structure
                    $link = [
                        'type' => 'link',
                        'title' => $courseName,
                        'url' => $canvasApiUrl,
                    ];

                    $links[] = $link;
                }
            }
            if (empty($links)) {
                return response()->json(['error' => 'No Courses Found'], 400);
            }
            // Wrap the links in a container object if needed
            $response = ['@context' => 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem', 'items' => $links];

            return response()->json($response);
        }
    }
}

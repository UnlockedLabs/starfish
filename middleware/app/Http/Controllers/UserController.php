<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProviderPlatform;

class UserController extends Controller
{
    // ****************************************************
    // our response to GET: /api/user/{user_id}/courses
    //*****************************************************
    public function getUserCourses(Request $request): \Illuminate\Http\JsonResponse
    {
        $userId = $request->input('UserId');
        if (!$userId) {
            return response()->json(['error' => 'Missing UserId'], 400);
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
                $canvasUtil = \CanvasUtil::getByProviderId($id);
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
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Utilities\CanvasUtil\getByProviderId;
use App\Models\ProviderPlatform;
use Ramsey\Collection\Map\AssociativeArrayMap;

class UserController extends Controller
{
    // ****************************************************
    // response to POST: /api/providers/{provider}
    // ****************************************************
    public function registerProvider(Request $req): \Illuminate\Http\JsonResponse
    {
        $Type = $req->input('Type');
        $AccountId = $req->input('AccountId');
        $AccountName = $req->input('AccountName');
        $BaseUrl = $req->input('type');
        $AccessKey = $req->input('AccessKey');
        $url = $req->input('url');
        // Here we get register the provdider into our database, and generate/get the UUID
        // for the provider
        $provider = new \ProviderUtil($Type, $AccountId, $AccountName, $AccessKey, $BaseUrl);
        return response()->json($provider->getProviderId());
    }

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
            return response()->json(['error' => 'Multiple Providers Not Supported'], 400);
        } else {
            foreach ($providerIds as $id) {
                // Each iteration will instantiate a new CanvasUtil object
                // and query the Canvas API for the user's courses
                $canvasUtil = \CanvasUtil::getByProviderId($id);
                $enrollments = $canvasUtil->listCoursesForUser($userId);

                foreach ($enrollments as $course) {
                    $courseId = $course->id;
                    $courseName = $course->name;
                    $canvasApiUrl = $canvasUtil->getUrl() . "api/v1/courses/" . $courseId;

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

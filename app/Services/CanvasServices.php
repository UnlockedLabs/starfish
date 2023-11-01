<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Models\ProviderPlatform;
use Psr\Http\Message\ResponseInterface;
use App\Http\Requests\StudentEnrollmentRequest;

/**
 * Class CanvasServices
 * constants for canvas API routes
 */
const CANVAS_API = 'api/v1/';
const API_ERROR = 'API Request Error: ';
const USERS = 'users/';
const ACCOUNTS = 'accounts/';
const COURSES = 'courses/';
const ENROLLMENTS = 'enrollments/';
const ASSIGNMENTS = 'assignments/';
const SUBMISSIONS = 'submissions/';
const STUDENTS = 'students/';
const ACTIVITY_STREAM = 'activity_stream/';
const SELF = 'self/';
const PROGRESS = 'progress/';
const SECTIONS  = 'sections/';
const GRADEABLE_STUDENTS = 'gradeable_students/';
const READ = 'read/';
const ANONYMOUS_SUBMISSIONS = 'anonymous_submissions/';
use App\Models\ProviderUserResource;

class CanvasServices

{
    private int $provider_id;
    private int $account_id;
    private string $access_key;
    private string $base_url;

    private string $api_url;
    public Client $client;


    public function __construct(int $providerId, int $accountId, string $apiKey, string $url)
    {
        $this->provider_id = $providerId;
        $this->account_id = $accountId;
        $this->access_key =  $apiKey;
        $this->base_url = $url;

        $this->api_url = $url . CANVAS_API;
        $this->client = $this->getClient();
    }

    public function getAccountId(): int
    {
        return $this->account_id;
    }

    public function getAccessKey(): string
    {
        return $this->base_url;
    }
    public function getBaseUrl(): string
    {
        return $this->base_url;
    }


    /* Helper function to return an HTTP client set with access key
    * @return GuzzleHttp\Client
    */
    function getClient(): GuzzleHttp\Client
    {
        return new Client(["Headers" => ['Authorization' => 'Bearer' . $this->access_key]]);
    }

    /*
     * Formats a given Course object and returns a Canvas LTI Deep Link
    * @param object $course
    * @param string $baseUrl
    * @return array
     */
    public function formatLtiLinking($course): array
    {
        $link = [
            'type' => 'ltiResourceLink',
            'title' => $course->name,
            'url' => $this->base_url . '/courses/' . $course->id,
            'icon' => [
                'url' => 'https://www.instructure.com/favicon.ico',
                'width' => 50,
                'height' => 50
            ]
        ];
        return $link;
    }

    // Returns an instance of CanvasServices to make dynamic API calls.
    /*****************************************************
    //* @param int $providerId
    //* @return  CanvasServices
    //* @throws \InvalidArgumentException
     */
    public static function byProviderId(int $providerId): CanvasServices | \InvalidArgumentException
    {
        $provider = ProviderPlatform::where('id', $providerId)->first();
        if (!$provider) {
            throw new \InvalidArgumentException('Invalid provider ID');
        }
        return new self($provider->provider_id, $provider->account_id, $provider->access_key, $provider->base_url);
    }

    /**
     * validate and format the account ID parameter for API URLs
    // Turns a canvas specific course into a LTI deep linking JSON structure
    /***************************************************************
     * @param string $courseId
     * @param string $courseName
     * @param string $url
     * @return string
    /* Example response:
    {
        "@context": "http://purl.imsglobal.org/ctx/lti/v1/ContentItem",
        "items": [
            {   "type": "link",
                "title": "Course Name",
                "url": "https://canvas.instructure.com/api/v1/courses/123456" }]}
     */
    public static function encodeDeepLinkingJson(string $courseId, string $courseName, string $url): string
    {
        // Create the LTI deep linking JSON structure
        $courseUrl = $url . "api/v1/courses/" . $courseId;

        $link = [
            'type' => 'link',
            'title' => $courseName,
            'url' => $courseUrl,
        ];
        $response = ['@context' => 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem', 'items' => $link];
        return json_encode($response);
    }

    /**
     * Retrive all user enrollments for a given provider (cached)
     * @param string $userId
     * @return Illuminate\Http\JsonResponse
     * @throws \InvalidArgumentException
     */
    public function getCoursesByProvider(string $userId): Illuminate\Http\JsonResponse | \InvalidArgumentException
    {
        $time = time();
        $enrollments = ProviderUserResource::where('user_id', $userId)->get();
        $links = [];
        foreach ($enrollments as $course) {
            // Create the LTI deep linking JSON structure
            $link = \ProviderPlatformServices::formatLtiDeepLinkFromCanvasCourse($course, $this->getBaseUrl());
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

    public function updateProviderUserResources(string $userId): void
    {
        $courses = $this->listCoursesForUser($userId);
        $courses = $courses->courses;
        foreach ($courses as $course) {
            $providerUserResource = ProviderUserResource::where('user_id', $userId)
                ->where('provider_resource_id', $course->id)
                ->first();
            if (!$providerUserResource) {
                $providerUserResource = ProviderUserResource::create([
                    'provider_id' => $this->provider_id,
                    'provider_resource_id' => $course->id,
                    'user_id' => $userId,
                    'status' => 'incomplete'
                ]);
            }
        }
    }

    // constructor for when we already have the providerId
    public static function getByProviderId($providerId): CanvasServices | \InvalidArgumentException
    {
        $provider = ProviderPlatform::findByProviderId($providerId);

        if (!$provider) {
            throw new \InvalidArgumentException('Invalid provider ID');
        }
        return new self($provider->type, $provider->account_id, $provider->account_name, $provider->access_key, $provider->base_url, $provider->icon_url);
    }

    /**
     * Validate and format the account ID parameter for API URLs
     *
     * @param string $id
     * @return string Formatted account or user ID
     * @throws \InvalidArgumentException If the account ID is invalid
     */

    public static function fmtUrl(string $id): string
    {
        if (substr($id, -1) !== '/') {
            $id .= '/';
        }
        return $id;
    }

    public function fmtAndValidateId(string $id): string
    {
        if ($id === 'self' || is_numeric($id)) {
            // Append a trailing slash if needed
            if (substr($id, -1) !== '/') {
                $id .= '/';
            }
            return $id;
        } else {
            throw new \InvalidArgumentException('Invalid account ID');
        }
    }

    public static function handleResponse($response): mixed
    {
        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody()->__toString());
        } else {
            throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
        }
    }

    // This is the cache refresh function (makes API calls directly)
    // ****************************************************
    // route::get('/api/v1/users/{user_id}/courses')
    //*****************************************************
    // @param Request $request
    // @return Illuminate\Http\JsonResponse
    // ****************************************************
    public function refreshEnrollmentCache(StudentEnrollmentRequest $request): \Illuminate\Http\JsonResponse
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
    /**
     * Get a list of users from Canvas
     *
     * @param? string
     * @return mixed JSON decoded
     * @throws \Exception
     *
     * AccountId can be accessed via the field in the class,
     * but it seems most of the time it will be self.
     */
    public function listUsers(string $accountId = 'self'): mixed
    {
        $base_url = $this->api_url . ACCOUNTS . self::fmtUrl($accountId) . USERS;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }

    /* Get details for a specific user in Canvas
     *
     * @param string $userId
     * @return mixed JSON decoded
     * @throws \Exception
     */
    public function showUserDetails(string $userId = 'self'): mixed
    {
        $base_url = $this->api_url . USERS . $userId;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());

        }
        return self::handleResponse($response);
    }

    /**
     * Create a new user in Canvas
     *
     * @param string $name
     * @param string $email
     * @param? boolean $terms (defaults true)
     * @return mixed (decoded json)
     * @throws \Exception
     */
    public function createStudentInCanvas(string $name, string $email, bool $terms = true): mixed
    {
        $userData = [
            'user' => [
                'name' => $name,
                'skip_registration' => true,
                'terms_of_use' => $terms
            ],
            'pseudonym' => [
                'unique_id' => $email,
                'send_confirmation' => false,
            ],
            'force_validations' => true,
        ];

        $base_url = $this->api_url . ACCOUNTS . SELF . USERS;
        try {
            $response = $this->client->post($base_url, $userData);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }

    /**
     * List Activity Stream
     *
     * @param? string $account (default self)
     * @return mixed (decoded json)
     * @throws \Exception
     */
    public function listActivityStream(string $account = 'self'): mixed
    {

        $base_url = $this->api_url . USERS . self::fmtUrl($account) . ACTIVITY_STREAM;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }

    /**
     * List Activity Stream Summary from Canvas
     * @param? string $account (default self)
     * @return mixed (decoded json)
     * @throws \Exception
     */
    public function getActivityStreamSummary(string $account = 'self'): mixed
    {

        $base_url = $this->api_url . USERS .  self::fmtUrl($account) . ACTIVITY_STREAM . 'summary';
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }

    /**
     * List Todo Items from Canvas
     * @param? string $account (default self)
     * @return mixed (decoded json)
     * @throws \Exception
     */
    public function listTodoItems(string $account = 'self'): mixed
    {
        $base_url = $this->api_url . USERS .  self::fmtUrl($account) . 'todo';
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }

    /**
     * Get Todo Items Count from Canvas
     * @param? string $account (default self)
     * @return mixed (decoded json)
     *
     **/
    public function getTodoItemsCount(string $account = 'self'): mixed
    {
        $base_url = $this->api_url . USERS .  self::fmtUrl($account) . 'todo_item_count';
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }

    /**
     * List Upcoming Assignments from Canvas
     * @param? string $account (default self)
     * @return mixed (decoded json)
     * @throws \Exception
     */
    public function listUpcomingAssignments(string $userId = 'self'): mixed
    {
        $base_url = $this->api_url .  USERS .  self::fmtUrl($userId) . 'upcoming_events';
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }

    /**
     * List Missing Submissions from Canvas
     * @param? string $userId (default self)
     * @return mixed JSON decoded
     * @throws \Exception
     */
    public function listMissingSubmissions(string $userId = 'self'): mixed
    {
        $base_url = $this->api_url . USERS .  self::fmtUrl($userId) . 'missing_submissions';
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /**
     * List Courses from Canvas
     * @return mixed JSON decoded
     * @throws \Exception
     **/
    public function listCourses(): mixed
    {
        $base_url = $this->api_url . COURSES;
        try {
            $response = $this->client->get($base_url . COURSES);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /**
     * List Courses from Canvas per User
     * This returns the full User object complete with an array of course items
     * that the user should be enrolled in.
     * @param string $userI
     * @return mixed JSON decoded
     * @throws \Exception
     **/
    public function listCoursesForUser(string $userId = 'self'): mixed
    {
        $base_url = $this->api_url . USERS . self::fmtUrl($userId) . COURSES;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }

    /**
     * List Course Assignments from Canvas
     *
     * @param string $userId (default self)
     * @param string $courseId
     * @return mixed JSON decoded
     * @throws \Exception
     *
     * Canvas Docs:
     * "You can supply self as the user_id to query your own progress
     * in a course. To query another user’s progress, you must be a
     * teacher in the course, an administrator, or a linked observer of the user."
     * */
    public function getUserCourseProgress(string $userId = 'self', string $courseId): mixed
    {
        $base_url = $this->api_url . COURSES . self::fmtUrl($courseId) . USERS . self::fmtUrl($userId) . PROGRESS;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /**
     * List Course Assignments from Canvas
     * @param string $userId
     * @return mixed JSON decoded
     * @throws \Exception
     * */
    public function getEnrollmentsByUser(string $userId): mixed
    {
        $base_url = $this->api_url .  USERS . self::fmtUrl($userId) . ENROLLMENTS;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }

    /**
     * List Enrollments from Canvas by Course ID
     * @param string $
     * @return mixed JSON decoded
     * @throws \Exception
     **/
    public function getEnrollmentsByCourse(string $courseId): mixed
    {
        $base_url = $this->api_url . COURSES . self::fmtUrl($courseId) . ENROLLMENTS;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /**
     * List Course Enrollments By Section
     * @param string $sectionId
     * @return mixed JSON decoded
     * @throws \Exception
     **/
    public function getEnrollmentsBySection(string $sectionId): mixed
    {
        $base_url = $this->api_url . SECTIONS . self::fmtUrl($sectionId) . ENROLLMENTS;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception('API_ERROR ' . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /**
     * Enroll a user in a course
     *
     * @param string $user_id
     * @param string $course_id
     * @param? string $type (default=StudentEnrollment)
     * @throws \Exception
     * @return mixed JSON decoded
     **/
    public function enrollUser(string $userId, string $type = "StudentEnrollment", string $courseId): mixed
    {
        $enrollment = [
            'user_id' => [$userId],
            'type' => [$type],
        ];
        $base_url = $this->api_url . COURSES . self::fmtUrl($courseId) . ENROLLMENTS . $enrollment;
        try {
            $response = $this->client->post($base_url);
        } catch (RequestException $e) {
            throw new \Exception('API_ERROR ' . $e->getMessage());
        }
        return self::handleResponse($response);
    }

    /**
     *  Enroll a user in a Section
     * @param string $sectionId
     * @param string $user_id (default=self)
     * @param? string $type (default=StudentEnrollment)
     * @return mixed decoded JSON
     * @throws Exception
     **/
    public function enrollUserInSection(string $sectionId, string $userId, string $type = "StudentEnrollment"): mixed
    {
        $enrollment = [
            'user_id' => [$userId],
            'type' => [$type],
        ];
        $base_url = $this->api_url . SECTIONS . self::fmtUrl($sectionId) . ENROLLMENTS . $enrollment;
        try {
            $response = $this->client->post($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /**
     *  Delete a course Enrollment
     * @param string $course_id
     * @param string $user_id (default=self)
     * @return mixed JSON decoded
     * @throws Exception
     */
    public function deleteEnrollment(string $enrollmentId, string $courseId): mixed
    {
        $base_url = $this->api_url  . COURSES . self::fmtUrl($courseId) . ENROLLMENTS . $enrollmentId;
        try {
            $response = $this->client->delete($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /**
     * Accept a course invitation
     * @param string $course_id
     * @param string $user_id (default=self)
     * @return mixed JSON decoded
     * @throws Exception
     */
    public function acceptCourseInvitation(string $courseId, string $userId = 'self'): mixed
    {
        $base_url = $this->api_url  . COURSES . self::fmtUrl($courseId) . ENROLLMENTS . self::fmtUrl($userId) . 'accept';
        try {
            $response = $this->client->post($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /**
     * Reject a course invitation
     * @param string $courseId
     * @param string $userId (default=self)
     * @return mixed decoded JSON
     * @throws Exception
     */
    public function rejectCourseInvitation(string $courseId, string $userId = 'self'): mixed
    {
        $base_url = $this->api_url  . COURSES . self::fmtUrl($courseId) . ENROLLMENTS . self::fmtUrl($userId) . 'reject';
        try {
            $response = $this->client->post($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /**
     * Reactivate a course enrollment
     * @param string $courseId
     * @param string $userId (default=self)
     * @return mixed decoded JSON
     * @throws Exception
     **/
    public function reactivateCourseEnrollment(string $courseId, string $userId = 'self'): mixed
    {
        $base_url = $this->api_url  . COURSES . self::fmtUrl($courseId) . ENROLLMENTS . self::fmtUrl($userId) . 'reactivate';
        try {
            $response = $this->client->put($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /**
     * Add last attended date of course
     * @param string $courseId
     * @param string $userId (default=self)
     * @return mixed decoded JSON
     * @throws Exception
     **/
    public function addLastAttendedDate(string $courseId, string $userId = 'self'): mixed
    {
        $base_url = $this->api_url . COURSES . self::fmtUrl($courseId) . ENROLLMENTS . self::fmtUrl($userId) . 'last_attended';
        try {
            $response = $this->client->put($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }

    /**
     * Query progress of user
     * @param string $userId (default=self)
     * @return mixed decoded JSON
     * @throws Exception
     **/
    public function queryUserProgress(string $userId = 'self'): mixed
    {
        $base_url = $this->api_url . PROGRESS . $userId;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /**
     * Cancel user progress
     * @param string $userId (default=self)
     * @return mixed JSON decoded
     * @throws Exception
     **/
    public function cancelUserProgress(string $userId = 'self'): mixed
    {
        $base_url = $this->api_url  . PROGRESS . $userId;
        try {
            $response = $this->client->post($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /**
     * List Assignments for User
     * @param string $userId (default=self)
     * @param string $coursrId
     * @return mixed JSON decoded
     * @throws Exception
     **/
    public function listAssignmentsForUser(string $courseId, string $userId = 'self'): mixed
    {
        $base_url = $this->api_url . USERS . self::fmtUrl($userId) . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /**
     * List Assignments for Course
     * @param string $courseId
     * @return mixed JSON decoded
     * @throws Exception
     **/
    public function listAssignmentsByCourse(string $courseId): mixed
    {
        $base_url = $this->api_url  . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /**
     * List Assignments for Course
     * @param string $courseId
     * @return mixed JSON decoded
     * @throws Exception
     **/
    public function listAssignmentGroupsByCourse(string $assignmentGroupId, string $courseId): mixed
    {
        $base_url = $this->api_url . COURSES . self::fmtUrl($courseId) . 'assignment_groups/' . self::fmtUrl($assignmentGroupId) . ASSIGNMENTS;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /**
     * Delete Assignment
     * @param string $courseId
     * @param string $assignmentId
     * @return mixed JSON decoded
     * @throws Exception
     **/
    public function deleteAssignment(string $courseId, string $assignmentId): mixed
    {
        $base_url = $this->api_url . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS . $assignmentId;
        try {
            $response = $this->client->delete($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }

    /**
     * Get a single Assignment
     * @param string $courseId
     * @param string $assignmentId
     * @return mixed JSON decoded
     * @throws Exception
     **/
    public function getAssignment(string $courseId, string $assignmentId): mixed
    {
        $base_url = $this->api_url . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS . $assignmentId;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /*
        * Create an assignment for a given Course ID
        *
        * There are so many possible parameters, but the only one required
        * is "name" so we will just pass in the array which can have any
        * or all of them
        * @param associative array $assignmentInfo
        * @param string $courseId
        * @return mixed JSON decoded
        * @throws Exception
        **/
    public function createAssignmentForCourse(array $assignmentInfo, string $courseId): mixed
    {
        $base_url = $this->api_url  . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS . $assignmentInfo;
        try {
            $response = $this->client->post($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }

    /*
        * Edit an assignment for a given Course ID
        *
        * There are so many possible parameters, but the only one required
        * is "name" so we will just pass in the array which can have any
        * or all of them
        * @param associative array $assignmentInfo
        * @param string $courseId
        * @param string $assignmentId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public function editAssignmentForCourse(array $assignmentInfo, string $courseId, string  $assignmentId): mixed
    {
        $base_url = $this->api_url . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS . self::fmtUrl($assignmentId) . $assignmentInfo;
        try {
            $response = $this->client->put($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /*
        * Edit an assignment for a given Course ID
        *
        * There are so many possible parameters, but the only one required
        * is "name" so we will just pass in the array which can have any
        * or all of them
        * @param associative array $assignment (could also be a file? TODO: look into submissions)
        * @param string $courseId
        * @param string $assignmentId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public function submitAssignment(string $courseId, string $assignmentId, array $assignment): mixed
    {
        $base_url = $this->api_url . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS . self::fmtUrl($assignmentId) . SUBMISSIONS . $assignment;
        try {
            $response = $this->client->post($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /*
        * List assignment submissions for a given Course ID
        *
        * @param string $courseId
        * @param string $assignmentId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public function getAssignmentSubmissions(string $courseId, string $assignmentId): mixed
    {
        $base_url = $this->api_url . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS . self::fmtUrl($assignmentId) . SUBMISSIONS;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /*
        * List submissions for multiple assignments for a given Course ID
        *
        * @param string $courseId
        * @param string $assignmentId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public function getSubmissionsForMultipleAssignments(string $courseId): mixed
    {
        $base_url = $this->api_url  . COURSES . self::fmtUrl($courseId) . STUDENTS . SUBMISSIONS;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /*
        * Get single submission for user / assignment
        *
        * @param string $courseId
        * @param string $userId
        * @param string $assignmentId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public function getSubmissionForUser(string $courseId, string $assignmentId, string $userId): mixed
    {
        $base_url = $this->api_url  . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS . self::fmtUrl($assignmentId) . SUBMISSIONS . $userId;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /*
        * Get single submission by anonymous ID
        *
        * @param string $courseId
        * @param string $anonId
        * @param string $assignmentId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public function getSubmissionForAnonID(string $courseId, string $assignmentId, string $anonId): mixed
    {
        $base_url = $this->api_url . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS .  self::fmtUrl($assignmentId) . ANONYMOUS_SUBMISSIONS . $anonId;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /*
        * Upload a file for submission
        *
        * @param string $courseId
        * @param string $userId
        * @param string $assignmentId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public function uploadFileForSubmission(string $courseId, string $assignmentId, string $userId): mixed
    {
        $base_url = $this->api_url . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS .  self::fmtUrl($assignmentId) . SUBMISSIONS . self::fmtUrl($userId) . 'files';
        try {
            $response = $this->client->post($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /*
        * Grade or comment on a submission
        *
        * @param string $courseId
        * @param string $userId
        * @param string $assignmentId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public function gradeOrCommentSubmission(string $courseId, string $assignmentId, string  $userId): mixed
    {
        $base_url = $this->api_url  . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS .  self::fmtUrl($assignmentId) . SUBMISSIONS . $userId;
        try {
            $response = $this->client->put($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /*
        * Grade or comment on a submission by anonymous ID
        *
        * @param string $anonId
        * @param string $assignmentId
        * @param string $courseId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public function gradeOrCommentSubmissionAnon(string $courseId, string $assignmentId, string $anonId): mixed
    {
        $base_url = $this->api_url  . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS .  self::fmtUrl($assignmentId) . ANONYMOUS_SUBMISSIONS . $anonId;
        try {
            $response = $this->client->put($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /*
        * List Gradeable Students
        *
        * @param string $assignmentId
        * @param string $courseId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public function listGradeableStudents(string $courseId, string $assignmentId): mixed
    {
        $base_url = $this->api_url  . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS .  self::fmtUrl($assignmentId) . GRADEABLE_STUDENTS;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /*
        * List Multiple Assignments Gradeable Students
        *
        * @param string $courseId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public function listMultipleAssignmentsGradeableStudents(string $courseId): mixed
    {
        $base_url = $this->api_url  . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS . GRADEABLE_STUDENTS;
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /*
        * Mark Submision as read
        *
        * @param string $userId
        * @param string $courseId
        * @param string $assignmentId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public function markSubmissionAsRead(string $courseId, string $assignmentId, string $userId = 'self'): mixed
    {
        $base_url = $this->api_url  . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS . self::fmtUrl($assignmentId) . SUBMISSIONS . self::fmtUrl($userId) . READ;
        try {
            $response = $this->client->put($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }

    /*
        * Mark Submision Item as read
        *
        * @param string $userId
        * @param string $item
        * @param string $courseId
        * @param string $assignmentId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public function markSubmissionItemAsRead(string $courseId, string $assignmentId, string $userId = 'self', string $item): mixed
    {
        $base_url = $this->api_url  . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS . self::fmtUrl($assignmentId) . SUBMISSIONS . self::fmtUrl($userId) . READ . $item;
        try {
            $response = $this->client->put($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /*
        * Mark Submision as unread
        *
        * @param string $userId
        * @param string $courseId
        * @param string $assignmentId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public function markSubmissionAsUnread(string $courseId, string $assignmentId, string $userId = 'self'): mixed
    {
        $base_url = $this->api_url  . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS . self::fmtUrl($assignmentId) . SUBMISSIONS . self::fmtUrl($userId) . READ;
        try {
            $response = $this->client->delete($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }

    /*
        * Clear unread status for all Submisions
        * Site admin only
        * @param string $userId
        * @param string $courseId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public function clearUnreadStatusForAllSubmissions(string $courseId, string $userId = 'self'): mixed
    {
        $base_url = $this->api_url  . COURSES . self::fmtUrl($courseId) . SUBMISSIONS . self::fmtUrl($userId) . 'clear_unread';
        try {
            $response = $this->client->put($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
    /*
        * Get Submision summary
        *
        * @param string $courseId
        * @param string $assignmentId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public function getSubmissionSummary(string $courseId, string $assignmentId): mixed
    {
        $base_url = $this->api_url . COURSES . self::fmtUrl($courseId) . ASSIGNMENTS . self::fmtUrl($assignmentId) . 'submission_summary';
        try {
            $response = $this->client->get($base_url);
        } catch (RequestException $e) {
            throw new \Exception(API_ERROR . $e->getMessage());
        }
        return self::handleResponse($response);
    }
}

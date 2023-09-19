<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

declare(strict_types=1);

enum CanvasType
{
    case CLOUD;
    case OSS;
}

class CanvasUtil extends ProviderUtil
{
    const CLOUD = 'cloud';
    const OSS = 'oss';


    public function __construct(int $accountId, string $url, CanvasType $type, string $instanceId)
    {
        match ($type) {
            CanvasType::OSS => parent::__construct(CanvasUtil::OSS, $accountId, $instanceId, $url),
            CanvasType::CLOUD => parent::__construct(CanvasUtil::CLOUD, $accountId, $instanceId, $url),
        };
    }

    /**
     * Validate and format the account ID parameter for API URLs
     *
     * @param string $id
     * @return string Formatted account or user ID
     * @throws \InvalidArgumentException If the account ID is invalid
     */
    public static function fmtAndValidateId(string $id): string
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
    public static function listUsers(string $accountId = 'self'): mixed
    {
        $baseUrl = env('CANVAS_API');
        $accessToken = env('CANVAS_API_KEY');

        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);
        $accountId = self::fmtAndValidateId($accountId);
        try {
            $response = $client->get($baseUrl . 'accounts/' . $accountId . 'users');

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {

                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $e) {
            throw new \Exception('API request failed: ' . $e->getMessage());
        }
    }

    /* Get details for a specific user in Canvas
     *
     * @param string $userId
     * @return mixed JSON decoded
     * @throws \Exception
     */
    public static function showUserDetails(string $userId = 'self'): mixed
    {
        $baseUrl = env('CANVAS_API');
        $accessToken = env('CANVAS_API_KEY');

        $client = new Client([
            'base_uri' => $baseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);
        $userId = self::fmtAndValidateId($userId);
        try {
            $response = $client->get($baseUrl . 'users/' . $userId);

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $e) {

            throw new \Exception('API request failed: ' . $e->getMessage());
        }
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
    public function createUser(string $name, string $email, bool $terms = true): mixed
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
        $baseUrl = env('CANVAS_API');
        $apiKey = env('CANVAS_API_KEY');

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        try {
            $response = $client->post($baseUrl . "accounts/self/users/", $userData);

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
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
        $baseUrl = env('CANVAS_API');
        $apiKey = env('CANVAS_API_KEY');

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        try {
            $account = self::fmtAndValidateId($account);

            $response = $client->get($baseUrl . 'users/' . $account . 'activity_stream');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }

    /**
     * List Activity Stream Summary from Canvas
     * @param? string $account (default self)
     * @return mixed (decoded json)
     * @throws \Exception
     */
    public static function getActivityStreamSummary(string $account = 'self'): mixed
    {
        $baseUrl = env('CANVAS_API');
        $apiKey = env('CANVAS_API_KEY');

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        $account = self::fmtAndValidateId($account);

        try {
            $response = $client->get($baseUrl . 'users/' .  $account . 'activity_stream/summary');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }

    /**
     * List Todo Items from Canvas
     * @param? string $account (default self)
     * @return mixed (decoded json)
     * @throws \Exception
     */
    public static function listTodoItems(string $account = 'self'): mixed
    {
        $baseUrl = env('CANVAS_API');
        $apiKey = env('CANVAS_API_KEY');

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        $account = self::fmtAndValidateId($account);

        try {
            $response = $client->get($baseUrl . 'users/' .  $account . 'todo');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }

    /**
     * Get Todo Items Count from Canvas
     * @param? string $account (default self)
     * @return mixed (decoded json)
     *
     **/
    public static function getTodoItemsCount(string $account = 'self'): mixed
    {
        $baseUrl = env('CANVAS_API');
        $apiKey = env('CANVAS_API_KEY');

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        $account = self::fmtAndValidateId($account);

        try {
            $response = $client->get($baseUrl . 'users/' .  $account . 'todo_item_count');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }

    /**
     * List Upcoming Assignments from Canvas
     * @param? string $account (default self)
     * @return mixed (decoded json)
     * @throws \Exception
     */

    public static function listUpcomingAssignments(string $userId = 'self'): mixed
    {
        $baseUrl = env('CANVAS_API');
        $apiKey = env('CANVAS_API_KEY');

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->get($baseUrl . 'users/' .  $userId . 'upcoming_events');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }

    /**
     * List Missing Submissions from Canvas
     * @param? string $userId (default self)
     * @return mixed JSON decoded
     * @throws \Exception
     */
    public static function listMissingSubmissions(string $userId = 'self'): mixed
    {
        $baseUrl = env('CANVAS_API');
        $apiKey = env('CANVAS_API_KEY');

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->get($baseUrl . 'users/' .  $userId . 'missing_submissions');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /**
     * List Courses from Canvas
     * @return mixed JSON decoded
     * @throws \Exception
     **/
    public static function listCourses(): mixed
    {
        $baseUrl = env('CANVAS_API');
        $apiKey = env('CANVAS_API_KEY');

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        try {
            $response = $client->get($baseUrl . 'courses');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /**
     * List Courses from Canvas per User
     * @param string $userId
     * @return mixed JSON decoded
     * @throws \Exception
     **/
    public static function listCoursesForUser(string $userId = 'self'): mixed
    {
        $baseUrl = env('CANVAS_API');
        $apiKey = env('CANVAS_API_KEY');

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->get($baseUrl . 'users/' . $userId . 'courses');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
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
    public static function getUserCourseProgress(string $userId = 'self', string $courseId): mixed
    {
        $baseUrl = env('CANVAS_API');
        $apiKey = env('CANVAS_API_KEY');
        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/users/' . $userId . 'progress');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /**
     * List Course Assignments from Canvas
     * @param string $userId
     * @return mixed JSON decoded
     * @throws \Exception
     * */
    public static function getEnrollmentsByUser(string $userId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->get($baseUrl . 'users/' . $userId . 'enrollments');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /**
     * List Enrollments from Canvas by Course ID
     * @param string $
     * @return mixed JSON decoded
     * @throws \Exception
     **/
    public static function getEnrollmentsByCourse(string $courseId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);

        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/enrollments');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /**
     * List Course Enrollments By Section
     * @param string $sectionId
     * @return mixed JSON decoded
     * @throws \Exception
     **/
    public static function getEnrollmentsBySection(string $sectionId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'sections/' . $sectionId . '/enrollments');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
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
    public static function enrollUser(string $userId, string $type = "StudentEnrollment", string $courseId): mixed
    {
        $enrollment = [
            'user_id' => [$userId],
            'type' => [$type],
        ];

        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);

        try {
            $response = $client->post($baseUrl . 'courses/' . $courseId . '/enrollments' . $enrollment);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }

    /**
     *  Enroll a user in a Section
     * @param string $sectionId
     * @param string $user_id (default=self)
     * @param? string $type (default=StudentEnrollment)
     * @return mixed decoded JSON
     * @throws Exception
     **/
    public static function enrollUserInSection(string $sectionId, string $userId, string $type = "StudentEnrollment"): mixed
    {
        $enrollment = [
            'user_id' => [$userId],
            'type' => [$type],
        ];
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->post($baseUrl . 'sections/' . $sectionId . '/enrollments' . $enrollment);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /**
     *  Delete a course Enrollment
     * @param string $course_id
     * @param string $user_id (default=self)
     * @return mixed JSON decoded
     * @throws Exception
     */
    public static function deleteEnrollment(string $enrollmentId, string $courseId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->delete($baseUrl . 'courses/' . $courseId . '/enrollments/' . $enrollmentId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /**
     * Accept a course invitation
     * @param string $course_id
     * @param string $user_id (default=self)
     * @return mixed JSON decoded
     * @throws Exception
     */
    public static function acceptCourseInvitation(string $courseId, string $userId = 'self'): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->post($baseUrl . 'courses/' . $courseId . '/enrollments/' . $userId . 'accept');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /**
     * Reject a course invitation
     * @param string $courseId
     * @param string $userId (default=self)
     * @return mixed decoded JSON
     * @throws Exception
     */
    public static function rejectCourseInvitation(string $courseId, string $userId = 'self'): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->post($baseUrl . 'courses/' . $courseId . '/enrollments/' . $userId . 'reject');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /**
     * Reactivate a course enrollment
     * @param string $courseId
     * @param string $userId (default=self)
     * @return mixed decoded JSON
     * @throws Exception
     **/
    public static function reactivateCourseEnrollment(string $courseId, string $userId = 'self'): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->put($baseUrl . 'courses/' . $courseId . '/enrollments/' . $userId . 'reactivate');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /**
     * Add last attended date of course
     * @param string $courseId
     * @param string $userId (default=self)
     * @return mixed decoded JSON
     * @throws Exception
     **/
    public static function addLastAttendedDate(string $courseId, string $userId = 'self'): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->put($baseUrl . 'courses/' . $courseId . '/enrollments/' . $userId . 'last_attended');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }

    /**
     * Query progress of user
     * @param string $userId (default=self)
     * @return mixed decoded JSON
     * @throws Exception
     **/
    public static function queryUserProgress(string $userId = 'self'): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->get($baseUrl . 'progress/' . $userId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /**
     * Cancel user progress
     * @param string $userId (default=self)
     * @return mixed JSON decoded
     * @throws Exception
     **/
    public static function cancelUserProgress(string $userId = 'self'): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);
        try {
            $response = $client->post($baseUrl . 'progress/' . $userId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /**
     * List Assignments for User
     * @param string $userId (default=self)
     * @param string $coursrId
     * @return mixed JSON decoded
     * @throws Exception
     **/
    public static function listAssignmentsForUser(string $courseId, string $userId = 'self'): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->get($baseUrl . 'users/' . $userId . 'courses' . $courseId . '/assignments');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /**
     * List Assignments for Course
     * @param string $courseId
     * @return mixed JSON decoded
     * @throws Exception
     **/
    public static function listAssignmentsByCourse(string $courseId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignments');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /**
     * List Assignments for Course
     * @param string $courseId
     * @return mixed JSON decoded
     * @throws Exception
     **/
    public static function listAssignmentGroupsByCourse(string $assignmentGroupId, string $courseId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignment_groups/' . $assignmentGroupId . '/assignments');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /**
     * Delete Assignment
     * @param string $courseId
     * @param string $assignmentId
     * @return mixed JSON decoded
     * @throws Exception
     **/
    public static function deleteAssignment(string $courseId, string $assignmentId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->delete($baseUrl . 'courses/' . $courseId . '/assignments/' . $assignmentId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }

    /**
     * Get a single Assignment
     * @param string $courseId
     * @param string $assignmentId
     * @return mixed JSON decoded
     * @throws Exception
     **/
    public static function getAssignment(string $courseId, string $assignmentId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignments/' . $assignmentId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
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
    public static function createAssignmentForCourse(array $assignmentInfo, string $courseId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->post($baseUrl . 'courses/' . $courseId . '/assignments' . $assignmentInfo);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
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
    public static function editAssignmentForCourse(array $assignmentInfo, string $courseId, string  $assignmentId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->put($baseUrl . 'courses/' . $courseId . '/assignments' . $assignmentId . '/' . $assignmentInfo);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
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
    public static function submitAssignment(string $courseId, string $assignmentId, array $assignment): mixed
    {

        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->post($baseUrl . 'courses/' . $courseId . '/assignments/' . $assignmentId . '/submissions' . $assignment);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /*
        * List assignment submissions for a given Course ID
        *
        * @param string $courseId
        * @param string $assignmentId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public static function getAssignmentSubmissions(string $courseId, string $assignmentId): mixed
    {

        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignments/' . $assignmentId . '/submissions');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /*
        * List submissions for multiple assignments for a given Course ID
        *
        * @param string $courseId
        * @param string $assignmentId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public static function getSubmissionsForMultipleAssignments(string $courseId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/students' .  '/submissions');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
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
    public static function getSubmissionForUser(string $courseId, string $assignmentId, string $userId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignments' .  $assignmentId . '/submissions' . $userId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
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
    public static function getSubmissionForAnonID(string $courseId, string $assignmentId, string $anonId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignments' .  $assignmentId . '/anonymous_submissions' . $anonId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
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
    public static function uploadFileForSubmission(string $courseId, string $assignmentId, string $userId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->post($baseUrl . 'courses/' . $courseId . '/assignments' .  $assignmentId . '/submissions' . $userId . 'files');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
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
    public static function gradeOrCommentSubmission(string $courseId, string $assignmentId, string  $userId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->put($baseUrl . 'courses/' . $courseId . '/assignments' .  $assignmentId . '/submissions' . $userId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
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
    public static function gradeOrCommentSubmissionAnon(string $courseId, string $assignmentId, string $anonId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->put($baseUrl . 'courses/' . $courseId . '/assignments' .  $assignmentId . '/anonymous_submissions' . $anonId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /*
        * List Gradeable Students
        *
        * @param string $assignmentId
        * @param string $courseId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public static function listGradeableStudents(string $courseId, string $assignmentId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignments' .  $assignmentId . '/gradeable_students');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /*
        * List Multiple Assignments Gradeable Students
        *
        * @param string $courseId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public static function listMultipleAssignmentsGradeableStudents(string $courseId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignments' . '/gradeable_students');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
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
    public static function markSubmissionAsRead(string $courseId, string $assignmentId, string $userId = 'self'): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->put($baseUrl . 'courses/' . $courseId . '/assignments/' . $assignmentId . '/submissions/' . $userId . 'read');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
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
    public static function markSubmissionItemAsRead(string $courseId, string $assignmentId, string $userId = 'self', string $item): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->put($baseUrl . 'courses/' . $courseId . '/assignments/' . $assignmentId . '/submissions/' . $userId . 'read' . $item);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
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
    public static function markSubmissionAsUnread(string $courseId, string $assignmentId, string $userId = 'self'): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);
        try {
            $response = $client->delete($baseUrl . 'courses/' . $courseId . '/assignments/' . $assignmentId . '/submissions/' . $userId . 'read');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }

    /*
        * Clear unread status for all Submisions
        * Site admin only
        * @param string $userId
        * @param string $courseId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public static function clearUnreadStatusForAllSubmissions(string $courseId, string $userId = 'self'): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->put($baseUrl . 'courses/' . $courseId . '/submissions/' . $userId . 'clear_unread');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /*
        * Get Submision summary
        *
        * @param string $courseId
        * @param string $assignmentId
        * @return mixed decoded JSON
        * @throws Exception
        **/
    public static function getSubmissionSummary(string $courseId, string $assignmentId): mixed
    {
        $baseUrl = env("CANVAS_API");
        $apiKey = env("CANVAS_API_KEY");
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignments/' . $assignmentId . '/submission_summary');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
}

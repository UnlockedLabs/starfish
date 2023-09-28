<?php

<<<<<<< HEAD:app/Utilities/CanvasUtil.php
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\LtiController;

class CanvasUtil extends ProviderUtil
{
    /*
   * From Canvas, we get this information, this will be useful
   * because even though we are not speaking to canvas via LTI, we
   * will be modeling our providers after the LTI model.
   ***********************************************************
  the ID of the Account object
  "id": 2,
   The display name of the account
  "name": "Canvas Account",
   The UUID of the account
  "uuid": "WvAHhY5FINzq5IyRIJybGeiXyFkG3SqHUPb7jZY5",
   The account's parent ID, or null if this is the root account
  "parent_account_id": 1,
   The ID of the root account, or null if this is the root account
  "root_account_id": 1,
   The state of the account. Can be 'active' or 'deleted'.
  "workflow_state": "active"
}
*/
    // This probably will not remain as we will not be speaking LTI
    // to canvas, but we may want their provider info stored in similar schema
    public function __construct()
    {
        $controller = new LtiController();
        $info = $controller->getCanvasLtiAccount();
        $this->ltiAccount = $info['root_account_id'];
        $this->providerId = $info['id'];
        $this->providerName = $info['name'];
=======
declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Models\ProviderPlatform;


class CanvasUtil extends ProviderUtil
{

    public function __construct(string $type, int $accountId, string $accountName, string $apiKey, string $url, string $iconUrl)
    {
        return parent::__construct($type, $accountId,  $accountName, $apiKey, $url, $iconUrl);
    }


    // constructor for when we already have the providerId
    public static function getByProviderId($providerId): CanvasUtil | \InvalidArgumentException
    {
        $provider = ProviderPlatform::findByProviderId($providerId);

        if (!$provider) {
            throw new \InvalidArgumentException('Invalid provider ID');
        }
        return new self($provider->type, $provider->account_id, $provider->account_name, $provider->access_key, $provider->base_url, $provider->icon_url);
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    }

    /**
     * Validate and format the account ID parameter for API URLs
     *
     * @param string $id
     * @return string Formatted account or user ID
     * @throws \InvalidArgumentException If the account ID is invalid
     */
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function fmtAndValidateId(string $id)
=======
    public function fmtAndValidateId(string $id): string
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
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
     * @returns mixed JSON decoded
     * @throws \Exception
     *
     * AccountId can be accessed via the field in the class,
     * but it seems most of the time it will be self.
     */
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function listUsers(string $accountId = 'self')
=======
    public function listUsers(string $accountId = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $AccessKey = $this->AccessKey;

        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $AccessKey,
            ],
        ]);
        $accountId = self::fmtAndValidateId($accountId);
        try {
            $response = $client->get($baseUrl . 'accounts/' . $accountId . 'users');

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function showUserDetails(string $userId = 'self',)
=======
    public function showUserDetails(string $userId = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $AccessKey = $this->AccessKey;

        $client = new Client([
            'base_uri' => $baseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $AccessKey,
            ],
        ]);
        $userId = self::fmtAndValidateId($userId);
        try {
            $response = $client->get($baseUrl . 'users/' . $userId);

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return JSON decoded
     * @throws \Exception
     */

    public function createUser(string $name, string $email, bool $terms = true)
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
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        try {
            $response = $client->post($baseUrl . "accounts/self/users/", $userData);

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return JSON decoded
     * @throws \Exception
     */
    public function listActivityStream(string $account = 'self')
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        try {
            $account = self::fmtAndValidateId($account);

            $response = $client->get($baseUrl . 'users/' . $account . 'activity_stream');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return JSON decoded
     * @throws \Exception
     */
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function getActivityStreamSummary(string $account = 'self')
=======
    public function getActivityStreamSummary(string $account = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        $account = self::fmtAndValidateId($account);

        try {
            $response = $client->get($baseUrl . 'users/' .  $account . 'activity_stream/summary');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return JSON decoded
     * @throws \Exception
     */
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function listTodoItems(string $account = 'self')
=======
    public function listTodoItems(string $account = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        $account = self::fmtAndValidateId($account);

        try {
            $response = $client->get($baseUrl . 'users/' .  $account . 'todo');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return JSON decoded
     *
     **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function getTodoItemsCount(string $account = 'self')
=======
    public function getTodoItemsCount(string $account = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        $account = self::fmtAndValidateId($account);

        try {
            $response = $client->get($baseUrl . 'users/' .  $account . 'todo_item_count');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return JSON decoded
     * @throws \Exception
     */

<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function listUpcomingAssignments(string $userId = 'self')
=======
    public function listUpcomingAssignments(string $userId = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->get($baseUrl . 'users/' .  $userId . 'upcoming_events');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function listMissingSubmissions(string $userId = 'self')
=======
    public function listMissingSubmissions(string $userId = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->get($baseUrl . 'users/' .  $userId . 'missing_submissions');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function listCourses()
=======
    public function listCourses(): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        try {
            $response = $client->get($baseUrl . 'courses');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /**
     * List Courses from Canvas per User
     * This returns the full User object complete with an array of course items
     * that the user should be enrolled in.
     * @param string $userI
     * @return mixed JSON decoded
     * @throws \Exception
     **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function listCoursesForUser(string $userId = 'self')
=======
    public function listCoursesForUser(string $userId = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;

        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->get($baseUrl . 'users/' . $userId . 'courses');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return JSON decoded
     * @throws \Exception
     *
     * Canvas Docs:
     * "You can supply self as the user_id to query your own progress
     * in a course. To query another user’s progress, you must be a
     * teacher in the course, an administrator, or a linked observer of the user."
     * */
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function getUserCourseProgress(string $userId = 'self', string $courseId)
=======
    public function getUserCourseProgress(string $userId = 'self', string $courseId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;
        $client = new Client([
            'Authorization' => 'Bearer ' . $apiKey,
        ]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/users/' . $userId . 'progress');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @returns JSON decoded
     * @throws \Exception
     * */
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function getEnrollmentsByUser(string $userId)
=======
    public function getEnrollmentsByUser(string $userId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->get($baseUrl . 'users/' . $userId . 'enrollments');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @returns JSON decoded
     * @throws \Exception
     **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function getEnrollmentsByCourse(string $courseId)
=======
    public function getEnrollmentsByCourse(string $courseId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);

        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/enrollments');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @returns JSON decoded
     * @throws \Exception
     **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function getEnrollmentsBySection(string $sectionId)
=======
    public function getEnrollmentsBySection(string $sectionId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'sections/' . $sectionId . '/enrollments');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     *
     **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function enrollUser(string $userId, string $type = "StudentEnrollment", string $courseId)
=======
    public function enrollUser(string $userId, string $type = "StudentEnrollment", string $courseId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $enrollment = [
            'user_id' => [$userId],
            'type' => [$type],
        ];

        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);

        try {
            $response = $client->post($baseUrl . 'courses/' . $courseId . '/enrollments' . $enrollment);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
    /**
     *  Enroll a user in a Section
     * @param string $sectionId
     * @param string $user_id (default=self)
     * @param? string $type (default=StudentEnrollment)
     * @return decoded JSON
     * @throws Exception
     **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function enrollUserInSection(string $sectionId, string $userId, string $type = "StudentEnrollment")
=======
    public function enrollUserInSection(string $sectionId, string $userId, string $type = "StudentEnrollment"): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $enrollment = [
            'user_id' => [$userId],
            'type' => [$type],
        ];
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->post($baseUrl . 'sections/' . $sectionId . '/enrollments' . $enrollment);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return decoded JSON
     * @throws Exception
     */
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function deleteEnrollment(string $enrollmentId, string $courseId)
=======
    public function deleteEnrollment(string $enrollmentId, string $courseId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->delete($baseUrl . 'courses/' . $courseId . '/enrollments/' . $enrollmentId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return decoded JSON
     * @throws Exception
     */
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function acceptCourseInvitation(string $courseId, string $userId = 'self')
=======
    public function acceptCourseInvitation(string $courseId, string $userId = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->post($baseUrl . 'courses/' . $courseId . '/enrollments/' . $userId . 'accept');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function rejectCourseInvitation(string $courseId, string $userId = 'self')
=======
    public function rejectCourseInvitation(string $courseId, string $userId = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->post($baseUrl . 'courses/' . $courseId . '/enrollments/' . $userId . 'reject');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return decoded JSON
     * @throws Exception
     **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function reactivateCourseEnrollment(string $courseId, string $userId = 'self')
=======
    public function reactivateCourseEnrollment(string $courseId, string $userId = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->put($baseUrl . 'courses/' . $courseId . '/enrollments/' . $userId . 'reactivate');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return decoded JSON
     * @throws Exception
     **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function addLastAttendedDate(string $courseId, string $userId = 'self')
=======
    public function addLastAttendedDate(string $courseId, string $userId = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->put($baseUrl . 'courses/' . $courseId . '/enrollments/' . $userId . 'last_attended');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return decoded JSON
     * @throws Exception
     **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function queryUserProgress(string $userId = 'self')
=======
    public function queryUserProgress(string $userId = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->get($baseUrl . 'progress/' . $userId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return decoded JSON
     * @throws Exception
     **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function cancelUserProgress(string $userId = 'self')
=======
    public function cancelUserProgress(string $userId = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);
        try {
            $response = $client->post($baseUrl . 'progress/' . $userId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return decoded JSON
     * @throws Exception
     **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function listAssignmentsForUser(string $courseId, string $userId = 'self')
=======
    public function listAssignmentsForUser(string $courseId, string $userId = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->get($baseUrl . 'users/' . $userId . 'courses' . $courseId . '/assignments');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return decoded JSON
     * @throws Exception
     **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function listAssignmentsByCourse(string $courseId)
=======
    public function listAssignmentsByCourse(string $courseId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignments');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return decoded JSON
     * @throws Exception
     **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function listAssignmentGroupsByCourse(string $assignmentGroupId, string $courseId)
=======
    public function listAssignmentGroupsByCourse(string $assignmentGroupId, string $courseId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignment_groups/' . $assignmentGroupId . '/assignments');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return decoded JSON
     * @throws Exception
     **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function deleteAssignment(string $courseId, string $assignmentId)
=======
    public function deleteAssignment(string $courseId, string $assignmentId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->delete($baseUrl . 'courses/' . $courseId . '/assignments/' . $assignmentId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
     * @return decoded JSON
     * @throws Exception
     **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function getAssignment(string $courseId, string $assignmentId)
=======
    public function getAssignment(string $courseId, string $assignmentId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignments/' . $assignmentId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
        * @return decoded JSON
        * @throws Exception
        **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function createAssignmentForCourse(array $assignmentInfo, string $courseId)
=======
    public function createAssignmentForCourse(array $assignmentInfo, string $courseId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->post($baseUrl . 'courses/' . $courseId . '/assignments' . $assignmentInfo);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
        * @return decoded JSON
        * @throws Exception
        **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function editAssignmentForCourse(array $assignmentInfo, string $courseId, string  $assignmentId)
=======
    public function editAssignmentForCourse(array $assignmentInfo, string $courseId, string  $assignmentId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->put($baseUrl . 'courses/' . $courseId . '/assignments' . $assignmentId . '/' . $assignmentInfo);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
        * @return decoded JSON
        * @throws Exception
        **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function submitAssignment(string $courseId, string $assignmentId, array $assignment)
=======
    public function submitAssignment(string $courseId, string $assignmentId, array $assignment): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {

        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->post($baseUrl . 'courses/' . $courseId . '/assignments/' . $assignmentId . '/submissions' . $assignment);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
        * @return decoded JSON
        * @throws Exception
        **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function getAssignmentSubmissions(string $courseId, string $assignmentId)
=======
    public function getAssignmentSubmissions(string $courseId, string $assignmentId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {

        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignments/' . $assignmentId . '/submissions');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
        * @return decoded JSON
        * @throws Exception
        **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function getSubmissionsForMultipleAssignments(string $courseId)
=======
    public function getSubmissionsForMultipleAssignments(string $courseId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/students' .  '/submissions');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
        * @return decoded JSON
        * @throws Exception
        **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function getSubmissionForUser(string $courseId, string $assignmentId, string $userId)
=======
    public function getSubmissionForUser(string $courseId, string $assignmentId, string $userId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignments' .  $assignmentId . '/submissions' . $userId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
        * @return decoded JSON
        * @throws Exception
        **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function getSubmissionForAnonID(string $courseId, string $assignmentId, string $anonId)
=======
    public function getSubmissionForAnonID(string $courseId, string $assignmentId, string $anonId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignments' .  $assignmentId . '/anonymous_submissions' . $anonId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
        * @return decoded JSON
        * @throws Exception
        **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function uploadFileForSubmission(string $courseId, string $assignmentId, string $userId)
=======
    public function uploadFileForSubmission(string $courseId, string $assignmentId, string $userId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->post($baseUrl . 'courses/' . $courseId . '/assignments' .  $assignmentId . '/submissions' . $userId . 'files');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
        * @return decoded JSON
        * @throws Exception
        **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function gradeOrCommentSubmission(string $courseId, string $assignmentId, string  $userId)
=======
    public function gradeOrCommentSubmission(string $courseId, string $assignmentId, string  $userId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->put($baseUrl . 'courses/' . $courseId . '/assignments' .  $assignmentId . '/submissions' . $userId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
        * @return decoded JSON
        * @throws Exception
        **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function gradeOrCommentSubmissionAnon(string $courseId, string $assignmentId, string $anonId)
=======
    public function gradeOrCommentSubmissionAnon(string $courseId, string $assignmentId, string $anonId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->put($baseUrl . 'courses/' . $courseId . '/assignments' .  $assignmentId . '/anonymous_submissions' . $anonId);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
        * @return decoded JSON
        * @throws Exception
        **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function listGradeableStudents(string $courseId, string $assignmentId)
=======
    public function listGradeableStudents(string $courseId, string $assignmentId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignments' .  $assignmentId . '/gradeable_students');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
        * @return decoded JSON
        * @throws Exception
        **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function listMultipleAssignmentsGradeableStudents(string $courseId)
=======
    public function listMultipleAssignmentsGradeableStudents(string $courseId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignments' . '/gradeable_students');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
        * @return decoded JSON
        * @throws Exception
        **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function markSubmissionAsRead(string $courseId, string $assignmentId, string $userId = 'self')
=======
    public function markSubmissionAsRead(string $courseId, string $assignmentId, string $userId = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->put($baseUrl . 'courses/' . $courseId . '/assignments/' . $assignmentId . '/submissions/' . $userId . 'read');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
        * @return decoded JSON
        * @throws Exception
        **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function markSubmissionItemAsRead(string $courseId, string $assignmentId, string $userId = 'self', string $item)
=======
    public function markSubmissionItemAsRead(string $courseId, string $assignmentId, string $userId = 'self', string $item): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->put($baseUrl . 'courses/' . $courseId . '/assignments/' . $assignmentId . '/submissions/' . $userId . 'read' . $item);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
        * @return decoded JSON
        * @throws Exception
        **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function markSubmissionAsUnread(string $courseId, string $assignmentId, string $userId = 'self')
=======
    public function markSubmissionAsUnread(string $courseId, string $assignmentId, string $userId = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);
        try {
            $response = $client->delete($baseUrl . 'courses/' . $courseId . '/assignments/' . $assignmentId . '/submissions/' . $userId . 'read');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
        * @return decoded JSON
        * @throws Exception
        **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function clearUnreadStatusForAllSubmissions(string $courseId, string $userId = 'self')
=======
    public function clearUnreadStatusForAllSubmissions(string $courseId, string $userId = 'self'): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        $userId = self::fmtAndValidateId($userId);

        try {
            $response = $client->put($baseUrl . 'courses/' . $courseId . '/submissions/' . $userId . 'clear_unread');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
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
        * @return decoded JSON
        * @throws Exception
        **/
<<<<<<< HEAD:app/Utilities/CanvasUtil.php
    public static function getSubmissionSummary(string $courseId, string $assignmentId)
=======
    public function getSubmissionSummary(string $courseId, string $assignmentId): mixed
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Utilities/CanvasUtil.php
    {
        $baseUrl = $this->BaseUrl . "api/v1/";
        $apiKey = $this->AccessKey;;
        $client = new Client(['Authorization' => 'Bearer' . $apiKey]);
        try {
            $response = $client->get($baseUrl . 'courses/' . $courseId . '/assignments/' . $assignmentId . '/submission_summary');
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString());
            } else {
                throw new \Exception('API request failed with status code: ' . $response->getStatusCode());
            }
        } catch (RequestException $error) {
            throw new \Exception('API request failed: ' . $error->getMessage());
        }
    }
}

## Basic outline of testing strategy for Starfish/Middleware


This repository has basic functionality testing being ran on each PR or commit to main via Github Actions.

Whenever any functionality is added to starfish, in any pull request or addition, a test for basic functionality
of whatever was implemented, must be included with the PR before it is merged. 

Adding tests is fairly simple process:

- 1. in the Tests Directory inside the repository, there are three sub-directories… Browser, Feature, and Unit

    - Tests can be generated with the commend

        >Since we are using the Pest testing framework, we will want to be passing the --pest flag

    `php artisan make:test ProviderPlatformTest --pest`

 
### By default: generated tests will be placed in `tests/Feature` 

If the tests are more granular and would generally be considered Unit tests, you can also pass the flag:

    php artisan make:test ProviderPlatformTest --unit --pest

For a Unit test: this will generate such a file:
```php
<?php

test('example', function () {
    expect(true)->toBeTrue();
});

Unit tests are less common and that really isn’t a whole lot to work with….
Example Unit: 
<?php
use app\Services\CanvasServices;

test('example', function () {
    expect(true)->toBeTrue();
});

/*
  This is a function that takes two arguments, 
   1. string: the name of the test:
      "CreateProviderPlatform"
   2. function: the function that will test for whatever individual functionality you wish to test for.
 */
 
 // EXAMPLE: 
 
 test('formatLtiJsonTest', function () {
 $courseName = "MAT101";
 $providerContentId = "123456";
 $base_url = "https://canvas.instructure.com/api/v1/courses";
 
 $LtiJson = CanvasServices::formatLtiJson($courseName, $providerContentId, $baseUrl);
 
 $verified = ["resource_id" => "123456", // etc... real example of Lti json format
 
 expect($LtiJson)->toEqual($verified);
 })
```
 

 
## Example Feature test:
```php
<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderPlatformTest extends TestCase
{
    use RefreshDatabase; // This ensures a fresh database for each test.

    /**
     * Test retrieving provider platforms.
     */
    public function testGetProviderPlatforms()
    {
        $response = $this->get('/api/v1/provider_platforms');

        $response->assertStatus(200); // Check if the response code is OK.
        $response->assertJsonStructure([ // Check the JSON structure of the response.
            'data' => [
                '*' => [
                    'name',
                    'type',
                    'description',
                    'icon_url',
                    'account_id',
                    'access_key',
                    'base_url',
                ],
            ],
        ]);
    }

    /**
     * Test creating a provider platform.
     */
    public function testCreateProviderPlatform()
    {
        $data = [
            'name' => 'TestPlatform',
            'type' => 'canvas_cloud',
            'description' => 'Test Platform Description',
            'icon_url' => 'https://test.com/icon.jpg',
            'account_id' => 123456,
            'access_key' => 'testkey123',
            'base_url' => 'https://test.com/api',
        ];

        $response = $this->post('/api/v1/provider_platforms', $data);

        $response->assertStatus(201); // Check if the response code indicates a successful creation.
        $response->assertJsonFragment(['name' => 'TestPlatform']); // Check if the response contains the created data.
    }
}
```
 
At this point, it is a bit too early to be having code coverage or tests for every edge case,
as we expect our code base to evolve and change rapidly, but for core functionality (CRUD operations on controllers, formatting functions, etc), we should make sure to include at least a basic verification that what we are submitting to the repo, actually does what it’s supposed to.
Factories, Seeders, Faking DB content:

For each Model, the following commands need to be run:

`php artisan make:seeder ProviderPlatformSeeder`

and

`php artisan make:factory ProviderPlatformFactory`

 

Then you can go into the `database/seeders` and `database/factories` directories and add the information:

Example Factory:
```php

namespace Database\Factories;

use App\Models\ProviderPlatform;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentEnrollment>
 */
class StudentEnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "provider_user_id" => $this->faker->randomDigitNotNull,              // Student ID in our system (maps to appropriate ID)
            "provider_content_id" => $this->faker->randomDigitNotNull,           // Course ID
            "provider_platform_id" => $this->faker->randomDigitNotNull,
            "status" => "enabled",                        // Enum (ProviderUserResourceStatus)
        ];
    }
}
```
 
Example Seeder:
```php
// Luckily all that needs to be added for this is inside the `run` function:

    public function run(): void
    {
        \app\Models\ProviderContent::factory()->count(10)->create();
    }
```

Now we can run php 
`artisan migrate:fresh`

and 

`php artisan db:seed`


Now our db will populate with seed data for each test/CI run.

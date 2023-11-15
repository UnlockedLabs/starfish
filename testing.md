
# Testing Starfish locally



The following must be done in the right order:

1. Clone the repository.

2. Install all dependencies: `composer install` && `npm install`

3. Migrate the testing database: `php artisan migrate:fresh --env=testing`

4. Seed the database with testing data: `php artisan db:seed --env=testing`

5. Run the tests: `./vendor/bin/pest`


This is different from trying to test with a MySql prod database, this uses a sqlite database stored locally and is refreshed on each test

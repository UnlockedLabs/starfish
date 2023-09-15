## About StarFish

Project Starfish does a thing...

## Requirements

Currently, Starfish is tested with WSL, Brew and Ubuntu. 

- Docker (Docker Desktop on WSL)

## Setup Starfish


- Clone the repository
- Run composer install
- Run sail npm install
- Copy ‘.env.example’ to ‘.env’
- Run sail up
- Run sail artisan migrate
- Run sail npm run dev
- Open http://localhost


### DEVELOPMENT NOTES: 
+ Make sure you set the proper DB credentials in your .env file, this may include
    DB_HOST=, DB_PORT=, etc. Also make sure you get an API key and the exact URL 
    of the instance of canvas you are using for development/testing using the following
    `CANVAS_API` **and** `CANVAS_API_KEY` in your .env file. In the future this will be refactored
    to allow the instance of the canvasUtil class use the URL field instead of the hardcoded/ENV

- 
## License

The Laravel framework is open-sourced software licensed under the [Apache License, Version 2.0](https://opensource.org/license/apache-2-0/).

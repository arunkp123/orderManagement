# Docker Laravel RESTful API

## About

- [Docker](https://www.docker.com/) as the container service to isolate the environment.
- [Php](https://php.net/) to develop backend support.
- [Laravel](https://laravel.com) as the server framework / controller layer
- [MySQL](https://mysql.com/) as the database layer
- [NGINX](https://docs.nginx.com/nginx/admin-guide/content-cache/content-caching/) as a proxy / content-caching layer

## How to Install & Run

1.  Clone the repo
2.  Set Google Distance API key in environment file located in ./code .env file
3.  Run `./start.sh` to build docker containers, executing migration and PHPunit test cases 
4.  After starting container following will be executed automatically:
	- Table migrations using artisan migrate command.
	- Dummy Data imports using artisan db:seed command.

## Manually Migrating tables and Data Seeding

1. To run manually migrations use this command `docker exec manage_order_php php artisan migrate`
2. To run manually data import use this command `docker exec manage_order_php php artisan db:seed`

## Manually Starting the docker and test Cases

1. You can run `docker-compose up` from terminal
2. Server is accessible at `http://localhost:8080`
3. Run manual testcase suite by :
	- `docker exec manage_order_php php ./vendor/phpunit/phpunit/phpunit /var/www/html/tests/Feature/OrderControllerTest.php`
	
## How to Run Tests (Explicity from cli)

 Test Cases can be executed by:
-`docker exec manage_order_php php ./vendor/phpunit/phpunit/phpunit /var/www/html/tests/Feature/OrderControllerTest.php`
## API Reference Documentation

- **GET** `/orders?page=:page&limit=:limit`: Fetch paginated orders

    - Response :
	```
	    [
            {
                "distance": 1199398,
                "status": "TAKEN",
                "id": "5bebba7c1c2c2d001c3e92f3"
            },
            {
                "distance": 2000,
                "status": "UNASSIGNED",
                "id": "5bebba7c1c2c2d001c3e92f1"
            },
        ]
	```
- **POST** `/orders`: Create a new order

	- Request:
	```
    {
        "origin" :["28.704060", "77.102493"],
        "destination" :["28.535517", "77.391029"]
    }
	```

    - Response:
	```
    {
        "id": "5bebcf381c2c2d001c3e92f4",
        "distance": 1071,
        "status": "UNASSIGNED"
    }
	```

- **PATCH** `/orders/:id`: Update the status of a particular order using it's id

	- Request:
	```
    {
        "status" : "TAKEN"
    }
	```

    - Responsw:
	```
    {
        "status": "SUCCESS"
    }
	```
## App Structure

**./tests**

- this folder contains test cases written under /tests/Feature/OrderControllerTest.php & /tests/Unit/OrderUnitTest.php

**./app**

- Contains all the custom files for controllers, models and helpers.
- migration files are written under database folder in migrations directory
	- To run manually migrations use this command `docker exec manage_order_php php artisan migrate`
- Dummy data seeding is performed using faker under database seeds folder
	- To run manually data import use this command `docker exec manage_order_php php artisan db:seed`
- `OrderController` in ./app/Http/Controllers folder contains all the api methods.
- `Models` class contains all helper methods.
- `Helper` class contains all helper methods.

- PHPUnit.xml provides the unit test case and code coverage

**.env**

- config contains all project configuration like it provides app configs, Google API Key, database connection

- Set Google Distance API key

**./code/Readme**

- API Reference documentation file contains all method references.

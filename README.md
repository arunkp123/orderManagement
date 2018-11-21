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

## App Structure

**./tests**

- this folder contains test cases written under /tests/Feature/OrderControllerTest.php

**./app**

- Contains all the custom files for controllers, models and helpers.
- migration files are written under database folder in migrations directory
	- To run manually migrations use this command `docker exec manage_order_php php artisan migrate`
- Dummy data seeding is performed using faker under database seeds folder
	- To run manually data import use this command `docker exec manage_order_php php artisan db:seed`
- `OrderController` in ./app/Http/Controllers folder contains all the api's methods :
    1. localhost:8080/orders?page=0&limit=10 - GET url to fetch orders with page and limit
    2. localhost:8080/orders - POST method to insert new order with origin and distination
    3. localhost:8080/orders - PATCH method to update status for taken.(Handled simultaneous update request from multiple users at the same time with response status 409)
- `Models` class contains all helper methods.
- `Helper` class contains all helper methods.

- PHPUnit.xml provides the unit test case and code coverage

**.env**

- config contains all project configuration like it provides app configs, Google API Key, database connection

- Set Google Distance API key

**./code/Readme**

- API Reference documentation file contains all method references.

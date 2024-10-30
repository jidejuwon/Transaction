# Transaction Processing System

This is a simple transaction processing system built with Laravel. It demonstrates handling concurrent operations, ensuring data integrity, and implementing basic security measures.

## Table of Contents

-   [Requirements](#requirements)
-   [Installation](#installation)
-   [Usage Instructions](#setup-instructions)
-   [API Endpoints](#api-endpoints)
-   [Unit Tests](#unit-tests)
-   [Scaling and Architecture Improvements](#scaling-and-architecture-improvements)

## Requirements

-   PHP 8.0 or higher
-   Composer
-   Laravel 11.x
-   SQLite or any other supported database

## Installation

1. Clone the repository with `git clone https://github.com/jidejuwon/Transaction`
2. Install dependencies with `composer install`
3. Copy .env.example file to .env `cp .env.example .env`
4. Generate application key with `php artisan key:generate`
5. Set up database connection and api token in .env
   DB_CONNECTION=sqlite
   DB_DATABASE=/path/to/database.sqlite
   API_TOKEN=yourapikey

6. Ensure the database file exists or create it with `touch /path/to/database.sqlite`
7. Run migration with `php artisan migrate`
8. Run seed for default user with `php artisan db:seed`
   Default user email [test@example.com]
9. Start development server with `php artisan serve`

## Usage Instruction

1. Make Requests: Use tools like Postman or curl to test the API endpoints.
2. Import ./Transaction.postman_collection.json into postman
3. Update the Bearer Auth key with the key provided in `Installation:5 (API_TOKEN)`

## API Endpoints

1. POST /api/transaction: Create a new transaction.
2. GET /api/balance: Retrieve the current balance for a user by sending the user's email in the request body.

## Unit Test

1. Run the unit tests with `php artisan test`

## Scaling and Architecture Improvements

To scale this system and improve its architecture for production use, consider the following strategies:

1. Database Optimization:
   Use indexing on frequently queried fields (like user_id in transactions).
   Consider database partitioning or sharding as the user base grows.

2. Caching:
   Implement caching for frequently accessed data, such as user balances, to reduce database load.
   Use Redis or Memcached for fast in-memory caching.

3. Microservices Architecture:
   Break down the application into microservices to handle different aspects of the transaction process, such as authentication, transaction management, and user management.

4. Load Balancing:
   Use load balancers to distribute incoming traffic across multiple instances of your application, ensuring high availability and reliability.

5. Monitoring and Logging:
   Implement monitoring tools (like New Relic or Laravel Telescope) for tracking application performance.
   Set up proper logging to capture errors and performance metrics for further analysis.

6. Security Enhancements:
   Use HTTPS for all communications to secure data in transit.
   Implement rate limiting to prevent abuse of the API.
   Regularly update dependencies to fix security vulnerabilities.

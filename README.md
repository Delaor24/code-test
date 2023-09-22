## Project Overview
Implement a banking system with two types of users: Individual and Business. The system should
support deposit and withdrawal operations for both types of users.

## How to Setup project
- step 1: At first clone the project
- step 2: git checkout dev.0.0.1
- step 3: composer update
- step 4: create database: laravel_code_test
- step 5: cp .env.example .env
- step 6: php artisan migrate
- step 7: if you want to generate users by artisan command then run below command
    - php artisan user:generate
- step 8: php artisan serve

- Then chceck api on Postman

## API INFORMATION
- 1. User Register api:
    - method: post
    - url: http://127.0.0.1:8000/api/users

- 2. Login api:
    - method: post
    - url: http://127.0.0.1:8000/api/login

- 3. All Transaction api:
    - method: get
    - url: http://127.0.0.1:8000/api/show

- 4. user deposit list api:
    - method: get
    - url: http://127.0.0.1:8000/api/deposit

- 5. user deposit submit api:
    - method: post
    - url: http://127.0.0.1:8000/api/deposit

- 6. user withdrawal list api:
   - method: get
   - url: http://127.0.0.1:8000/api/withdrawal

- 7. user withdrawal submit api:
    - method: post
    - url: http://127.0.0.1:8000/api/withdrawal

- 8. Logout api:
    - method: post
    - url: http://127.0.0.1:8000/api/logout
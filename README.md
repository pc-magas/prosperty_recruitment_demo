# Prosperty recruitment app

# How to run

1. Install docker and docker compose
2. Execute `docker-compose up -d`
3. Detup database and settings:
    ```
    docker exec -ti -u www-data prosperty_recruitment_php81 /bin/bash
    composer install
    php artisan migrate
    php artisan db:seed
    ```
4. App served at: http://localhost:8080

# Authentication

A new user is created upon database seeding with the following credentials:

u: `test1@example.com`
p: `1234`
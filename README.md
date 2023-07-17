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


# Improvements

## Implement a User registration mechanism

At current situatiion users are created via seeder.
We need to create users either via command line or via a registration endpoint.

The command could be:

```
php artisan create user email
```

Where a unique link could be created and sent via email:

```
GET user/^id^/activation/^unique_token^
```

Then user would apply its password with a CAPTCHA. The link would expire after a specific ammount of time and new link must be created.

Then using the endpoint:

```
POST /user/id/activation
```

With body:

```
{
    "token":unique_token,
    "password" password
}
```

Would allow the user to complete its registration.


## Create a Password Management system

User must be able to change its password. Including:

1. Forgotten password reiminder
2. Renew password.

## Create a access management system

We need an access management system that will be either Attribute Based or Role based.
Assuming that the above functionality is implemented we need:

1. Specify Access policies for user regiustration and management
2. Specify Access policies for spy basic CRUD actions 

A simple one is a Role-Based Access controll with:

1. The `admin` role to be able to perform all functionalities.
2. The `user` role where ionly spies can be registered. 

## Make a complete docker image for the app

For easy of deployment we need to create a docker image that will auto create the nessesary users anbd launch the application.
This will aid to have the app deployed upon cloud.

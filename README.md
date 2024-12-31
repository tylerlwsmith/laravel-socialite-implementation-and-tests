# Laravel Socialite Tests

This repository demonstrates a minimal Google Socialite authentication integration and tests. The important files are listed below:

-   [`database/migrations/2024_12_31_075619_add_socialite_fields_to_users.php`](./database/migrations/2024_12_31_075619_add_socialite_fields_to_users.php)
-   [`config/services.php`](config/services.php)
-   [`routes/web.php`](routes/web.php)
-   [`resources/views/welcome.blade.php`](resources/views/welcome.blade.php)
-   [`tests/Feature/AuthRoutesTest.php`](tests/Feature/AuthRoutesTest.php)

## Running locally

Before you begin, you must create a Google app.

Once you have your Google app created, clone the respository and create an env file from the included example:

```sh
cp .env.example .env
```

Within `.env`, set the following fields with the credentials from your Google app:

```
GOOGLE_CLIENT_ID="your-google-client-id"
GOOGLE_CLIENT_SECRET="your-google-client-secret"
```

Run the following command to install the PHP dependencies:

```sh
composer install
```

Set the app key:

```sh
php artisan key:generate
```

Run the database migrations:

```sh
php artisan migrate
```

Run the project using the test server:

```sh
php artisan serve
```

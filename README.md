# Test Project for iPhone Photography School

**IMPORTANT!** This project requires MySQL 8 and PHP 7.4 or higher. [This function](app/Models/User.php#L199) uses MySQL 8 Window Functions.

## Installation

Configure `.env` file

Run `composer install`

Run `php artisan migrate --seed`

## Tests

Run `vendor/bin/phpunit` for tests

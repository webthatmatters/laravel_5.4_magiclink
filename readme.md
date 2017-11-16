## Laravel 5.4 API Authentication Boilerplate

This project is a boilerplate project that includes out of the box JWT API authentication,
using [this](https://github.com/tymondesigns/jwt-auth) great package, as well as login
with magic link functionality.

## Setup
- Configure your database credentials
- Run `composer install && php artisan migrate && php artisan jwt:generate` to bootstrap the project
- Configure permissions in `app/storage` to allow writing logs and cache
- Configure the magic links' duration and email url in `config/auth.php`
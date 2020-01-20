<p align="center">
  <img src="https://user-images.githubusercontent.com/8562128/68948016-3a7e8080-07b7-11ea-812c-b7bbec1b9c9b.png">
</p>

# karaoke-app

Backend for the karaoke web application made with Laravel 6

[![Build Status](https://travis-ci.com/karaoke-app/backend.svg?branch=dev)](https://travis-ci.com/karaoke-app/backend)

## Requirements

- PHP >= 7.2
- MySQL 5.7
- Composer

## Getting started

We're using _Docker_ with _Docker Compose_ for local developement

### Installation

Build and run docker containers in background

```sh
docker-compose up -d
```

Install dependencies

```sh
docker-compose exec php-fpm composer install
```

Create .env file

```sh
cp .env.example .env
```

Generate app key

```sh
docker-compose exec php-fpm php artisan key:generate
```

Run database migrations

```sh
docker-compose exec php-fpm php artisan migrate:fresh
```

You're ready to go. The API should be available at http://localhost:8080/api

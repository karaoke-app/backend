<p align="center">
  <img src="https://user-images.githubusercontent.com/8562128/68948016-3a7e8080-07b7-11ea-812c-b7bbec1b9c9b.png">
</p>

# karaoke-app

Backend for the karaoke web application made with Laravel 6

[![Build Status](https://travis-ci.com/karaoke-app/backend.svg?branch=dev)](https://travis-ci.com/karaoke-app/backend)

## Requirements

## Installation

Clone repository

```sh
git clone https://github.com/karaoke-app/backend.git
```

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
docker-compose exec php-fpm artisan key:generate
```

Run database migrations

```sh
docker-compose exec php-fpm artisan migrate:fresh

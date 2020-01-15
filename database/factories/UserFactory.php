<?php declare(strict_types=1);

/** @var Factory $factory */
use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'created_at' => now(),
        'updated_at' => now(),
        'password' => bcrypt('Testowe123!'),
        'remember_token' => Str::random(10),
    ];
});

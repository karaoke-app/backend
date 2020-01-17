<?php declare(strict_types=1);

/** @var Factory $factory */
use App\Playlist;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Playlist::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->numberBetween(1),
        'name' => $faker->name,
        'is_private' => 0,
        'user_id' => function () {
            return factory(User::class)->create();
        },
        'created_at' => now(),
        'updated_at' => now()
    ];
});

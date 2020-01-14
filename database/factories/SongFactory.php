<?php declare(strict_types=1);

/** @var Factory $factory */
use App\Song;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

$factory->define(Song::class, function (Faker $faker) {
    $artist = $faker->name;
    $title = $faker->title;
    return [
        'created_at' => now(),
        'updated_at' => now(),
        'user_id' => function () {
            return factory(User::class)->create();
        },
        'artist' => $artist,
        'title' => $title,
        'cues' => [
            [
                'text' => $faker->text(10),
                'startTime' => $faker->numberBetween(1, 5),
                'endTime' => $faker->numberBetween(5, 10)
            ]
        ],
        'video_id' => $faker->unique()->numberBetween(1),
        'provider_id' => 'youtube',
        'is_accepted' => 1,
        'slug' => Str::slug($artist . $title, '-')
    ];
});

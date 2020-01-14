<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Song;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class SongTest extends TestCase
{
    use WithFaker;

    public function setUpValidSong()
    {
        $artist = $this->faker->name;
        $title = $this->faker->title;
        return [
            'artist' => $artist,
            'title' => $title,
            'cues' => [
                [
                    'text' => $this->faker->text(10),
                    'startTime' => '000',
                    'endTime' => '010'
                ],
                [
                    'text' => $this->faker->text(10),
                    'startTime' => '011',
                    'endTime' => '017'
                ]
            ],
            'video_id' => $this->faker->numberBetween(1),
            'provider_id' => 'youtube'
        ];
    }

    public function setUpValidRegisterData()
    {
        $password = 'Testowe123!';
        return [
            'name' => $this->faker->name,
            'password' => $password,
            'password_confirmation' => $password,
            'email' => $this->faker->email
        ];
    }

    public function testCreateSong()
    {
        $response = $this->json('POST', '/api/register', self::setUpValidRegisterData());

        $token = $response->baseResponse->original['access_token'];

        $response = $this->json('POST', '/api/songs', self::setUpValidSong(), ['HTTP_Authorization' => 'Bearer' . $token]);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'song' => [
                    'artist',
                    'title',
                    'cues' => [
                        [
                            'text',
                            'startTime',
                            'endTime'
                        ],
                        [
                            'text',
                            'startTime',
                            'endTime'
                        ]
                    ],
                    'video_id',
                    'provider_id',
                    'is_accepted',
                    'slug',
                    'user_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ])
        ;
    }

    public function testGetAllSongs()
    {
        $response = $this->json('GET', '/api/songs');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                [
                    'id',
                    'slug',
                    'artist',
                    'title'
                ]
            ]);
    }

    public function testGetSongUserWhoHasNotSong()
    {
        $user = factory(User::class)->create();

        $response = $this->json('GET', '/api/songs/user/' . $user->id);

        $response
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Sorry, this user did not upload any songs or user does not exist.'
            ]);
    }

    public function testGetSongUserWithNonexistentId()
    {
        $response = $this->json('GET', '/api/songs/user/' . 9999999);

        $response
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Sorry, this user did not upload any songs or user does not exist.'
            ]);
    }

    public function testGetSongWithId()
    {
        $song = factory(Song::class)->create();

        $response = $this->json('GET', '/api/songs/' . $song->id);
        $user = User::where('id', $song->user_id)->first();
        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $song->id,
                'user_id' => $song->user_id,
                'title' => $song->title,
                'artist' => $song->artist,
                'provider_id' => $song->provider_id,
                'video_id' => $song->video_id,
                'slug' => $song->slug,
                'is_accepted' => $song->is_accepted,
                'created_at' => $song->created_at,
                'updated_at' => $song->updated_at,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar
                ]
            ])
        ;
    }
}

<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Playlist;
use App\Song;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class PlaylistTest extends TestCase
{
    use WithFaker;

    public function setUpValidUser()
    {
        return [
            'name' => $this->faker->lastName,
            'email' => $this->faker->email,
            'password' => 'Testowskie98!',
            'password_confirmation' => 'Testowskie98!'
        ];
    }

    public function testCreatePlaylist()
    {
        $user = $this->json('POST', '/api/register', self::setUpValidUser());

        $token = $user->baseResponse->original['access_token'];

        $response = $this->json('POST', '/api/playlists', ['name' => $this->faker->name], ['HTTP_Authorization' => 'Bearer' . $token]);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'success' => true,
                'playlist' => [
                    'name',
                    'is_private',
                    'user_id',
                    'created_at',
                    'updated_at',
                    'id'
                ]
            ]);
    }

    public function testGetAllPlaylists()
    {
        $response = $this->json('GET', '/api/playlists');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                ['name']
            ]);
    }

    public function testGetPlaylistWithId()
    {
        $playlist = factory(Playlist::class)->create();

        $response = $this->json('GET', '/api/playlists/' . $playlist->id);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'playlist' => []
            ]);
    }

    public function testGetPlaylistWithNoneExistentId()
    {
        $id = 99999;
        $response = $this->json('GET', '/api/playlists/' . $id);

        $response
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Sorry, playlist with id ' . $id . ' cannot be found.'
            ]);
    }

    public function testAddSongToPlaylist()
    {
        $user = factory(User::class)->create();
        $login = $this->json('POST', '/api/login', ['email' => $user->email, 'password' => 'Testowe123!']);

        $token = $login->baseResponse->original['access_token'];

        $song = factory(Song::class)->create([
            'user_id' => $user->id
        ]);

        $playlist = factory(Playlist::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->json('POST', '/api/playlists/' . $playlist->id . '/' . $song->id, [], ['HTTP_Authorization' => 'Bearer' . $token]);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'success'
            ])
        ;
    }

    public function testAddSongThatDoesNotBelongToTheUser()
    {
        $user = factory(User::class)->create();
        $login = $this->json('POST', '/api/login', ['email' => $user->email, 'password' => 'Testowe123!']);
        $userId = Playlist::where('user_id', '!=', $user->id)->first();

        $token = $login->baseResponse->original['access_token'];

        $song = factory(Song::class)->create([
            'user_id' => $userId
        ]);

        $playlist = factory(Playlist::class)->create([
            'user_id' => $userId
        ]);

        $response = $this->json('POST', '/api/playlists/' . $playlist->id . '/' . $song->id, [], ['HTTP_Authorization' => 'Bearer' . $token]);
        $response
            ->assertStatus(403)
        ;
    }
}

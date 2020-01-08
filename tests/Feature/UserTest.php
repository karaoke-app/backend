<?php


namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use App\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    use WithFaker;

    public function testGetAllUsers()
    {
        $response = $this->json('GET', '/api/users');
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                [
                    'id',
                    'name',
                    'email'
                ]
            ])
        ;
    }

    public function testGetUserWithId()
    {
        $user = factory(User::class)->create();
        $response = $this->json('GET', '/api/users/' . $user->id);
        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => null,
                'avatar_original' => null,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ])
        ;
    }

    public function testGetUserWithNonexistentId()
    {
        do {
            $id = $this->faker->numberBetween(100);
        } while (User::where('id', '=', $id)->first() != null);

        $response = $this->json('GET', '/api/users/' . $id);
        $response
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Sorry, user with id ' . $id . ' cannot be found.'
            ])
        ;
    }
}

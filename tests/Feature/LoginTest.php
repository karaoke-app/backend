<?php declare(strict_types=1);

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use WithFaker;

    public function setUpValidUser()
    {
        $user = factory(User::class)->create();
        return [
            'email' => $user->email,
            'password' => 'Testowe123!',
        ];
    }

    public function testLogin()
    {
        $response = $this->json('POST', '/api/login', self::setUpValidUser());
        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in'
            ]);
    }

    public function testLoginWithBadCredentials()
    {
        $response = $this->json('POST', '/api/login', [
            'email' => $this->faker->email,
            'password' => '000'
        ]);
        $response->assertStatus(401)
            ->assertJson(['error' => 'Invalid email or password']);
    }
}

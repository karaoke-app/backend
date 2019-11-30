<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
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

    public function setUpInvalidUser()
    {
        return [
            'name' => '02',
            'email' => $this->faker->firstName,
            'password' => 'abc',
            'password_confirmation' => 'cba'
        ];
    }

    public function testCreateUser()
    {
        $response = $this->json('POST', '/api/register', self::setUpValidUser());
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }

    public function testCreateUserWithTheSameEmail()
    {
        $user = self::setUpValidUser();
        $this->json('POST', '/api/register', $user);

        $response = $this->json('POST', '/api/register', $user);
        $response->assertStatus(400);
        $response->assertJson(['message' => 'This email is already used in the system']);
    }

    public function testCreateUserWithBadCredentials()
    {
        $response = $this->json('POST', '/api/register', self::setUpInvalidUser());
        $response->assertStatus(400);
        $response->assertJson(['error' => 'Bad credentials']);
    }
}

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
        $response->assertStatus(201)
            ->assertJsonStructure([
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
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => [
                        'The email has already been taken.'
                    ]
                ]
            ]);
    }

    public function testCreateUserWithBadCredentials()
    {
        $response = $this->json('POST', '/api/register', self::setUpInvalidUser());
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => [
                        'The email must be a valid email address.'
                    ],
                    'password' => [
                        'The password must be at least 8 characters.',
                        'The password confirmation does not match.'
                    ]
                ]
            ]);
    }
}

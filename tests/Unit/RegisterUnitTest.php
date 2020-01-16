<?php declare(strict_types=1);


namespace Tests\Unit;


use Tests\TestCase;

class RegisterUnitTest extends TestCase
{
    /**
     * @dataProvider userDataProvider
     * @param $formInput
     * @param $formInputValue
     */
    public function testFormValidationWithBadCredentials($formInput, $formInputValue)
    {
        $response = $this->json('POST', '/api/register', [$formInput => $formInputValue]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($formInput);
    }


    public function userDataProvider()
    {
        return [
            'Name is required!' => ['name', ''],
            'Email is required!' => ['email', 'lalalpl'],
            'Password is required!' => ['password', 'X'],
        ];
    }

}

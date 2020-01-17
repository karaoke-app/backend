<?php declare(strict_types=1);

namespace Tests\Unit\RegisterTest;

use Tests\TestCase;

class RegisterUnitTest extends TestCase
{
    /**
     * @dataProvider userDataProvider
     * @param $inputData
     * @param $expectedErrors
     */
    public function testRegisterValidation($inputData, $expectedErrors)
    {
        $test = (new RegisterControllerMock())->testValidation($inputData);

        $a = $test->errors()->getMessages();

        $this->assertSame($expectedErrors, $test->errors()->getMessages());

    }

    public function userDataProvider()
    {
        return [
            'empty request' => [
                'data' => [],
                'expected errors' => [
                    'name' => [
                        'The name field is required.'
                    ],
                    'email' => [
                        'The email field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ]
                ]
            ],
            'Email is not email' => [
                'data' => [
                    'email' => [
                        'abc.pl'
                    ]
                ],
                'expected errors' => [
                    'name' => [
                        'The name field is required.'
                    ],
                    'email' => [
                        'The email must be a string.',
                        'The email must be a valid email address.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ]
                ]
            ],
            'Password and password_confirmation are not identical' => [
                'data' => [
                    'name' => 'Jan Kowalski',
                    'email' => 'abc@test.pl',
                    'password' => 'Testowe123!',
                    'password_confirmation' => 'Testowe'
                ],
                'expected errors' => [
                    'password' => [
                        'The password confirmation does not match.'
                    ]
                ]
            ],
            'Password does not contain 8 characters' => [
                'data' => [
                    'name' => 'Jan Kowalski',
                    'email' => 'abc@test.pl',
                    'password' => 'Tes',
                    'password_confirmation' => 'Tes'
                ],
                'expected errors' => [
                    'password' => [
                        'The password must be at least 8 characters.'
                    ]
                ]
            ]
        ];
    }

}

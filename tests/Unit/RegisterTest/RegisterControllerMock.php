<?php


namespace Tests\Unit\RegisterTest;


use App\Http\Controllers\Auth\RegisterController;

class RegisterControllerMock extends RegisterController
{
    public function testValidation(array $data)
    {
        return $this->validator($data);
    }
}

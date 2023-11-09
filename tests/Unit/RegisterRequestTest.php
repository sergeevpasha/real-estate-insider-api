<?php

use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RegisterRequestTest extends TestCase
{
    public function testGuestCanRegisterSuccessfully()
    {
        $container = new \Illuminate\Container\Container();

        $container->bind('Illuminate\Contracts\Validation\Factory', function ($app) {
            return Validator::getFacadeRoot();
        });

        $request = new RegisterRequest([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $request->setContainer($container);

        Auth::shouldReceive('guest')->andReturn(true);

        $isAuthorized = $request->authorize();
        $this->assertTrue($isAuthorized);

        $request->validateResolved();

        $this->assertTrue(true);
    }
}

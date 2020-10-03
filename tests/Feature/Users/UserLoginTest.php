<?php

namespace AppTests\Feature\Users;

use App\Models\User;
use AppTests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UserLoginTest extends TestCase
{
    use DatabaseMigrations;

    /** @test * */
    public function a_user_can_logIn()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->make();
        $user->save();

        $response = $this->post('/login', [
            'username' => $user->username,
            'email' => $user->email,
            'password' => $user->password,
            'password_confirmation' => $user->password,
        ])->shouldReturnJson()
            ->seeJsonStructure(['user', 'access_token'])
            ->seeJson(['user' => $user]);
    }
}

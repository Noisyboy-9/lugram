<?php

namespace AppTests\Feature\Users;

use App\Lugram\traits\tests\user\HasUserInteractions;
use AppTests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

class LoginTest extends TestCase
{
    use DatabaseMigrations, HasUserInteractions;

    /** @test * */
    public function a_user_with_already_created_account_can_logged_in()
    {
        $this->withoutExceptionHandling();
        $user = $this->createUser(['password' => 'password']);

        $response = $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->shouldReturnJson()
            ->seeJsonStructure(['access_token']);
    }
}

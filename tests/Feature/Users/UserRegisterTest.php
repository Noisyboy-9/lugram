<?php

namespace AppTests\Feature\Users;

use AppTests\TestCase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UserRegisterTest extends TestCase
{
    use DatabaseMigrations;

    /** @test * */
    public function a_user_can_be_registered()
    {
        $this->withoutExceptionHandling();
        $user = [
            'username' => 'noisyboy',
            'email' => 'sina.shariati@yahoo.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $this->post('/auth/register', $user)
            ->shouldReturnJson()
            ->seeJsonStructure(['user', 'created'])
            ->seeJson(['created' => true])
            ->seeJson([
                'user' => [
                    'email' => $user['email'],
                    'username' => $user['username'],
                ],
            ]);


        $this->seeInDatabase('users', [
            'username' => $user['username'],
            'email' => $user['email'],
        ]);
    }

    /** @test * */
    public function when_registering_a_user_its_password_is_hashed()
    {
        $user = [
            'username' => 'noisyboy',
            'email' => 'sina.shariati@yahoo.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $this->post('/auth/register', $user);

        $hashedPassword = DB::table('users')->first()->password;
        $password = Crypt::decrypt($hashedPassword);

        $this->assertEquals($user['password'], $password);
    }
}

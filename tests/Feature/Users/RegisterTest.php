<?php

namespace AppTests\Feature\Users;

use App\Lugram\traits\tests\user\HasUserInteractions;
use AppTests\TestCase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\DatabaseMigrations;

class RegisterTest extends TestCase
{
    use DatabaseMigrations, HasUserInteractions;

    /** @test * */
    public function a_user_can_be_registered()
    {
        $this->withoutExceptionHandling();
        $user = $this->makeUser();
        $user->password = 'password';

        $test = $this->post('/auth/register', [
            'username' => $user->username,
            'email' => $user->email,
            'password' => $user->password,
            'password_confirmation' => $user->password,
        ])
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
        $user = $this->createUser();

        $this->post('/auth/register', $user->toArray());

        $hashedPassword = DB::table('users')->pluck('password')[0];
        $password = Crypt::decrypt($hashedPassword);

        $this->assertEquals('password', $password);
    }

    /** @test * */
    public function a_user_name_is_required_to_create_a_user()
    {
        $user = $this->makeUser(['username' => null]); // bad username

        $this->post('/auth/register', $user->toArray())->assertResponseStatus(422);

        $this->notSeeInDatabase('users', [
            'username' => $user['username'],
            'email' => $user['email'],
        ]);
    }

    /** @test * */
    public function a_username_must_be_unique_to_create_a_user()
    {
        $user1 = $this->createUser(['username' => 'same_username']);
        $user2 = $this->makeUser(['username' => 'same_username']);

        $this->post('/auth/register', $user2->toArray())->assertResponseStatus(422);

        $this->notSeeInDatabase('users', [
            'username' => $user2['username'],
            'email' => $user2['email'],
        ]);
    }

    /** @test * */
    public function an_email_is_required_to_create_a_user()
    {
        $user = $this->makeUser(['email' => null]); //bad email

        $this->post('/auth/register', $user->toArray())->assertResponseStatus(422);

        $this->notSeeInDatabase('users', [
            'username' => $user['username'],
            'email' => $user['email'],
        ]);
    }

    /** @test * */
    public function an_email_must_be_unique_to_create_a_user()
    {
        $user1 = $this->createUser(['email' => 'same@email.com']);
        $user2 = $this->makeUser(['email' => 'same@email.com']);

        $this->post('/auth/register', $user2->toArray())->assertResponseStatus(422);

        $this->notSeeInDatabase('users', [
            'username' => $user2['username'],
            'email' => $user2['email'],
        ]);
    }
}

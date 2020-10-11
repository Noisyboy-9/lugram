<?php

namespace AppTests\Feature\Users;

use App\Lugram\traits\tests\user\HasUserInteractions;
use App\Models\User;
use AppTests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

class AccountManagementTest extends TestCase
{
    use DatabaseMigrations, HasUserInteractions;

    /** @test * */
    public function an_authenticated_user_can_update_his_account_credentials()
    {
        $this->withoutExceptionHandling();
        $oldUser = $this->login();
        $newUser = User::factory()->raw();
        $newUser['password_confirmation'] = $newUser['password'] = 'new password';

        $this->put($oldUser->path(), $newUser)
            ->shouldReturnJson()
            ->seeJson(['updated' => true])
            ->assertResponseOk();

        $this->seeInDatabase('users', [
            'username' => $newUser['username'],
            'email' => $newUser['email'],
        ]);

        $this->notSeeInDatabase('users', [
            'username' => $oldUser->username,
            'email' => $oldUser->email,
            'password' => $oldUser->password,
        ]);
    }

    /** @test * */
    public function a_user_must_be_authenticated_in_order_to_update_his_account_credentials()
    {
        $oldUser = $this->createUser();
        $newUser = User::factory()->raw();
        $newUser['password_confirmation'] = $newUser['password'];

        $this->put($oldUser->path(), $newUser)
            ->assertResponseStatus(401);

        $this->notSeeInDatabase('users', [
            'username' => $newUser['username'],
            'email' => $newUser['email'],
        ]);

        $this->seeInDatabase('users', [
            'username' => $oldUser->username,
            'email' => $oldUser->email,
        ]);
    }

    /** @test * */
    public function an_user_can_not_update_another_users_data()
    {
        $user1 = $this->login();
        $user2 = $this->createUser();
        $newUser = User::factory()->raw();
        $this->put($user2->path(), $newUser)
            ->shouldReturnJson()
            ->seeJson(['message' => 'Unauthorized'])
            ->assertResponseStatus(401);

        $this->seeInDatabase('users', [
            'username' => $user2->username,
            'email' => $user2->email,
        ]);

        $this->notSeeInDatabase('users', [
            'username' => $newUser['username'],
            'email' => $newUser['email'],
        ]);
    }

    /** @test * */
    public function username_must_be_unique_in_order_to_update_a_account_credentials()
    {
        $oldUser = $this->createUser(['username' => 'same_username']);
        $this->login($oldUser);

        $newUser = User::factory()->raw(['username' => 'same_username']);
        $newUser['password_confirmation'] = $newUser['password'];

        $this->put($oldUser->path(), $newUser)
            ->assertResponseStatus(422);

        $this->notSeeInDatabase('users', [
            'username' => $newUser['username'],
            'email' => $newUser['email'],
        ]);

        $this->seeInDatabase('users', [
            'username' => $oldUser->username,
            'email' => $oldUser->email,
        ]);
    }

    /** @test * */
    public function email_must_be_unique_in_order_to_update_a_account_credentials()
    {
        $oldUser = $this->createUser(['email' => 'same.email@example.com']);
        $this->login($oldUser);

        $newUser = User::factory()->raw(['email' => 'same.email@example.com']);
        $newUser['password_confirmation'] = $newUser['password'];

        $this->put($oldUser->path(), $newUser)
            ->assertResponseStatus(422);

        $this->notSeeInDatabase('users', [
            'username' => $newUser['username'],
            'email' => $newUser['email'],
        ]);

        $this->seeInDatabase('users', [
            'username' => $oldUser->username,
            'email' => $oldUser->email,
        ]);

    }

    /** @test * */
    public function password_must_be_confirmed_before_being_sent_for_credential_update()
    {
        $oldUser = $this->login();
        $newUser = User::factory()->raw();
        $newUser['password_confirmation'] = null;

        $this->put($oldUser->path(), $newUser)
            ->assertResponseStatus(422);

    }
}

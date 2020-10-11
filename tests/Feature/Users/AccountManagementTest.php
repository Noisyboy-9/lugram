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
    public function a_user_can_update_his_account_credentials()
    {
        $oldUser = $this->createUser();
        $newUser = User::factory()->raw();

        $this->assertTrue(true);
    }
}

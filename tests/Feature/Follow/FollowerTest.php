<?php

namespace AppTests\Feature\Follow;

use App\Lugram\traits\tests\user\HasUserInteractions;
use AppTests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

class FollowerTest extends TestCase
{
    use DatabaseMigrations, HasUserInteractions;

    /** @test * */
    public function a_authenticated_user_can_follow_another_user()
    {
        $jhon = $this->login();

    }
}

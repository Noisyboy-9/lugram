<?php

namespace AppTests\Feature\Follow;

use AppTests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

class FollowingTest extends TestCase
{
    use DatabaseMigrations;

    /** @test * */
    public function a_test()
    {
        $this->assertTrue(true);
    }
}

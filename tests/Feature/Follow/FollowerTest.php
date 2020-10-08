<?php

namespace AppTests\Feature\Follow;

use App\Lugram\traits\tests\user\HasUserInteractions;
use App\Models\User;
use AppTests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

class FollowerTest extends TestCase
{
    use DatabaseMigrations, HasUserInteractions;

    /** @test * */
    public function a_authenticated_user_can_follow_another_user()
    {
        $this->withoutExceptionHandling();

        $jhon = $this->login(
            User::factory()->make(['username' => 'jhon'])
        );

        $jane = $this->createUser(['username' => 'jane']);

        $this->post('/follow/' . $jane->id)
            ->shouldReturnJson()
            ->seeJson(['followed' => true])
            ->assertResponseStatus(201);

        $this->seeInDatabase('follows', [
            'follower_id' => $jhon->id,
            'following_id' => $jane->id,
        ]);

        $this->assertTrue($jhon->isFollowerOf($jane));
        $this->assertTrue($jane->isFollowingOf($jhon));
    }
}

<?php

namespace AppTests\Feature\Follow;

use App\Lugram\traits\tests\user\HasUserInteractions;
use AppTests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

class FollowTest extends TestCase
{
    use DatabaseMigrations, HasUserInteractions;

    /** @test * */
    public function a_authenticated_user_can_follow_another_user()
    {
        $this->withoutExceptionHandling();

        $jhon = $this->login();
        $jane = $this->createUser();

        $this->post('/requests/' . $jane->id)
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

    /** @test * */
    public function a_user_must_be_authenticated_to_another_user()
    {
        $jhon = $this->createUser();

        $this->post('/requests/' . $jhon->id)
            ->seeStatusCode(401);

        $this->notSeeInDatabase('follows', [
            'following_id' => $jhon->id,
        ]);

        $this->assertCount(0, $jhon->followings);
    }

    /** @test * */
    public function a_user_must_exist_in_order_for_another_user_to_follow()
    {
        $this->withoutExceptionHandling();
        $jhon = $this->login();

        $this->post('/requests/' . 12)
            ->seeJson(['message' => 'not found'])
            ->assertResponseStatus(404);// bad id : id does not relate to any known user

        $this->notSeeInDatabase('follows', [
            'follower_id' => $jhon->id,
        ]);
    }
}

<?php

namespace AppTests\Unit;

use App\Lugram\Managers\FollowRequestStatusManager;
use App\Lugram\traits\tests\user\HasUserInteractions;
use App\Models\Post;
use AppTests\TestCase;
use Illuminate\Support\Collection;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations, HasUserInteractions;

    /** @test * */
    public function it_may_have_many_posts()
    {
        $user = $this->login();

        $post = Post::factory()->make();
        $post->save();

        $this->assertInstanceOf(Collection::class, $user->posts);
        $this->assertEquals($post->id, $user->posts[0]->id);
    }

    /** @test * */
    public function it_may_have_many_follow_requests()
    {
        $jhon = $this->login();
        $jane = $this->createUser();

        $jhon->makeFollowRequest($jane);

        $this->assertInstanceOf(Collection::class, $jane->requests());
        $this->assertCount(1, $jane->requests());

    }

    /** @test * */
    public function it_can_make_a_follow_request_to_another_user()
    {
        $jhon = $this->login();
        $jane = $this->createUser();

        $jhon->makeFollowRequest($jane);

        $this->seeInDatabase('follows', [
            'follower_id' => $jhon->id,
            'following_id' => $jane->id,
            'status' => FollowRequestStatusManager::AWAITING_FOR_RESPONSE,
        ]);
    }

    /** @test * */
    public function it_does_not_consider_a_user_followed_before_its_request_being_accepted()
    {
        $jhon = $this->login();
        $jane = $this->createUser();

        $jhon->makeFollowRequest($jane);

        $this->assertCount(0, $jane->followers);
        $this->assertCount(0, $jhon->followings);
    }

    /** @test * */
    public function it_may_have_many_followings()
    {
        $jhon = $this->login();
        $jane = $this->createUser();

        $jhon->follow($jane);
        $this->assertInstanceOf(Collection::class, $jhon->followings);
        $this->assertTrue($jhon->followings->contains($jane));
    }

    /** @test * */
    public function it_may_have_many_followers()
    {
        $jhon = $this->login();
        $jane = $this->createUser();

        $jhon->follow($jane);

        $this->assertInstanceOf(Collection::class, $jane->followers);
        $this->assertTrue($jane->followers->contains($jhon));
    }

    /** @test * */
    public function it_can_follow_another_user()
    {
        $this->withoutExceptionHandling();
        $jhon = $this->login();
        $jane = $this->createUser();

        $jhon->follow($jane);
        $this->assertTrue($jhon->followings->contains($jane));
        $this->assertTrue($jane->followers->contains($jhon));
    }

    /** @test * */
    public function it_can_know_if_is_follower_of_another_user()
    {
        $jhon = $this->login();
        $jane = $this->createUser();

        $jhon->follow($jane);

        $this->assertTrue($jhon->isFollowerOf($jane));
    }

    /** @test * */
    public function it_can_know_if_it_has_another_user_as_following()
    {
        $jhon = $this->login();
        $jane = $this->createUser();

        $jhon->follow($jane);

        $this->assertTrue($jane->isFollowingOf($jhon));
    }
}

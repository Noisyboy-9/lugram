<?php

namespace AppTests\Unit;

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


}

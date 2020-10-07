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
    public function it_has_many_posts()
    {
        $user = $this->login();

        $post = Post::factory()->make();
        $post->save();

        $this->assertInstanceOf(Collection::class, $user->posts);
        $this->assertEquals($post->id, $user->posts[0]->id);
    }
}

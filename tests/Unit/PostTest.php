<?php

namespace AppTests\Unit;

use App\Lugram\traits\tests\user\HasUserInteractions;
use App\Models\Post;
use App\Models\User;
use AppTests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Lumen\Testing\DatabaseMigrations;

class PostTest extends TestCase
{
    use DatabaseMigrations, HasUserInteractions;

    /** @test * */
    public function it_knows_its_user()
    {
        $this->login();

        $post = Post::factory()->create();

        $this->assertInstanceOf(User::class, $post->owner);
    }
}

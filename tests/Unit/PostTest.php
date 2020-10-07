<?php

namespace AppTests\Unit;

use App\Lugram\traits\tests\user\HasUserInteractions;
use App\Models\Post;
use App\Models\User;
use AppTests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

class PostTest extends TestCase
{
    use DatabaseMigrations, HasUserInteractions;

    /**
     * create posts and persist them to database
     *
     * @param array $attributes
     * @param int   $count
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    private function createPost(array $attributes = [], int $count = 1)
    {
        $posts = Post::factory()->count($count)->make($attributes);

        $posts->each(function ($post) {
            $post->save();
        });

        if ($count === 1) {
            return $posts[0];
        }

        return $posts;
    }

    /** @test * */
    public function it_knows_its_user()
    {
        $this->login();

        $post = Post::factory()->create();

        $this->assertInstanceOf(User::class, $post->owner);
    }

    /** @test * */
    public function it_knows_its_own_path()
    {
        $this->login();
        $post = $this->createPost();

        $this->assertEquals('/posts/' . $post->id, $post->path());
    }
}

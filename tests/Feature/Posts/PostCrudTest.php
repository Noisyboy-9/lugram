<?php

namespace AppTests\Feature\Posts;

use App\Lugram\traits\tests\user\HasUserInteractions;
use App\Models\Post;
use AppTests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

class PostCrudTest extends TestCase
{
    use DatabaseMigrations, HasUserInteractions;

    /** @test * */
    public function an_authenticated_user_can_only_see_his_own_posts()
    {
        $user1 = $this->login();
        $user2 = $this->createUser();

        $post = $this->createPost(['user_id' => $user2['id']]);

        $this->get('/posts')
            ->shouldReturnJson()
            ->seeJsonStructure(['posts'])
            ->dontSeeJson([
                'posts' => [
                    $post->toArray(),
                ],
            ]);
    }

    /** @test * */
    public function an_authenticated_user_can_fetch_his_posts()
    {
        $this->withoutExceptionHandling();
        $user = $this->login();
        $post = $this->createPost();

        $this->get('/posts')
            ->shouldReturnJson()
            ->seeJsonStructure(['posts'])
            ->assertResponseOk();
    }

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
}

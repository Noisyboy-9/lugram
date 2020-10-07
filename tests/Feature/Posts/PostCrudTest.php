<?php

namespace AppTests\Feature\Posts;

use App\Lugram\traits\tests\user\HasUserInteractions;
use App\Models\Post;
use AppTests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

class PostCrudTest extends TestCase
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
    public function an_authenticated_user_can_delete_his_own_post()
    {
        $this->withoutExceptionHandling();

        $user = $this->login();
        $post = $this->createPost();

        $this->delete($post->path())
            ->shouldReturnJson()
            ->seeJson(['deleted' => true])
            ->seeJson(['message' => 'Post deleted successfully'])
            ->assertResponseOk();

        $this->notSeeInDatabase('posts', $post->toArray());
    }

    /** @test * */
    public function a_user_must_be_logged_in_to_delete_his_own_post()
    {
        $post = $this->createPost(['user_id' => 1]);
        $this->delete($post->path())->assertResponseStatus(401);
    }

    /** @test * */
    public function a_user_can_only_delete_his_own_post()
    {
        $user1 = $this->login();
        $user2 = $this->createUser();
        $post = $this->createPost(['user_id' => $user2['id']]);

        $this->delete($post->path())->assertResponseStatus(401);
        $this->notSeeInDatabase('posts', $post->toArray());
    }
}

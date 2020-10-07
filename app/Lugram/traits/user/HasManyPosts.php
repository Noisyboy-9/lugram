<?php


namespace App\Lugram\traits\user;


use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait HasManyPosts
{

    /**
     * get all posts related to the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * check if user is owner of a model
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return bool
     */
    public function isOwnerOf(Model $model)
    {
        return $model->owner->is($this);
    }
}

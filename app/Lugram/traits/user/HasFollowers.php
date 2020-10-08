<?php


namespace App\Lugram\traits\user;


use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasFollowers
{

    /**
     * follow another user
     *
     * @param \App\Models\User $user
     */
    public function follow(User $user): void
    {
        $this->followings()->attach($user);
    }

    /**
     * return users that have followed model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id');

    }

    /**
     * check if model is follower of given user
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function isFollowerOf(User $user): bool
    {
        return $this->followings->contains($user);
    }
}

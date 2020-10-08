<?php


namespace App\Lugram\traits\user;


use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasFollowings
{

    /**
     * check if given user is followed by given user
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function isFollowingOf(User $user): bool
    {
        return $this->followers->contains($user);
    }

    /**
     * return users that model has followed
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followings(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id');

    }
}

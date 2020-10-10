<?php


namespace App\Lugram\traits\user;


use App\Lugram\Managers\FollowRequestStatusManager;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasFollowers
{
    /**
     * return users that have followed model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
            ->wherePivot('status', FollowRequestStatusManager::ACCEPTED);

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

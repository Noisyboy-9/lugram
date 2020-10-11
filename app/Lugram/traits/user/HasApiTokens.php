<?php


namespace App\Lugram\traits\user;


use App\Lugram\Managers\FollowRequestStatusManager;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

trait HasApiTokens
{

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * get all model's follow requests
     *
     * @return \Illuminate\Support\Collection
     */
    public function requests()
    {
        return DB::table('follows')
            ->where('following_id', $this->id)
            ->where('status', FollowRequestStatusManager::AWAITING_FOR_RESPONSE)
            ->get();
    }

    /**
     * decline follow request from given user
     *
     * @param \App\Models\User $user
     */
    public function declineRequest(User $user)
    {
        DB::table('follows')
            ->where('follower_id', $user->id)
            ->where('following_id', $this->id)
            ->where('status', FollowRequestStatusManager::AWAITING_FOR_RESPONSE)
            ->update(['status' => FollowRequestStatusManager::DECLINED]);
    }

    /**
     * save a follow request for the given user in the database
     *
     * @param \App\Models\User $user
     */
    public function makeFollowRequest(User $user)
    {
        DB::table('follows')->insert([
            'follower_id' => $this->id,
            'following_id' => $user->id,
            'status' => FollowRequestStatusManager::AWAITING_FOR_RESPONSE,
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ]);
    }

    /**
     * accept following request from given user
     *
     * @param \App\Models\User $user
     */
    public function acceptRequest(User $user)
    {
        DB::table('follows')
            ->where('follower_id', $user->id)
            ->where('following_id', $this->id)
            ->update(['status' => FollowRequestStatusManager::ACCEPTED]);
    }

    /**
     * check if model has accepted following request of given user
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function hasAcceptedRequestOf(User $user)
    {
        return DB::table('follows')
            ->where('follower_id', $user->id)
            ->where('following_id', $this->id)
            ->where('status', FollowRequestStatusManager::ACCEPTED)
            ->exists();
    }

    /**
     * check if model has any awaiting follow request from given user
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function hasAwaitingRequestFrom(User $user)
    {
        return DB::table('follows')
            ->where('follower_id', $user->id)
            ->where('following_id', $this->id)
            ->where('status', FollowRequestStatusManager::AWAITING_FOR_RESPONSE)
            ->exists();
    }
}

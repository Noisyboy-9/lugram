<?php


namespace App\Lugram\traits\user;


use App\Lugram\Managers\FollowRequestStatusManager;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

trait HasFollowRequests
{

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
     * hash the password before storing it to database
     *
     * @param $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Crypt::encrypt($password);
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
    public function hasAwaitingRequestFrom(User $user): bool
    {
        return DB::table('follows')
            ->where('follower_id', $user->id)
            ->where('following_id', $this->id)
            ->where('status', FollowRequestStatusManager::AWAITING_FOR_RESPONSE)
            ->exists();
    }
}

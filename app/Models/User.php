<?php

namespace App\Models;

use App\Lugram\Managers\FollowRequestStatusManager;
use App\Lugram\traits\user\HasApiTokens;
use App\Lugram\traits\user\HasFollowers;
use App\Lugram\traits\user\HasFollowings;
use App\Lugram\traits\user\HasManyPosts;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @method static findOrFail($userId)
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable,
        Authorizable,
        HasFactory,
        HasApiTokens,
        HasManyPosts,
        HasFollowers,
        HasFollowings;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'status',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * hash the password before storing it to database
     *
     * @param $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Crypt::encrypt($password);
    }

    public function requests()
    {
        return DB::table('follows')
            ->where('following_id', $this->id)
            ->where('status', FollowRequestStatusManager::AWAITING_FOR_RESPONSE)
            ->get();
    }

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

    public function acceptRequest(User $user)
    {
        DB::table('follows')
            ->where('follower_id', $user->id)
            ->where('following_id', $this->id)
            ->update(['status' => FollowRequestStatusManager::ACCEPTED]);
    }

    public function hasAcceptedRequestOf(User $user)
    {
        return DB::table('follows')
            ->where('follower_id', $user->id)
            ->where('following_id', $this->id)
            ->where('status', FollowRequestStatusManager::ACCEPTED)
            ->exists();
    }
}

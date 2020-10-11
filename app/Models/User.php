<?php

namespace App\Models;

use App\Lugram\Managers\FollowRequestStatusManager;
use App\Lugram\traits\user\HasApiTokens;
use App\Lugram\traits\user\HasFollowers;
use App\Lugram\traits\user\HasFollowings;
use App\Lugram\traits\user\HasFollowRequests;
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
        HasFollowRequests,
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

}

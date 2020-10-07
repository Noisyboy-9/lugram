<?php


namespace App\Lugram\traits\tests\user;


use App\Models\User;

trait HasUserInteractions
{

    /**
     * create users but don't persist them to database
     *
     * @param array $attributes
     * @param int   $count
     *
     * @note this function does not persist data to databsae
     * @return array|mixed
     */
    protected function makeUser(array $attributes = [], int $count = 1)
    {
        $users = User::factory()->count($count)->make($attributes);

        foreach ($users as $user) {
            $user['password_confirmation'] = $user['password'];
        }

        if ($count === 1) return $users->toArray()[0];

        return $users->toArray();
    }

    /**
     * create users and persist them to the  database
     *
     * @param array $attributes
     * @param int   $count
     *
     * @note this function persist data to database
     *
     * @return array|mixed
     */
    protected function createUser(array $attributes = [], int $count = 1)
    {
        $users = User::factory()->count($count)->make($attributes);
        foreach ($users as $user) $user->save();

        if ($count === 1) return $users->toArray()[0];

        return $users->toArray();
    }

    /**
     * login the user
     *
     * @param null $user
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null
     */
    protected function login($user = null)
    {
        if (! $user) $user = User::factory()->make();

        $user->save();
        $this->actingAs($user);

        return $user;
    }
}

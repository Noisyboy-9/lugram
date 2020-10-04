<?php

namespace AppTests\Feature\Users;

use App\Models\User;
use AppTests\TestCase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UserRegisterTest extends TestCase
{
    use DatabaseMigrations;

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
    private function createUser(array $attributes = [], int $count = 1)
    {
        $users = User::factory()->count($count)->make($attributes);
        foreach ($users as $user) $user->save();

        if ($count === 1) return $users->toArray()[0];

        return $users->toArray();
    }


    /**
     * create users but don't persist them to database
     *
     * @param array $attributes
     * @param int   $count
     *
     * @note this function does not persist data to databsae
     * @return array|mixed
     */
    private function makeUser(array $attributes = [], int $count = 1)
    {
        $users = User::factory()->count($count)->make($attributes);

        foreach ($users as $user) {
            $user['password_confirmation'] = $user['password'];
        }

        if ($count === 1) return $users->toArray()[0];

        return $users->toArray();
    }

    /** @test * */
    public function a_user_can_be_registered()
    {
        $user = $this->makeUser();

        $this->post('/auth/register', $user)
            ->shouldReturnJson()
            ->seeJsonStructure(['user', 'created'])
            ->seeJson(['created' => true])
            ->seeJson([
                'user' => [
                    'email' => $user['email'],
                    'username' => $user['username'],
                ],
            ]);


        $this->seeInDatabase('users', [
            'username' => $user['username'],
            'email' => $user['email'],
        ]);
    }

    /** @test * */
    public function when_registering_a_user_its_password_is_hashed()
    {
        $user = $this->createUser();

        $this->post('/auth/register', $user);

        $hashedPassword = DB::table('users')->first()->password;
        $password = Crypt::decrypt($hashedPassword);

        $this->assertEquals('password', $password);
    }


}

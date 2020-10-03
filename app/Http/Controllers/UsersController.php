<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function login(Request $request)
    {
        $user = $this->validateUser($request);

        if ($this->isAlreadyMember($user)) {
            $data = $this->generateData($user);
            dd($data);
            return response()->json([
                'user' => $user,
                'access_token' => $this->generateToken($data),
            ]);
        }

        return response()->json(['message' => 'Authentication failed! No such email and password combination'], 401);

    }

    private function validateUser(Request $request)
    {
        return $this->validate($request, [
            'username' => 'required|string|min:4',
            'email' => 'required|email:rfc',
            'password' => 'required|confirmed|min:5',
        ]);
    }

    private function isAlreadyMember(array $user)
    {
        $hashedPassword = $user['password'];

        return DB::table('users')->where('email', $user['email'])->where('password', $hashedPassword)->exists();
    }

    private function generateData(array $user)
    {
        return [
            'grant_type' => env('PASSPORT_GRANT_TYPE'),
            'client_id' => env('PASSPORT_PASSWORD_GRANT_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_GRANT_SECRET'),
            'username' => $user['email'],
            'password' => $user['password'],
        ];
    }

    private function generateToken(array $data)
    {
        $request = app('request')->create('/auth/token', 'POST', $data);
        $response = app()->dispatch($request);
    }


}

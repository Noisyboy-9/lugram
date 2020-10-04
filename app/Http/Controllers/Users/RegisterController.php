<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;


class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $user = $this->validateUser($request);

        User::create([
            'username' => $user['username'],
            'email' => $user['email'],
            'password' => $this->hashPassword($user['password']),
        ]);

        return response()->json([
            'created' => true,
            'user' => [
                'username' => $user['username'],
                'email' => $user['email'],
            ],
        ], 201);
    }

    private function validateUser(Request $request)
    {

        return $this->validate($request, [
            'username' => 'required|string|unique:users|min:3',
            'email' => 'required|email:rfc|unique:users',
            'password' => 'required|confirmed|string|min:5',
        ]);
    }


    private function hashPassword(string $password)
    {
        return Crypt::encrypt($password);
    }
}

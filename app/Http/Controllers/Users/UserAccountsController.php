<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;


class UserAccountsController extends Controller
{
    /**
     * store a user to the database
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = $this->validateRegisterRequest($request);

        User::create([
            'username' => $user['username'],
            'email' => $user['email'],
            'password' => $user['password'],
        ]);

        return response()->json([
            'created' => true,
            'user' => [
                'username' => $user['username'],
                'email' => $user['email'],
            ],
        ], 201);
    }

    /**
     * validate the register request data
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRegisterRequest(Request $request)
    {

        return $this->validate($request, [
            'username' => 'required|string|unique:users|min:3',
            'email' => 'required|email:rfc|unique:users',
            'password' => 'required|confirmed|string|min:5',
        ]);
    }

}

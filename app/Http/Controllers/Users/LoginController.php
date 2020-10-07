<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /**
     * authenticate user and return a token
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $attributes = $this->validateLogin($request);

        if ($this->isInputEmailStored($request) && $this->isInputPasswordMatch($request)) {
            $token = auth()->tokenById($this->getUserId($request));

            return $this->respondWithToken($token);
        }

        return response()->json(['message' => 'Email and password combination does not match! Please try again.'], 401);
    }

    /**
     * validate the input data for logging in
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateLogin(Request $request)
    {
        return $this->validate($request, [
            'email' => 'required|email:rfc',
            'password' => 'required',
        ]);
    }

    /**
     * check if the input email coming from request is stored in the database
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    private function isInputEmailStored(Request $request)
    {
        return DB::table('users')->where('email', $request['email'])->exists();
    }

    /**
     * check if the password and email combination actually match with data stored in database
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    private function isInputPasswordMatch(Request $request)
    {
        $hashedPassword = DB::table('users')->where('email', $request['email'])->pluck('password')[0];

        $password = Crypt::decrypt($hashedPassword);

        return $password === $request['password'];
    }

    /**
     * send response containing token and token type
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function respondWithToken(string $token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ], 200);
    }

    /**
     * Get newly successfully logged in user's id
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    private function getUserId(Request $request)
    {
        return DB::table('users')->where('email', $request['email'])->pluck('id')[0];
    }
}

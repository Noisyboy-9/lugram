<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        $attributes = $this->validateLogin($request);

        if ($this->isInputEmailStored($request) && $this->isInputPasswordMatch($request)) {
            $token = auth()->tokenById($this->getUserId($request));

            return $this->respondWithToken($token);
        }

        return response()->json(['message' => 'Email and password combination does not match! Please try again.'], 401);
    }

    private function validateLogin(Request $request)
    {
        return $this->validate($request, [
            'email' => 'required|email:rfc',
            'password' => 'required',
        ]);
    }

    private function isInputEmailStored(Request $request)
    {
        return DB::table('users')->where('email', $request['email'])->exists();
    }

    private function isInputPasswordMatch(Request $request)
    {
        $hashedPassword = DB::table('users')->where('email', $request['email'])->pluck('password')[0];

        $password = Crypt::decrypt($hashedPassword);

        return $password === $request['password'];
    }

    private function respondWithToken(string $token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ], 200);
    }

    private function getUserId(Request $request)
    {
        return DB::table('users')->where('email', $request['email'])->pluck('id')[0];
    }
}

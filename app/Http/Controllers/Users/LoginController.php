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

        if ($this->isMember($attributes) && $this->isInputPasswordCorrect($attributes)) {
            $id = DB::table('users')->where('email', $attributes['email'])->pluck('id')[0];

            return $this->respondWithToken(auth()->tokenById($id));
        }

        return response()->json(['message' => 'Email and password combination does not match out records!'], 401);
    }

    private function validateLogin(Request $request)
    {
        return $this->validate($request, [
            'email' => 'required|email:rfc',
            'password' => 'required',
        ]);
    }

    private function isMember(array $attributes): bool
    {
        return DB::table('users')
            ->where('email', $attributes['email'])
            ->exists();
    }

    private function isInputPasswordCorrect(array $attributes)
    {
        $hashedPassword = DB::table('users')->pluck('password');

        return $attributes['password'] === Crypt::decrypt($hashedPassword);
    }

    private function respondWithToken(string $token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}

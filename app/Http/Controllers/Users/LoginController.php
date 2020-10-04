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
        $hash = DB::table('users')->pluck('password');

        dd(auth()->attempt($attributes));
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


}

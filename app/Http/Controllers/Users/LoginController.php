<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        $attributes = $this->validateLogin($request);

        dd(Auth::attempt($attributes)); // why this returns false everytime
    }

    private function validateLogin(Request $request)
    {
        return $this->validate($request, [
            'email' => 'required|email:rfc',
            'password' => 'required',
        ]);
    }
}

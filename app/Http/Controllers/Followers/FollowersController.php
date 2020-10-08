<?php

namespace App\Http\Controllers\Followers;

use App\Http\Controllers\Controller;
use App\Models\User;

class FollowersController extends Controller
{
    public function store($userId)
    {
        $user = User::findOrFail($userId);

        auth()->user()->follow($user);

        return response()->json(['followed' => true], 201);
    }
}

<?php

namespace App\Http\Controllers\Follows;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FollowRequestsController extends Controller
{
    public function store($userId)
    {
        try {
            $user = User::findOrFail($userId);

        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'not found'], 404);
        }


        if ($user->hasAwaitingRequestFrom(auth()->user())) {
            return response()->json(['message' => 'same request have already been sent to user.'], 406);
        }
        auth()->user()->makeFollowRequest($user);

        return response()->json(['requested' => true], 201);
    }
}

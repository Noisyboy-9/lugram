<?php

namespace App\Http\Controllers\Follows;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AcceptFollowRequestsController extends Controller
{
    public function update($userId)
    {
        try {
            $user = User::findOrFail($userId);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'not found'], 404);
        }

        auth()->user()->acceptRequest($user);

        return response()->json(['accepted' => true], 200);
    }
}
